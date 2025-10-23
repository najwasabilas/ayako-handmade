<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\ProfileController;
use Illuminate\View\View;

class ProfileControllerTest extends TestCase
{
    /* 1. Test utama - method index mengembalikan view profile */
    public function test_index_returns_profile_view()
    {
        // Arrange
        $controller = new ProfileController();

        // Act
        $response = $controller->index();

        // Assert
        $this->assertEquals('profile', $response->name());
    }

    /* 2. Test route dapat diakses via URL */
    public function test_profile_page_can_be_accessed_via_url()
    {
        $response = $this->get('/profile-umkm');

        $response->assertStatus(200);
    }

    /* 3. Test view yang benar digunakan */
    public function test_profile_page_uses_correct_view()
    {
        $response = $this->get('/profile-umkm');

        $response->assertViewIs('profile');
    }

    /* 4. Test dapat diakses tanpa authentication */
    public function test_profile_page_accessible_without_authentication()
    {
        $response = $this->get('/profile-umkm');

        $response->assertOk();
    }

    /* 5. Test response adalah HTML */
    public function test_profile_response_is_html()
    {
        $response = $this->get('/profile-umkm');

        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /* 6. Test tidak redirect */
    public function test_profile_page_does_not_redirect()
    {
        $response = $this->get('/profile-umkm');

        $response->assertStatus(200);
    }

    /* 7. Test route name bekerja dengan benar */
    public function test_route_name_works_correctly()
    {
        $response = $this->get(route('profile-umkm'));

        $response->assertSuccessful();
    }

    /* 8. Test method controller exists */
    public function test_index_method_exists()
    {
        $controller = new ProfileController();

        $this->assertTrue(method_exists($controller, 'index'));
    }

    /* 9. Test response adalah instance View */
    public function test_index_returns_view_instance()
    {
        $controller = new ProfileController();
        $response = $controller->index();

        $this->assertInstanceOf(View::class, $response);
    }

    /* 10. Test view berisi konten expected */
    public function test_profile_view_contains_expected_content()
    {
        $response = $this->get('/profile-umkm');

        // Sesuaikan dengan konten aktual di view profile Anda
        $response->assertSee('Profil'); // Ganti dengan teks yang ada di view
    }
}