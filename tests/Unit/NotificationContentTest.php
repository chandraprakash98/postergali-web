<?php

namespace Tests\Unit;

use App\Constants\NotificationContent;
use Tests\TestCase;

class NotificationContentTest extends TestCase
{
    public function test_ad_approved_notification_content_en_and_hi(): void
    {
        $en = NotificationContent::adApproved('en');
        $this->assertEquals('Ad Approved ✓', $en['title']);
        $this->assertEquals('Your ad has been approved and is now live!', $en['body']);

        $hi = NotificationContent::adApproved('hi');
        $this->assertEquals('विज्ञापन स्वीकृत ✓', $hi['title']);
        $this->assertEquals('आपका विज्ञापन स्वीकृत हो गया है और अब लाइव है!', $hi['body']);
    }

    public function test_ad_rejected_notification_content_en_and_hi(): void
    {
        $enDefault = NotificationContent::adRejected(null, 'en');
        $this->assertEquals('Ad Rejected ✕', $enDefault['title']);
        $this->assertEquals('Your ad was rejected. Contact support for details.', $enDefault['body']);

        $enCustom = NotificationContent::adRejected('Image resolution too low', 'en');
        $this->assertEquals('Your ad was rejected. Image resolution too low', $enCustom['body']);

        $hiDefault = NotificationContent::adRejected(null, 'hi');
        $this->assertEquals('विज्ञापन अस्वीकृत ✕', $hiDefault['title']);
        $this->assertEquals('आपका विज्ञापन अस्वीकृत कर दिया गया है। विवरण के लिए सहायता से संपर्क करें।', $hiDefault['body']);

        $hiCustom = NotificationContent::adRejected('छवि स्पष्ट नहीं है', 'hi');
        $this->assertEquals('आपका विज्ञापन अस्वीकृत कर दिया गया है। छवि स्पष्ट नहीं है', $hiCustom['body']);
    }

    public function test_referral_reward_notification_content_en_and_hi(): void
    {
        $en = NotificationContent::referralReward(100, 'en');
        $this->assertEquals('🎉 Referral Reward!', $en['title']);
        $this->assertEquals('🎉 Congratulations! 100 Poster Credits have been added to your account.', $en['body']);

        $hi = NotificationContent::referralReward(100, 'hi');
        $this->assertEquals('🎉 रेफरल इनाम!', $hi['title']);
        $this->assertEquals('🎉 बधाई हो! आपके खाते में 100 पोस्टर क्रेडिट जोड़ दिए गए हैं।', $hi['body']);
    }

    public function test_view_milestone_notification_content_en_and_hi(): void
    {
        $en = NotificationContent::viewMilestone('Cafe Delight', 100, 'en');
        $this->assertEquals('🎉 Views Milestone Crossed!', $en['title']);
        $this->assertStringContainsString('Cafe Delight', $en['body']);
        $this->assertStringContainsString('crossed 100 views', $en['body']);

        $hi = NotificationContent::viewMilestone('Cafe Delight', 100, 'hi');
        $this->assertEquals('🎉 व्यूज माइलस्टोन सूचना!', $hi['title']);
        $this->assertStringContainsString('Cafe Delight', $hi['body']);
        $this->assertStringContainsString('100 व्यूज पूरे कर लिए हैं', $hi['body']);
    }

    public function test_day_before_expiry_notification_content_en_and_hi(): void
    {
        $en = NotificationContent::dayBeforeExpiry('Bakery Hub', 'en');
        $this->assertEquals('⏰ Poster Expires Tomorrow', $en['title']);
        $this->assertStringContainsString('Bakery Hub', $en['body']);
        $this->assertStringContainsString('expires tomorrow', $en['body']);

        $hi = NotificationContent::dayBeforeExpiry('Bakery Hub', 'hi');
        $this->assertEquals('⏰ पोस्टर कल समाप्त हो रहा है', $hi['title']);
        $this->assertStringContainsString('Bakery Hub', $hi['body']);
        $this->assertStringContainsString('कल समाप्त हो रहा है', $hi['body']);
    }

    public function test_on_expiry_notification_content_en_and_hi(): void
    {
        $en = NotificationContent::onExpiry('Fitness Zone', 'en');
        $this->assertEquals('⚠️ Poster Expired', $en['title']);
        $this->assertStringContainsString('Fitness Zone', $en['body']);
        $this->assertStringContainsString('has expired today', $en['body']);

        $hi = NotificationContent::onExpiry('Fitness Zone', 'hi');
        $this->assertEquals('⚠️ पोस्टर समाप्त हो गया', $hi['title']);
        $this->assertStringContainsString('Fitness Zone', $hi['body']);
        $this->assertStringContainsString('की अवधि आज समाप्त हो गई है', $hi['body']);
    }
}
