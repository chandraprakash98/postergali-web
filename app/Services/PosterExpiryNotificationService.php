<?php

namespace App\Services;

use App\Services\Batches\Actions\CheckExpiredPostersAction;
use App\Services\Batches\Actions\CheckExpiringSoonPostersAction;
use App\Services\Batches\BatchHelper;
use Carbon\Carbon;

class PosterExpiryNotificationService
{
    // ── Schedule constants (kept for backward compatibility) ─────────────────
    public const DEFAULT_SCHEDULE = '0 6 * * *';
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    public static function getSchedule(): string
    {
        return (string) config('posters.expiry_notification.schedule', self::DEFAULT_SCHEDULE);
    }

    public static function getTimezone(): string
    {
        return (string) config('posters.expiry_notification.timezone', self::DEFAULT_TIMEZONE);
    }

    public static function getScheduleHuman(): string
    {
        return BatchHelper::getScheduleHuman(self::getSchedule());
    }

    public static function getNextRunIst(): Carbon
    {
        return BatchHelper::getNextRun(self::getSchedule(), self::getTimezone());
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected PosterPostingLimitService $limitService = new PosterPostingLimitService(),
        protected ?FcmTokenResolver $tokenResolver = null,
        protected ?CheckExpiringSoonPostersAction $expiringAction = null,
        protected ?CheckExpiredPostersAction $expiredAction = null,
    ) {
        $this->tokenResolver  = $tokenResolver ?? new FcmTokenResolver($this->limitService);
        $this->expiringAction = $expiringAction ?? new CheckExpiringSoonPostersAction($this->firebaseService, $this->tokenResolver);
        $this->expiredAction  = $expiredAction ?? new CheckExpiredPostersAction($this->firebaseService, $this->tokenResolver);
    }

    /**
     * Legacy wrapper: Find approved posters expiring within 24 hours and send reminder.
     */
    public function sendExpiringNotifications(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        $result = $this->expiringAction->execute($dryRun);
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            \App\Models\BatchRunLog::create([
                'batch_name'         => 'notify-expiring',
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => $result['found'] ?? 0,
                'day_before_offers'  => 0,
                'on_expiry_jobs'     => 0,
                'on_expiry_offers'   => 0,
                'notifications_sent' => $result['sent'] ?? 0,
                'skipped_no_token'   => $result['skipped'] ?? 0,
                'duration_ms'        => $durationMs,
            ]);
        }

        return $result;
    }

    /**
     * Legacy wrapper: Find approved posters whose expiry has passed and send notification.
     */
    public function sendExpiredNotifications(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        $result = $this->expiredAction->execute($dryRun);
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            \App\Models\BatchRunLog::create([
                'batch_name'         => 'notify-expired',
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => 0,
                'day_before_offers'  => 0,
                'on_expiry_jobs'     => $result['found'] ?? 0,
                'on_expiry_offers'   => 0,
                'notifications_sent' => $result['sent'] ?? 0,
                'skipped_no_token'   => $result['skipped'] ?? 0,
                'duration_ms'        => $durationMs,
            ]);
        }

        return $result;
    }

    /**
     * Token resolution delegated to FcmTokenResolver.
     */
    public function resolveFcmToken(?string $phoneNumber, ?string $deviceId): ?string
    {
        return $this->tokenResolver->resolve($phoneNumber, $deviceId);
    }
}
