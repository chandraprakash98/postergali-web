<?php

namespace Tests\Feature;

use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OfferApiFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_offers_index_can_filter_by_subcategory_expiry_and_radius(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-18 12:00:00'));

        Offer::create([
            'temp_id' => 'offer-shop',
            'device_id' => 'device-offer-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'Shop/Office/School Staff',
            'business_name' => 'Campus Offer',
            'offer_details' => 'Great deal',
            'offer_type' => 'deal',
            'mobile_number' => '777777777',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'approved_at' => now(),
            'expires_at' => now()->addDays(3),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        Offer::create([
            'temp_id' => 'offer-health',
            'device_id' => 'device-offer-2',
            'device_os' => 'ios',
            'master_category' => 'Services',
            'subcategory' => 'Healthcare',
            'business_name' => 'Health Offer',
            'offer_details' => 'Health deal',
            'offer_type' => 'deal',
            'mobile_number' => '888888888',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'approved_at' => now(),
            'expires_at' => now()->addDays(10),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        $response = $this->getJson('/api/v1/offers?latitude=28.591400&longitude=77.402100&radius=15&sub_categories=shop&is_expiry=within_3_days');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('pagination.total', 1);
    }
}
