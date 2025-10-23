<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Controllers\OrderController;
use PHPUnit\Framework\Attributes\Test;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user dengan role yang valid
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'role' => 'customer'
        ]);

        // Create product manual tanpa factory
        $this->product = Product::create([
            'nama' => 'Test Product',
            'deskripsi' => 'Test Description',
            'harga' => 100000,
            'stok' => 10,
            'kategori' => 'test',
            'gambar' => 'test.jpg'
        ]);

        Auth::login($this->user);
    }

    #[Test]
    public function it_can_add_product_to_cart()
    {
        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 2
        ]);

        $controller = new OrderController();
        $response = $controller->addToCart($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'cart'
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'qty' => 2
        ]);
    }

    #[Test]
    public function it_fails_when_qty_exceeds_stock()
    {
        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 15
        ]);

        $controller = new OrderController();
        $response = $controller->addToCart($request);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    #[Test]
    public function it_updates_existing_cart_item()
    {
        // Create order dan item terlebih dahulu
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'qty' => 1,
            'harga' => $this->product->harga
        ]);

        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 2
        ]);

        $controller = new OrderController();
        $response = $controller->addToCart($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'qty' => 3
        ]);
    }

    #[Test]
    public function it_redirects_to_checkout_page()
    {
        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 2
        ]);

        $controller = new OrderController();
        $response = $controller->checkoutNow($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('checkout.page'), $response->getTargetUrl());
        
        // Check session data
        $this->assertNotNull(Session::get('checkout_item'));
    }

    #[Test]
    public function it_fails_checkout_when_qty_exceeds_stock()
    {
        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 15
        ]);

        $controller = new OrderController();
        $response = $controller->checkoutNow($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertTrue(Session::has('error'));
    }

    #[Test]
    public function it_redirects_when_no_checkout_session()
    {
        // Pastikan session kosong
        Session::forget('checkout_item');

        // Test method controller langsung
        $controller = new OrderController();
        $response = $controller->showCheckoutPage();

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('katalog'), $response->getTargetUrl());
        
        // Verifikasi session error message
        $this->assertTrue(Session::has('error'));
    }

    #[Test]
    public function it_calculates_order_total_correctly()
    {
        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 3
        ]);

        $controller = new OrderController();
        $controller->addToCart($request);

        $order = Order::where('user_id', $this->user->id)->first();
        $expectedTotal = 3 * $this->product->harga;

        $this->assertEquals($expectedTotal, $order->total);
    }

    #[Test]
    public function it_creates_new_cart_when_none_exists()
    {
        $this->assertDatabaseMissing('orders', [
            'user_id' => $this->user->id,
            'status' => 'cart'
        ]);

        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 1
        ]);

        $controller = new OrderController();
        $controller->addToCart($request);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'cart'
        ]);
    }

    #[Test]
    public function it_uses_existing_cart_when_available()
    {
        // Buat cart terlebih dahulu
        $existingOrder = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0
        ]);

        $request = new \Illuminate\Http\Request([
            'product_id' => $this->product->id,
            'qty' => 1
        ]);

        $controller = new OrderController();
        $controller->addToCart($request);

        // Pastikan tidak ada order baru yang dibuat
        $ordersCount = Order::where('user_id', $this->user->id)
                           ->where('status', 'cart')
                           ->count();
        $this->assertEquals(1, $ordersCount);
        $this->assertEquals($existingOrder->id, Order::first()->id);
    }
}