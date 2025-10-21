<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\AuthController;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AuthController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthController();
    }

    public function test_tampilkan_halaman_register_mengembalikan_view()
    {
        $response = $this->controller->showRegister();
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function test_tampilkan_halaman_login_mengembalikan_view()
    {
        $response = $this->controller->showLogin();
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function test_register_berhasil_membuat_user_dan_redirect_ke_home()
    {
        $request = Request::create('/register', 'POST', [
            'name' => 'Unit Tester',
            'email' => 'unit@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response = $this->controller->register($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertDatabaseHas('users', ['email' => 'unit@test.com']);

        $user = User::where('email', 'unit@test.com')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertAuthenticatedAs($user);
        $this->assertEquals(route('home'), $response->getTargetUrl());
    }

    public function test_register_gagal_jika_data_tidak_valid()
    {
        $request = Request::create('/register', 'POST', []);
        $this->expectException(ValidationException::class);
        $this->controller->register($request);
    }

    public function test_login_berhasil_dan_redirect_ke_home()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'customer',
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        // Tambahkan session secara manual
        $request->setLaravelSession(app('session')->driver());

        $response = $this->controller->login($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('home'), $response->getTargetUrl());
        $this->assertAuthenticatedAs($user);
    }



    public function test_login_gagal_mengembalikan_error_dan_tetap_guest()
    {
        $user = User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'wrong@example.com',
            'password' => 'invalid',
        ]);

        $response = $this->controller->login($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertGuest();
        $this->assertTrue(session()->has('errors'));
    }

    public function test_logout_berhasil_menghapus_session_dan_redirect_ke_login()
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user);

        $request = Request::create('/logout', 'POST');

        // Inject session secara manual
        $request->setLaravelSession(app('session')->driver());

        $response = $this->controller->logout($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }

}
