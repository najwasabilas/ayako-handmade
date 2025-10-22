<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerIntTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_view_with_product_data()
    {
        // Create product in test database
        $product = Product::create([
            'nama' => 'Test Product',
            'deskripsi' => 'Test Description',
            'harga' => 100000,
            'stok' => 10,
            'kategori' => 'Test'
        ]);

        $controller = new ProductController();
        $result = $controller->show($product->id);

        $this->assertEquals('products.detail', $result->getName());
        
        $viewData = $result->getData();
        $this->assertArrayHasKey('product', $viewData);
        $this->assertEquals($product->id, $viewData['product']->id);
    }

    /** @test */
    public function controller_can_be_instantiated()
    {
        $controller = new ProductController();
        $this->assertInstanceOf(ProductController::class, $controller);
    }

    /** @test */
    public function show_method_exists_and_accessible()
    {
        $controller = new ProductController();
        $this->assertTrue(method_exists($controller, 'show'));
    }
}