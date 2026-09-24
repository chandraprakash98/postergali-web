<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_message_and_returns_success_json(): void
    {
        $response = $this->postJson(route('contact-us.store'), [
            'first_name' => 'Asha',
            'last_name' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '+91 9876543210',
            'message' => 'I would like to know more about PosterGali.',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Thank you for contacting PosterGali. Our team will get back to you within 24 hours.');
        $this->assertDatabaseHas('contact_us', [
            'first_name' => 'Asha',
            'last_name' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '+91 9876543210',
            'message' => 'I would like to know more about PosterGali.',
        ]);
    }

    public function test_contact_form_requires_all_fields(): void
    {
        $response = $this->postJson(route('contact-us.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'phone', 'message']);
    }
}