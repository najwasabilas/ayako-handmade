<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $products;

    protected function setUp(): void
    {
        parent::setUp();

        // Create sample products with different categories
        $this->products = [
            Product::create([
                'nama' => 'Product 1', 
                'kategori' => 'Elektronik', 
                'harga' => 100000,
                'stok' => 10,
                'deskripsi' => 'Deskripsi product 1'
            ]),
            Product::create([
                'nama' => 'Product 2', 
                'kategori' => 'Fashion', 
                'harga' => 50000,
                'stok' => 5,
                'deskripsi' => 'Deskripsi product 2'
            ]),
            Product::create([
                'nama' => 'Another Product', 
                'kategori' => 'Elektronik', 
                'harga' => 150000,
                'stok' => 3,
                'deskripsi' => 'Deskripsi another product'
            ]),
        ];
    }

    /**
     * Test: Menampilkan semua produk tanpa filter
     */
    public function test_index_displays_all_products_without_filters()
    {
        $response = $this->get(route('katalog'));

        $response->assertOk()
                ->assertViewIs('katalog')
                ->assertViewHasAll(['products', 'categories']);

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(3, $viewProducts->items());
    }

    /**
     * Test: Filter produk berdasarkan kategori
     */
    public function test_index_filters_products_by_category()
    {
        $response = $this->get(route('katalog', ['kategori' => 'Elektronik']));

        $response->assertOk()
                ->assertViewIs('katalog');

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(2, $viewProducts->items());
        
        // Pastikan hanya produk dengan kategori Elektronik
        foreach ($viewProducts as $product) {
            $this->assertEquals('Elektronik', $product->kategori);
        }
    }

    /**
     * Test: Search produk berdasarkan nama
     */
    public function test_index_searches_products_by_name()
    {
        $response = $this->get(route('katalog', ['search' => 'Product 1']));

        $response->assertOk()
                ->assertViewIs('katalog');

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(1, $viewProducts->items());
        $this->assertEquals('Product 1', $viewProducts->first()->nama);
    }

    /**
     * Test: Kombinasi filter kategori dan search
     */
    public function test_index_filters_with_category_and_search_combination()
    {
        $response = $this->get(route('katalog', [
            'kategori' => 'Elektronik',
            'search' => 'Another'
        ]));

        $response->assertOk();

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(1, $viewProducts->items());
        $this->assertEquals('Another Product', $viewProducts->first()->nama);
        $this->assertEquals('Elektronik', $viewProducts->first()->kategori);
    }

    /**
     * Test: Search dengan kata kunci yang tidak ditemukan
     */
    public function test_index_returns_empty_when_search_not_found()
    {
        $response = $this->get(route('katalog', ['search' => 'Nonexistent Product']));

        $response->assertOk();

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(0, $viewProducts->items());
    }

    /**
     * Test: Filter dengan kategori yang tidak ada
     */
    public function test_index_returns_empty_when_category_not_exists()
    {
        $response = $this->get(route('katalog', ['kategori' => 'NonexistentCategory']));

        $response->assertOk();

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(0, $viewProducts->items());
    }

    /**
     * Test: Pagination bekerja dengan benar
     */
     public function test_index_paginates_results()
    {
        // Create more products manually tanpa factory
        for ($i = 4; $i <= 16; $i++) {
            Product::create([
                'nama' => "Product {$i}", 
                'kategori' => 'Kategori ' . ($i % 3 + 1), 
                'harga' => 10000 * $i,
                'stok' => $i,
                'deskripsi' => "Deskripsi product {$i}"
            ]);
        }

        $response = $this->get(route('katalog'));
        $response->assertOk();
        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(12, $viewProducts->items()); // Default pagination 12
        $this->assertEquals(16, $viewProducts->total()); // 3 existing + 13 new
    }

    /**
     * Test: Kategori yang unik diambil dengan benar
     */
    public function test_index_retrieves_unique_categories()
    {
        $response = $this->get(route('katalog'));

        $response->assertOk();

        $viewCategories = $response->getOriginalContent()->getData()['categories'];
        $this->assertCount(2, $viewCategories); // Elektronik dan Fashion
        $this->assertContains('Elektronik', $viewCategories);
        $this->assertContains('Fashion', $viewCategories);
    }

    /**
     * Test: Empty state ketika tidak ada produk
     */
    public function test_index_handles_empty_products()
    {
        // Hapus semua produk
        Product::query()->delete();

        $response = $this->get(route('katalog'));

        $response->assertOk();

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(0, $viewProducts->items());
        
        $viewCategories = $response->getOriginalContent()->getData()['categories'];
        $this->assertCount(0, $viewCategories);
    }

    /**
     * Test: Search dengan kata kunci spesifik untuk partial match yang lebih jelas
     */
    public function test_index_searches_products_with_specific_partial_match()
    {
        $response = $this->get(route('katalog', ['search' => 'Another']));

        $response->assertOk();

        $viewProducts = $response->getOriginalContent()->getData()['products'];
        $this->assertCount(1, $viewProducts->items());
        $this->assertEquals('Another Product', $viewProducts->first()->nama);
    }
}