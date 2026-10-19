<?php

namespace Tests\Feature;

use App\Mail\ContactUsMessage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    public function test_contact_form_emails_message_and_redirects_with_success(): void
    {
        Mail::fake();

        $response = $this->post(route('contact-us.store'), [
            'first_name' => 'Asha',
            'last_name' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '+91 9876543210',
            'message' => 'I would like to know more about PosterGali.',
        ]);

        $response->assertRedirect('/#contact');
        $response->assertSessionHas('contact_success', true);
        Mail::assertSent(ContactUsMessage::class, function (ContactUsMessage $mail): bool {
            return $mail->hasTo('officialcp1998@gmail.com')
                && $mail->hasReplyTo('asha@example.com')
                && $mail->contact['message'] === 'I would like to know more about PosterGali.';
        });
    }

    public function test_contact_form_requires_all_fields(): void
    {
        Mail::fake();

        $response = $this->post(route('contact-us.store'), []);

        $response->assertRedirect('/#contact');
        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'phone', 'message']);
        Mail::assertNothingOutgoing();
    }
}