<?php

namespace App\Constants;

class NotificationContent
{
    // ==========================================
    // 1. Ad Status Notifications (Admin Panel)
    // ==========================================

    // Ad Approved - English
    public const AD_APPROVED_TITLE_EN = '🎉 Congratulations!';  
    public const AD_APPROVED_BODY_EN  = 'Your poster is now LIVE on PosterGali.';

    // Ad Approved - Hindi
    public const AD_APPROVED_TITLE_HI = '🎉 बधाई हो!';
    public const AD_APPROVED_BODY_HI  = 'आपका पोस्टर अब PosterGali पर LIVE है.';

    // Ad Rejected - English
    public const AD_REJECTED_TITLE_EN = '❌ Sorry!';
    public const AD_REJECTED_BODY_EN  = 'Your poster was rejected, Your refund will be processed within 2 days.';
    public const AD_REJECTED_DEFAULT_REASON_EN = 'Contact support for details.';

    // Ad Rejected - Hindi
    public const AD_REJECTED_TITLE_HI = '❌ क्षमा करें!';
    public const AD_REJECTED_BODY_HI  = 'आपका पोस्टर :reason के कारण अस्वीकार कर दिया गया है। आपका रिफंड 2 दिनों के भीतर कर दिया जाएगा।';
    public const AD_REJECTED_DEFAULT_REASON_HI = 'विवरण के लिए सहायता से संपर्क करें।';

    // ==========================================
    // 2. Referral Reward Notifications
    // ==========================================

    // Referral Reward - English
    public const REFERRAL_REWARD_TITLE_EN = '🎉 Referral Reward!';
    public const REFERRAL_REWARD_BODY_EN  = '🎉 Congratulations! :credits Poster Credits have been added to your account.';

    // Referral Reward - Hindi
    public const REFERRAL_REWARD_TITLE_HI = '🎉 रेफरल इनाम!';
    public const REFERRAL_REWARD_BODY_HI  = '🎉 बधाई हो! आपके खाते में :credits पोस्टर क्रेडिट जोड़ दिए गए हैं।';

    // ==========================================
    // 3. Views Milestone Crossed Notifications
    // ==========================================

    // View Milestone - English
    public const VIEW_MILESTONE_TITLE_EN = '🎉 Views Milestone Crossed!';
    public const VIEW_MILESTONE_BODY_EN  = '🚀 Great news! Your poster ":business_name" has crossed :views views on PosterGali! Keep tracking your responses.';

    // View Milestone - Hindi
    public const VIEW_MILESTONE_TITLE_HI = '🎉 व्यूज माइलस्टोन सूचना!';
    public const VIEW_MILESTONE_BODY_HI  = '🚀 खुशखबरी! PosterGali पर आपके पोस्टर ":business_name" ने :views व्यूज पूरे कर लिए हैं! नए ग्राहकों से जुड़े रहें।';

    // ==========================================
    // 4. Day Before Expiry Notifications
    // ==========================================

    // Day Before Expiry - English
    public const DAY_BEFORE_EXPIRY_TITLE_EN = '📅 Your poster expires tomorrow.';
    public const DAY_BEFORE_EXPIRY_BODY_EN  = '⏰ Create a new poster today and continue reaching more people on PosterGali.';

    // Day Before Expiry - Hindi
    public const DAY_BEFORE_EXPIRY_TITLE_HI = '📅 आपके पोस्टर की अवधि कल समाप्त हो रही है।';
    public const DAY_BEFORE_EXPIRY_BODY_HI  = '⏰  नया पोस्टर बनाएं और PosterGali पर अधिक लोगों तक पहुंचना जारी रखें।';

    // ==========================================
    // 5. On Poster Expiry Notifications
    // ==========================================

    // On Expiry - English
    public const ON_EXPIRY_TITLE_EN = '⚠️ Poster Expired';
    public const ON_EXPIRY_BODY_EN  = '⚠️ Your poster ":business_name" has expired today. Create a new poster on PosterGali and keep reaching more people online.';

    // On Expiry - Hindi
    public const ON_EXPIRY_TITLE_HI = '⚠️ पोस्टर समाप्त हो गया';
    public const ON_EXPIRY_BODY_HI  = '⚠️ आपके पोस्टर ":business_name" की अवधि आज समाप्त हो गई है। नया पोस्टर बनाएं और PosterGali पर अधिक लोगों तक पहुंचना जारी रखें।';

    /**
     * Get title and body for Ad Approved notification.
     * Default language: English ('en').
     */
    public static function adApproved(string $lang = 'en'): array
    {
        return [
            'title' => $lang === 'hi' ? self::AD_APPROVED_TITLE_HI : self::AD_APPROVED_TITLE_EN,
            'body'  => $lang === 'hi' ? self::AD_APPROVED_BODY_HI : self::AD_APPROVED_BODY_EN,
        ];
    }

    /**
     * Get title and body for Ad Rejected notification.
     * Default language: English ('en').
     */
    public static function adRejected(?string $reason = null, string $lang = 'en'): array
    {
        $cleanReason = trim((string) $reason);

        if ($lang === 'hi') {
            $effectiveReason = $cleanReason !== '' ? $cleanReason : self::AD_REJECTED_DEFAULT_REASON_HI;
            $body = strtr(self::AD_REJECTED_BODY_HI, [':reason' => $effectiveReason]);
            return [
                'title' => self::AD_REJECTED_TITLE_HI,
                'body'  => $body,
            ];
        }

        $effectiveReason = $cleanReason !== '' ? $cleanReason : self::AD_REJECTED_DEFAULT_REASON_EN;
        $body = strtr(self::AD_REJECTED_BODY_EN, [':reason' => $effectiveReason]);

        return [
            'title' => self::AD_REJECTED_TITLE_EN,
            'body'  => $body,
        ];
    }

    /**
     * Get title and body for Referral Reward notification.
     * Default language: English ('en').
     */
    public static function referralReward(int|float|string $credits = 100, string $lang = 'en'): array
    {
        $creditsStr = (string) $credits;

        if ($lang === 'hi') {
            return [
                'title' => self::REFERRAL_REWARD_TITLE_HI,
                'body'  => strtr(self::REFERRAL_REWARD_BODY_HI, [':credits' => $creditsStr]),
            ];
        }

        return [
            'title' => self::REFERRAL_REWARD_TITLE_EN,
            'body'  => strtr(self::REFERRAL_REWARD_BODY_EN, [':credits' => $creditsStr]),
        ];
    }

    /**
     * Get title and body for Views Milestone notification.
     * Default language: English ('en').
     */
    public static function viewMilestone(string $businessName, int|string $views, string $lang = 'en'): array
    {
        $replacements = [
            ':business_name' => $businessName,
            ':views' => (string) $views,
        ];

        if ($lang === 'hi') {
            return [
                'title' => self::VIEW_MILESTONE_TITLE_HI,
                'body'  => strtr(self::VIEW_MILESTONE_BODY_HI, $replacements),
            ];
        }

        return [
            'title' => self::VIEW_MILESTONE_TITLE_EN,
            'body'  => strtr(self::VIEW_MILESTONE_BODY_EN, $replacements),
        ];
    }

    /**
     * Get title and body for Day Before Expiry notification.
     * Default language: English ('en').
     */
    public static function dayBeforeExpiry(string $businessName, string $lang = 'en'): array
    {
        $replacements = [
            ':business_name' => $businessName,
        ];

        if ($lang === 'hi') {
            return [
                'title' => self::DAY_BEFORE_EXPIRY_TITLE_HI,
                'body'  => strtr(self::DAY_BEFORE_EXPIRY_BODY_HI, $replacements),
            ];
        }

        return [
            'title' => self::DAY_BEFORE_EXPIRY_TITLE_EN,
            'body'  => strtr(self::DAY_BEFORE_EXPIRY_BODY_EN, $replacements),
        ];
    }

    /**
     * Get title and body for On Expiry notification.
     * Default language: English ('en').
     */
    public static function onExpiry(string $businessName, string $lang = 'en'): array
    {
        $replacements = [
            ':business_name' => $businessName,
        ];

        if ($lang === 'hi') {
            return [
                'title' => self::ON_EXPIRY_TITLE_HI,
                'body'  => strtr(self::ON_EXPIRY_BODY_HI, $replacements),
            ];
        }

        return [
            'title' => self::ON_EXPIRY_TITLE_EN,
            'body'  => strtr(self::ON_EXPIRY_BODY_EN, $replacements),
        ];
    }
}
