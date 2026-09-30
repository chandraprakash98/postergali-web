<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerExistsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_true_when_customer_mobile_exists(): void
    {
        Customer::create(['mobile' => '919876543210']);

        $this->getJson('/api/v1/customers/exists?mobile=%2B91%209876543210')
            ->assertOk()
            ->assertExactJson(true);
    }

    public function test_returns_false_when_customer_mobile_does_not_exist(): void
    {
        $this->getJson('/api/v1/customers/exists?mobile=919876543211')
            ->assertOk()
            ->assertExactJson(false);
    }
}