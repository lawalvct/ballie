<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\Tenant\OnboardingController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class OnboardingConnectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_refresh_database_connection()
    {
        $controller = new OnboardingController();

        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('refreshDatabaseConnection');
        $method->setAccessible(true);

        // This should not throw an exception
        $method->invoke($controller);

        // Verify connection is working
        $this->assertTrue(DB::connection()->getPdo() instanceof \PDO);
    }

    /** @test */
    public function it_can_retry_operations()
    {
        $controller = new OnboardingController();
        $attempts = 0;

        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('retryOperation');
        $method->setAccessible(true);

        // Test successful operation
        $method->invoke($controller, function() use (&$attempts) {
            $attempts++;
            if ($attempts < 2) {
                throw new \Exception('Test exception');
            }
            return true;
        }, 'Test operation');

        $this->assertEquals(2, $attempts);
    }
}
