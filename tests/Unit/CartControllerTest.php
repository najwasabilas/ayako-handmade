<?php

use App\Http\Controllers\CartController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Mockery;

beforeEach(function () {
    // bersihkan mock sebelum tiap test
    Mockery::close();
});

afterEach(function () {
    Mockery::close();
});

test('index menampilkan view dengan item keranjang aktif', function () {
    $mockOrder = Mockery::mock(Order::class);
    $mockItem = Mockery::mock(OrderItem::class);
    $mockProduct = Mockery::mock(Product::class);

    $mockProduct->nama = 'Kalung Emas';
    $mockItem->product = $mockProduct;
    $mockOrder->items = collect([$mockItem]);

    Auth::shouldReceive('id')->once()->andReturn(1);

    // Mock query Eloquent
    $mockBuilder = Mockery::mock();
    $mockBuilder->shouldReceive('where')->with('user_id', 1)->andReturnSelf();
    $mockBuilder->shouldReceive('where')->with('status', 'cart')->andReturnSelf();
    $mockBuilder->shouldReceive('with')->with('items.product')->andReturnSelf();
    $mockBuilder->shouldReceive('first')->andReturn($mockOrder);

    Mockery::mock('alias:' . Order::class)
        ->shouldReceive('where')
        ->andReturn($mockBuilder);

    // Mock view
    View::shouldReceive('make')
        ->once()
        ->with('cart.index', Mockery::type('array'))
        ->andReturn('cart view');

    $controller = new CartController();
    $response = $controller->index();

    expect($response)->toBe('cart view');
});

test('updateQuantity berhasil memperbarui jumlah item', function () {
    $mockProduct = Mockery::mock(Product::class);
    $mockProduct->stok = 10;

    $mockOrder = Mockery::mock(Order::class);
    $mockOrderItem = Mockery::mock(OrderItem::class);
    $mockOrderItem->product = $mockProduct;
    $mockOrderItem->qty = 2;
    $mockOrderItem->order = $mockOrder;
    $mockOrderItem->harga = 10000;

    // Mock model find
    Mockery::mock('alias:' . OrderItem::class)
        ->shouldReceive('with')->with('product')->andReturnSelf()
        ->shouldReceive('find')->with(1)->andReturn($mockOrderItem);

    // Mock save & update total
    $mockOrderItem->shouldReceive('save')->once();
    $mockOrder->shouldReceive('update')->once();

    $controller = new CartController();
    $request = new Request(['item_id' => 1, 'qty' => 5]);

    $response = $controller->updateQuantity($request);
    $data = $response->getData(true);

    expect($data['success'])->toBe('Jumlah berhasil diperbarui');
});

test('updateQuantity gagal jika qty melebihi stok', function () {
    $mockProduct = Mockery::mock(Product::class);
    $mockProduct->stok = 3;

    $mockOrderItem = Mockery::mock(OrderItem::class);
    $mockOrderItem->product = $mockProduct;

    Mockery::mock('alias:' . OrderItem::class)
        ->shouldReceive('with')->with('product')->andReturnSelf()
        ->shouldReceive('find')->with(1)->andReturn($mockOrderItem);

    $controller = new CartController();
    $request = new Request(['item_id' => 1, 'qty' => 10]);

    $response = $controller->updateQuantity($request);
    $data = $response->getData(true);

    expect($data['error'])->toBe('Jumlah melebihi stok tersedia');
});

test('remove menghapus item dan update total order', function () {
    $mockOrder = Mockery::mock(Order::class);
    $mockOrder->shouldReceive('update')->once();

    $mockItem = Mockery::mock(OrderItem::class);
    $mockItem->order = $mockOrder;
    $mockItem->shouldReceive('delete')->once();

    Mockery::mock('alias:' . OrderItem::class)
        ->shouldReceive('findOrFail')->with(1)->andReturn($mockItem);

    $controller = new CartController();
    $request = new Request(['id' => 1]);

    $response = $controller->remove($request);
    $data = $response->getData(true);

    expect($data['success'])->toBeTrue();
});

test('checkoutSelected menyimpan item terpilih ke session dan redirect ke checkout.page', function () {
    $mockProduct = Mockery::mock(Product::class);
    $mockProduct->nama = 'Gelang';
    $mockProduct->images = collect([(object)['gambar' => 'img1.jpg']]);

    $mockItem = Mockery::mock(OrderItem::class);
    $mockItem->id = 1;
    $mockItem->product_id = 5;
    $mockItem->harga = 20000;
    $mockItem->qty = 2;
    $mockItem->product = $mockProduct;

    Mockery::mock('alias:' . OrderItem::class)
        ->shouldReceive('whereIn')->andReturnSelf()
        ->shouldReceive('with')->andReturnSelf()
        ->shouldReceive('get')->andReturn(collect([$mockItem]));

    $controller = new CartController();
    $request = new Request(['selected_items' => [1]]);

    $response = $controller->checkoutSelected($request);

    expect($response->getTargetUrl())->toContain('checkout.page');
});
