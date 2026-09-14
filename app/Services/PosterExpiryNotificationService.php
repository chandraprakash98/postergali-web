<?php

namespace App\Services;

use App\Models\BatchRunLog;
use Carbon\Carbon;

/**
 * Backward compatibility wrapper for PostergaliAlphaBatchService.
 */
class PosterExpiryNotificationService
{
    public const DEFAULT_SCHEDULE = PostergaliAlphaBatchService::DEFAULT_SCHEDULE;
    public const DEFAULT_TIMEZONE = PostergaliAlphaBatchService::DEFAULT_TIMEZONE;

    public static function getSchedule(): string
    {
        return PostergaliAlphaBatchService::getSchedule();
    }

    public static function getTimezone(): string
    {
        return PostergaliAlphaBatchService::getTimezone();
    }

    public static function getScheduleHuman(): string
    {
        return PostergaliAlphaBatchService::getScheduleHuman(self::getSchedule());
    }

    public static function getNextRunIst(): Carbon
    {
        return PostergaliAlphaBatchService::getNextRun(self::getSchedule(), self::getTimezone());
    }

    public function __construct(
        protected ?PostergaliAlphaBatchService $alphaService = null,
        protected ?FcmTokenResolver $tokenResolver = null,
    ) {
        $this->alphaService  = $alphaService ?? new PostergaliAlphaBatchService();
        $this->tokenResolver = $tokenResolver ?? new FcmTokenResolver();
    }

    /**
     * Legacy wrapper: Find approved posters expiring within 24 hours and send reminder (Function 1).
     */
    public function sendExpiringNotifications(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        $result = $this->alphaService->sendDayBeforeExpiryNotifications($dryRun);
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => 'notify-expiring',
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => $result['jobs_found'] ?? 0,
                'day_before_offers'  => $result['offers_found'] ?? 0,
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
     * Legacy wrapper: Find approved posters whose expiry has passed and send notification (Function 2).
     */
    public function sendExpiredNotifications(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        $result = $this->alphaService->sendExpiringTodayNotifications($dryRun);
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => 'notify-expired',
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => 0,
                'day_before_offers'  => 0,
                'on_expiry_jobs'     => $result['jobs_found'] ?? 0,
                'on_expiry_offers'   => $result['offers_found'] ?? 0,
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
