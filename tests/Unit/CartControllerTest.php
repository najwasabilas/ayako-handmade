<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\CartController;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductImage;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected CartController $controller;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // controller instance
        $this->controller = new CartController();

        // create user explicitly to avoid factory/HasFactory issues;
        $this->user = User::create([
            'name' => 'Unit User',
            'email' => 'unituser@example.test',
            'password' => bcrypt('password'),
            // pastikan role valid sesuai constraint DB; ganti 'customer' jika DB memerlukan itu
            'role' => 'customer',
        ]);

        // authenticate user for controller methods that depend on Auth::id()
        $this->actingAs($this->user);
    }

    /* ---------------------------
       INDEX (view cart)
       --------------------------- */

    public function test_index_returns_view_with_items_when_cart_exists(): void
    {
        // make product and order + order item (create directly, not with factories)
        $product = Product::create([
            'nama' => 'Totebag Songket',
            'deskripsi' => 'Deskripsi',
            'harga' => 50000,
            'stok' => 10,
            'kategori' => 'Fashion',
        ]);

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0,
            'alamat' => 'Jl. Test No.1',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 2,
            'harga' => $product->harga,
        ]);

        $response = $this->controller->index();

        $this->assertInstanceOf(View::class, $response);

        $data = $response->getData();
        $this->assertArrayHasKey('items', $data);
        $this->assertCount(1, $data['items']);
        $this->assertEquals($product->id, $data['items'][0]->product_id);
    }

    public function test_index_returns_empty_collection_when_no_cart_exists(): void
    {
        // Ensure there is no order with status 'cart' for this user
        $response = $this->controller->index();

        $this->assertInstanceOf(View::class, $response);

        $data = $response->getData();
        $this->assertArrayHasKey('items', $data);
        $this->assertCount(0, $data['items']);
    }

    /* ---------------------------
       updateQuantity
       --------------------------- */

    public function test_updateQuantity_successfully_updates_qty(): void
    {
        $product = Product::create([
            'nama' => 'Totebag Songket',
            'deskripsi' => 'Desc',
            'harga' => 30000,
            'stok' => 10,
            'kategori' => 'Fashion',
        ]);

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0,
            'alamat' => 'Alamat',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 1,
            'harga' => $product->harga,
        ]);

        $request = Request::create('/cart/update', 'POST', [
            'item_id' => $item->id,
            'qty' => 5,
        ]);

        $response = $this->controller->updateQuantity($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'qty' => 5,
        ]);

        // total updated on order
        $order->refresh();
        $expectedTotal = $order->items->sum(fn($i) => $i->qty * $i->harga);
        $this->assertEquals($expectedTotal, $order->total);
    }

    public function test_updateQuantity_fails_when_qty_exceeds_stock(): void
    {
        $product = Product::create([
            'nama' => 'Totebag Songket',
            'deskripsi' => 'Desc',
            'harga' => 40000,
            'stok' => 2,
            'kategori' => 'Fashion',
        ]);

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0,
            'alamat' => 'Alamat',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 1,
            'harga' => $product->harga,
        ]);

        $request = Request::create('/cart/update', 'POST', [
            'item_id' => $item->id,
            'qty' => 5, // melebihi stok 2
        ]);

        $response = $this->controller->updateQuantity($request);

        // controller returns 400 with json error
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['error' => 'Jumlah melebihi stok tersedia'], $response->getData(true));
    }

    public function test_updateQuantity_validation_fails_when_input_invalid(): void
    {
        $this->expectException(ValidationException::class);

        // missing item_id
        $request = Request::create('/cart/update', 'POST', [
            'qty' => 2,
        ]);

        $this->controller->updateQuantity($request);
    }

    /* ---------------------------
       remove
       --------------------------- */

    public function test_remove_deletes_item_and_updates_order_total(): void
    {
        $product = Product::create([
            'nama' => 'Totebag Songket',
            'deskripsi' => 'Desc',
            'harga' => 20000,
            'stok' => 10,
            'kategori' => 'Fashion',
        ]);

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0,
            'alamat' => 'Alamat',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 2,
            'harga' => $product->harga,
        ]);

        $request = Request::create('/cart/remove', 'POST', [
            'id' => $item->id,
        ]);

        $response = $this->controller->remove($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));

        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
    }

    public function test_remove_validation_fails_for_invalid_id(): void
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/cart/remove', 'POST', [
            'id' => 999999, // does not exist
        ]);

        $this->controller->remove($request);
    }

    /* ---------------------------
       checkoutSelected
       --------------------------- */

    public function test_checkoutSelected_saves_items_to_session_and_redirects(): void
    {
        $product = Product::create([
            'nama' => 'Totebag Songket',
            'deskripsi' => 'Desc',
            'harga' => 50000,
            'stok' => 10,
            'kategori' => 'Fashion',
        ]);

        // ensure product has image so controller can access images->first()->gambar
        if (class_exists(ProductImage::class)) {
            ProductImage::create([
                'product_id' => $product->id,
                'gambar' => 'img-test.jpg',
            ]);
        }

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0,
            'alamat' => 'Alamat',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 2,
            'harga' => $product->harga,
        ]);

        $request = Request::create('/cart/checkout', 'POST', [
            'selected_items' => [$item->id],
        ]);

        // call controller
        $response = $this->controller->checkoutSelected($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('checkout.page'), $response->getTargetUrl());

        // session data must be set
        $checkoutItems = session('checkout_items');
        $this->assertIsArray($checkoutItems);
        $this->assertCount(1, $checkoutItems);
        $this->assertEquals($item->id, $checkoutItems[0]['id']);
        $this->assertEquals($product->nama, $checkoutItems[0]['nama']);
    }

    public function test_checkoutSelected_fails_when_selected_items_empty(): void
    {
        // when array empty, controller redirects back with error flash
        $request = Request::create('/cart/checkout', 'POST', [
            'selected_items' => [],
        ]);

        $response = $this->controller->checkoutSelected($request);

        // redirect back → session should have flash 'error'
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(session()->has('error'));
        $this->assertEquals('Tidak ada produk yang dipilih untuk checkout.', session('error'));
    }

   public function test_checkoutSelected_validation_fails_when_missing(): void
{
    $request = Request::create('/cart/checkout', 'POST', [
        // missing selected_items
    ]);

    $response = $this->controller->checkoutSelected($request);

    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertTrue(session()->has('error'));
    $this->assertEquals('Tidak ada produk yang dipilih untuk checkout.', session('error'));
}}