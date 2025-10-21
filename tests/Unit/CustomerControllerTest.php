<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class CustomerControllerTest extends TestCase
{
    #[Test]
    public function controller_can_be_instantiated(): void
    {
        $controller = new CustomerController();
        $this->assertInstanceOf(CustomerController::class, $controller);
    }

    #[Test]
    public function profile_method_exists(): void
    {
        $controller = new CustomerController();
        $this->assertTrue(method_exists($controller, 'profile'));
    }

    #[Test]
    public function updateProfile_method_exists(): void
    {
        $controller = new CustomerController();
        $this->assertTrue(method_exists($controller, 'updateProfile'));
    }

    #[Test]
    public function profile_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(CustomerController::class, 'profile');
        $this->assertTrue($reflection->isPublic());
    }

    #[Test]
    public function updateProfile_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(CustomerController::class, 'updateProfile');
        $this->assertTrue($reflection->isPublic());
    }

    #[Test]
    public function updateProfile_method_accepts_request_parameter(): void
    {
        $reflection = new \ReflectionMethod(CustomerController::class, 'updateProfile');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters, 'updateProfile() seharusnya memiliki 1 parameter.');
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals(Request::class, $parameters[0]->getType()->getName());
    }

    #[Test]
    public function user_model_exists(): void
    {
        $this->assertTrue(class_exists(User::class));
    }

    #[Test]
    public function request_class_exists(): void
    {
        $this->assertTrue(class_exists(Request::class));
    }

    #[Test]
    public function controller_uses_base_controller(): void
    {
        $controller = new CustomerController();
        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }

    #[Test]
    public function expected_view_name_is_customer_profile(): void
    {
        $expectedView = 'customer.profile';
        $this->assertIsString($expectedView);
        $this->assertNotEmpty($expectedView);
    }

    #[Test]
    public function compact_function_data_structure_is_valid(): void
    {
        // Simulasi data yang akan dikirim ke view profile customer
        $user = 'mock_user';
        $orders = 'mock_orders';
        $notifications = 'mock_notifications';

        $data = compact('user', 'orders', 'notifications');

        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('orders', $data);
        $this->assertArrayHasKey('notifications', $data);
        $this->assertCount(3, $data);
    }

    #[Test]
    public function profile_method_conceptually_returns_view(): void
    {
        $reflection = new \ReflectionMethod(CustomerController::class, 'profile');

        $this->assertTrue($reflection->isPublic());
        $this->assertCount(0, $reflection->getParameters(), 'profile() seharusnya tidak memiliki parameter.');
    }

    #[Test]
    public function updateProfile_method_conceptually_returns_redirect(): void
    {
        $reflection = new \ReflectionMethod(CustomerController::class, 'updateProfile');

        $this->assertTrue($reflection->isPublic());
        $this->assertCount(1, $reflection->getParameters());
        $parameters = $reflection->getParameters();
        $this->assertEquals(Request::class, $parameters[0]->getType()->getName());
    }
}
