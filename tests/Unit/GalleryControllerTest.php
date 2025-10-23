<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\GalleryController;
use Illuminate\View\View;
use PHPUnit\Framework\Attributes\Test;

class GalleryControllerTest extends TestCase
{
    #[Test]
    public function it_can_instantiate_the_controller()
    {
        $controller = new GalleryController();
        $this->assertInstanceOf(GalleryController::class, $controller);
    }

    #[Test]
    public function index_method_returns_a_view_instance()
    {
        $controller = new GalleryController();
        $response = $controller->index();

        $this->assertInstanceOf(View::class, $response);
    }

    #[Test]
    public function index_method_returns_gallery_view()
    {
        $controller = new GalleryController();
        $response = $controller->index();

        $this->assertEquals('gallery', $response->getName());
    }

    #[Test]
    public function index_method_does_not_throw_any_exception()
    {
        $controller = new GalleryController();

        $this->expectNotToPerformAssertions();
        $controller->index();
    }
}
