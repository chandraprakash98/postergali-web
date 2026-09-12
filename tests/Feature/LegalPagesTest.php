<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_terms_and_conditions_page_is_accessible(): void
    {
        $this->get('/terms-and-conditions')
            ->assertStatus(200)
            ->assertSee('About PosterGali')
            ->assertSee('contact@postergali.com');
    }
}
