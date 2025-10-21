<?php

use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Session\Store;
use Illuminate\Session\ArraySessionHandler;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('showRegister returns a view instance', function () {
    $controller = new AuthController();
    $response = $controller->showRegister();
    $this->assertInstanceOf(\Illuminate\View\View::class, $response);
});

test('showLogin returns a view instance', function () {
    $controller = new AuthController();
    $response = $controller->showLogin();
    $this->assertInstanceOf(\Illuminate\View\View::class, $response);
});

test('register success creates user, logs in and redirects to home', function () {
    $request = Request::create('/register', 'POST', [
        'name' => 'Unit Tester',
        'email' => 'unit@test.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $this->assertDatabaseCount('users', 0);

    $controller = new AuthController();
    $response = $controller->register($request);

    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertDatabaseHas('users', [
        'email' => 'unit@test.com',
        'name' => 'Unit Tester',
    ]);

    $user = \App\Models\User::where('email', 'unit@test.com')->first();
    $this->assertNotNull($user);
    $this->assertTrue(Hash::check('secret123', $user->password));
    $this->assertAuthenticatedAs($user);
});

test('register validation fails throws ValidationException and no user created', function () {
    $request = Request::create('/register', 'POST', []);
    $controller = new AuthController();

    $this->expectException(\Illuminate\Validation\ValidationException::class);

    try {
        $controller->register($request);
    } finally {
        $this->assertDatabaseCount('users', 0);
    }
});

test('login success attempts auth, regenerates session and redirects to home', function () {
    $password = 'mypwd';
    $user = \App\Models\User::create([
        'name' => 'Login User',
        'email' => 'loginuser@example.com',
        'password' => bcrypt($password),
        'role' => 'customer',
    ]);

    $request = Request::create('/login', 'POST', [
        'email' => 'loginuser@example.com',
        'password' => $password,
    ]);

    // set a proper session store (ArraySessionHandler needs minutes argument)
    $session = new Store('laravel_session', new ArraySessionHandler(120));
    $request->setLaravelSession($session);

    $controller = new AuthController();
    $response = $controller->login($request);

    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertAuthenticatedAs($user);
});

test('login fail returns back with errors and user remains guest', function () {
    \App\Models\User::create([
        'name' => 'Try User',
        'email' => 'tryuser@example.com',
        'password' => bcrypt('rightpwd'),
        'role' => 'customer',
    ]);

    $request = Request::create('/login', 'POST', [
        'email' => 'tryuser@example.com',
        'password' => 'wrongpwd',
    ]);

    $session = new Store('laravel_session', new ArraySessionHandler(120));
    $request->setLaravelSession($session);

    $controller = new AuthController();
    $response = $controller->login($request);

    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertGuest();
});

test('logout invalidates session, regenerates token and redirects to login', function () {
    $user = \App\Models\User::create([
        'name' => 'UserToLogout',
        'email' => 'logout@example.com',
        'password' => bcrypt('pwd'),
        'role' => 'customer',
    ]);

    $this->actingAs($user);

    $request = Request::create('/logout', 'POST');
    $session = new Store('laravel_session', new ArraySessionHandler(120));
    $request->setLaravelSession($session);

    $controller = new AuthController();
    $response = $controller->logout($request);

    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertStringContainsString('login', $response->getTargetUrl());
    $this->assertGuest();
});
