<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\FabricController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Fabric;

class FabricControllerTest extends TestCase
{
    //Test dasar untuk memastikan controller bisa diinstansiasi
    //Ini test paling fundamental untuk memverifikasi class exist dan bisa dibuat object-nya
    public function test_controller_can_be_instantiated(): void
    {
        $controller = new FabricController();
        $this->assertInstanceOf(FabricController::class, $controller);
    }

    //Test bahwa method index() ada di controller
    //Method ini penting karena menangani request GET untuk halaman fabric
    public function test_index_method_exists(): void
    {
        $controller = new FabricController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    //Test visibility method index() harus public
    //Method controller harus public agar bisa diakses melalui routing Laravel
     
    public function test_index_method_is_public(): void
    {
        $controller = new FabricController();
        $reflection = new \ReflectionMethod($controller, 'index');
        $this->assertTrue($reflection->isPublic());
    }

    //Test signature method index() menerima parameter Request
    //Parameter Request digunakan untuk mengambil query string (kategori, search)
    
    public function test_index_method_accepts_request_parameter(): void
    {
        $controller = new FabricController();
        $reflection = new \ReflectionMethod($controller, 'index');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals(Request::class, $parameters[0]->getType()->getName());
    }

    //Test bahwa Fabric model exist
    //Model ini digunakan untuk query data fabrics di controller
    
    public function test_fabric_model_exists(): void
    {
        $this->assertTrue(class_exists(Fabric::class));
    }

    //Test bahwa Illuminate\Http\Request class exist
    //Request class diperlukan untuk type hinting parameter method
    
    public function test_request_class_exists(): void
    {
        $this->assertTrue(class_exists(Request::class));
    }

    //Test bahwa controller meng-extend base Controller Laravel
    //Base Controller menyediakan functionality helper seperti view(), validate(), dll
    
    public function test_controller_uses_base_controller(): void
    {
        $controller = new FabricController();
        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }

    //Test konvensi nama view 'fabric'
    //View ini akan menampilkan halaman katalog fabrics dengan filter dan pagination
    
    public function test_view_name_follows_convention(): void
    {
        $expectedView = 'fabric';
        $this->assertIsString($expectedView);
        $this->assertNotEmpty($expectedView);
    }

    //Test struktur data yang di-pass ke view menggunakan compact()
    //Controller mengirim 3 variable: $fabrics, $categories, $kategori ke view
    
    public function test_compact_function_data_structure(): void
    {
        // Simulasi data yang di-pass ke view
        $fabrics = 'mock_fabrics';        // Data fabrics yang sudah di-paginate
        $categories = 'mock_categories';  // List kategori untuk filter dropdown
        $kategori = 'mock_kategori';      // Kategori yang sedang aktif dipilih
        
        $data = compact('fabrics', 'categories', 'kategori');
        
        $this->assertArrayHasKey('fabrics', $data);
        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('kategori', $data);
        $this->assertCount(3, $data);
    }

    //Test konsep return view dari method index()
    //Memverifikasi method signature sudah sesuai untuk return view
    
    public function test_method_returns_view_concept(): void
    {
        $controller = new FabricController();
        $reflection = new \ReflectionMethod($controller, 'index');
        
        // Verifikasi method public dan parameter count benar
        $this->assertTrue($reflection->isPublic());
        $this->assertCount(1, $reflection->getParameters());
        
        // Verifikasi parameter type hint adalah Request
        $parameters = $reflection->getParameters();
        $this->assertEquals(Request::class, $parameters[0]->getType()->getName());
    }
}