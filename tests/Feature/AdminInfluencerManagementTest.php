<?php

namespace Tests\Feature;

use App\Models\BonusCoupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInfluencerManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_create_update_and_delete_an_influencer(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.influencers.store'), [
                'influencer_name' => 'Creator One',
                'influencer_bonus_coupon_id' => 'CREATOR10',
                'bonus_coupon_poster_credit' => '250.50',
                'bonus_coupon_status' => 'active',
            ])
            ->assertRedirect(route('admin.influencers'));

        $coupon = BonusCoupon::where('influencer_bonus_coupon_id', 'CREATOR10')->firstOrFail();
        $this->assertSame('Creator One', $coupon->influencer_name);
        $this->assertEquals(250.50, (float) $coupon->bonus_coupon_poster_credit);

        $this->get(route('admin.influencers'))
            ->assertOk()
            ->assertSee('Creator One')
            ->assertSee('CREATOR10');
        $this->get(route('admin.influencers.edit', $coupon))->assertOk();

        $this->put(route('admin.influencers.update', $coupon), [
            'influencer_name' => 'Updated Creator',
            'influencer_bonus_coupon_id' => 'CREATOR10',
            'bonus_coupon_poster_credit' => '500',
            'bonus_coupon_status' => 'inactive',
        ])->assertRedirect(route('admin.influencers'));

        $this->assertDatabaseHas('bonus_coupons', [
            'id' => $coupon->id,
            'influencer_name' => 'Updated Creator',
            'bonus_coupon_poster_credit' => '500.00',
            'bonus_coupon_status' => 'inactive',
        ]);

        $this->delete(route('admin.influencers.delete', $coupon))
            ->assertRedirect(route('admin.influencers'));

        $this->assertDatabaseMissing('bonus_coupons', ['id' => $coupon->id]);
    }

    public function test_influencer_promo_codes_must_be_unique(): void
    {
        BonusCoupon::create([
            'influencer_name' => 'Existing Creator',
            'influencer_bonus_coupon_id' => 'EXISTING10',
            'bonus_coupon_poster_credit' => 100,
            'bonus_coupon_status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.influencers.store'), [
                'influencer_name' => 'Another Creator',
                'influencer_bonus_coupon_id' => 'EXISTING10',
                'bonus_coupon_poster_credit' => 200,
                'bonus_coupon_status' => 'active',
            ])
            ->assertSessionHasErrors('influencer_bonus_coupon_id');
    }

    public function test_influencer_management_requires_admin_authentication(): void
    {
        $this->get(route('admin.influencers'))->assertRedirect(route('admin.login'));
    }
}
