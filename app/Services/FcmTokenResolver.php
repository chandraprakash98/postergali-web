<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Notification;

class FcmTokenResolver
{
    public function __construct(
        protected PosterPostingLimitService $limitService = new PosterPostingLimitService()
    ) {}

    /**
     * Resolve a valid FCM token from Customer, Notification, or device_id.
     */
    public function resolve(?string $phoneNumber, ?string $deviceId): ?string
    {
        $phoneNumber = trim((string) $phoneNumber);
        $deviceId    = trim((string) $deviceId);

        // 1. Customer table by phone number variants
        if (!empty($phoneNumber)) {
            $variants = $this->limitService->getPhoneVariants($phoneNumber);
            if (!empty($variants)) {
                $customerToken = Customer::whereIn('mobile', $variants)
                    ->whereNotNull('fcm')
                    ->where('fcm', '!=', '')
                    ->value('fcm');
                if ($customerToken) {
                    return $customerToken;
                }
            }
        }

        // 2. Notification table by phone or device_id
        if (!empty($phoneNumber) || !empty($deviceId)) {
            $query = Notification::whereNotNull('fcm_tocken')->where('fcm_tocken', '!=', '');

            if (!empty($phoneNumber) && !empty($deviceId)) {
                $variants = $this->limitService->getPhoneVariants($phoneNumber);
                $query->where(function ($q) use ($variants, $deviceId) {
                    $q->whereIn('mobile', $variants)->orWhere('device_id', $deviceId);
                });
            } elseif (!empty($phoneNumber)) {
                $variants = $this->limitService->getPhoneVariants($phoneNumber);
                $query->whereIn('mobile', $variants);
            } else {
                $query->where('device_id', $deviceId);
            }

            $notificationToken = $query->latest('id')->value('fcm_tocken');
            if ($notificationToken) {
                return $notificationToken;
            }
        }

        // 3. Raw device_id if it's already an FCM token (80+ chars, no spaces)
        if (strlen($deviceId) >= 80 && !str_contains($deviceId, ' ')) {
            return $deviceId;
        }

        return null;
    }
}
