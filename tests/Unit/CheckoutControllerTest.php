<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product1, $product2;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup user dan produk untuk semua test
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        $this->product1 = Product::create([
            'nama' => 'Product 1', 'harga' => 10000, 'stok' => 10, 'deskripsi' => 'Test 1'
        ]);
        
        $this->product2 = Product::create([
            'nama' => 'Product 2', 'harga' => 15000, 'stok' => 5, 'deskripsi' => 'Test 2'
        ]);
    }

    // Helper untuk membuat checkout items
    protected function createCheckoutItems($products = null)
    {
        $defaultProducts = [
            ['product' => $this->product1, 'qty' => 2],
            ['product' => $this->product2, 'qty' => 1]
        ];

        $products = $products ?? $defaultProducts;

        return array_map(function ($item) {
            return [
                'product_id' => $item['product']->id,
                'nama' => $item['product']->nama,
                'harga' => $item['product']->harga,
                'qty' => $item['qty'],
                'image' => 'default.jpg' // Required oleh view
            ];
        }, $products);
    }

    // Helper untuk data order
    protected function getOrderData()
    {
        return [
            'nama' => 'John Doe',
            'telepon' => '08123456789',
            'alamat_lengkap' => 'Jl. Test No. 123'
        ];
    }

    //Test 1: Redirect ketika tidak ada items di checkout
    public function test_index_redirects_when_checkout_items_empty()
    {
        $this->actingAs($this->user);
        Session::put('checkout_items', []);

        $response = $this->get(route('checkout.page'));

        $response->assertRedirect(route('cart.index'))
                ->assertSessionHas('error', 'Tidak ada produk yang dipilih untuk checkout.');
    }

    //Test 2: Menampilkan halaman checkout dengan items
    public function test_index_returns_view_with_checkout_items()
    {
        $this->actingAs($this->user);
        Session::put('checkout_items', $this->createCheckoutItems());

        $response = $this->get(route('checkout.page'));

        $response->assertOk()
                ->assertViewIs('checkout.index')
                ->assertViewHasAll(['items', 'subtotal', 'total']);

        // Verifikasi perhitungan total
        $viewData = $response->getOriginalContent()->getData();
        $this->assertEquals(35000, $viewData['subtotal']);
        $this->assertEquals(35000, $viewData['total']);
    }

    //Test 3: Validasi gagal untuk required fields
    public function test_place_order_fails_validation()
    {
        $this->actingAs($this->user);

        $this->post(route('checkout.place'), [])
             ->assertSessionHasErrors(['nama', 'telepon', 'alamat_lengkap']);
    }

    //Test 4: Gagal membuat order ketika checkout items kosong
    public function test_place_order_fails_when_checkout_items_empty()
    {
        $this->actingAs($this->user);
        Session::put('checkout_items', []);

        $this->post(route('checkout.place'), $this->getOrderData())
             ->assertRedirect(route('cart.index'))
             ->assertSessionHas('error', 'Tidak ada produk yang dipilih untuk checkout.');
    }

    //Test 5: Berhasil membuat order dan mengurangi stok
    public function test_place_order_creates_order_successfully()
    {
        $this->actingAs($this->user);
        Session::put('checkout_items', $this->createCheckoutItems());

        $response = $this->post(route('checkout.place'), $this->getOrderData());

        // Verifikasi order dibuat
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'Belum dibayar',
            'total' => 35000, // (10000 * 2) + (15000 * 1)
        ]);

        $order = Order::first();
        
        // Verifikasi order items dibuat
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product1->id,
            'qty' => 2,
            'harga' => 10000
        ]);

        // Verifikasi stok berkurang
        $this->assertEquals(8, $this->product1->fresh()->stok); // 10 - 2
        $this->assertEquals(4, $this->product2->fresh()->stok); // 5 - 1

        $response->assertRedirect(route('checkout.payment', $order->id))
                ->assertSessionHas('success', 'Pesanan berhasil dibuat!');
        
        // Verifikasi session dibersihkan
        $this->assertFalse(Session::has('checkout_items'));
    }

    //Test 6: Pengurangan stok produk bekerja dengan benar
    public function test_place_order_reduces_product_stock_correctly()
    {
        $this->actingAs($this->user);
        
        $checkoutItems = $this->createCheckoutItems([
            ['product' => $this->product1, 'qty' => 3]
        ]);
        
        Session::put('checkout_items', $checkoutItems);

        $this->post(route('checkout.place'), $this->getOrderData());

        $this->assertEquals(7, $this->product1->fresh()->stok); // 10 - 3
    }

    //Test 7: Menghapus cart order yang terkait setelah checkout
    public function test_place_order_deletes_related_cart_order()
    {
        $this->actingAs($this->user);

        // Buat cart order
        $cartOrder = Order::create([
            'user_id' => $this->user->id,
            'status' => 'cart',
            'total' => 0,
            'alamat' => ''
        ]);

        OrderItem::create([
            'order_id' => $cartOrder->id,
            'product_id' => $this->product1->id,
            'qty' => 1,
            'harga' => $this->product1->harga
        ]);

        Session::put('checkout_items', $this->createCheckoutItems([
            ['product' => $this->product1, 'qty' => 1]
        ]));

        $this->post(route('checkout.place'), $this->getOrderData());

        // Verifikasi item cart dihapus
        $this->assertDatabaseMissing('order_items', [
            'order_id' => $cartOrder->id,
            'product_id' => $this->product1->id
        ]);
    }

    //Test 8: Menampilkan halaman pembayaran
    public function test_payment_displays_payment_page()
    {
        $this->actingAs($this->user);

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'Belum dibayar',
            'total' => 20000,
            'alamat' => 'Test Address'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product1->id,
            'qty' => 1,
            'harga' => $this->product1->harga
        ]);

        $this->get(route('checkout.payment', $order->id))
             ->assertOk()
             ->assertViewIs('checkout.payment')
             ->assertViewHas('order', $order);
    }

    //Test 9: 404 untuk order yang tidak ditemukan
    public function test_payment_returns_404_for_nonexistent_order()
    {
        $this->actingAs($this->user);

        $this->get(route('checkout.payment', 999))
             ->assertNotFound();
    }

    //Test 10: Authentication required untuk place order
    public function test_place_order_requires_authentication()
    {
        $this->post(route('checkout.place'), $this->getOrderData())
             ->assertRedirect(route('login'));
    }
}