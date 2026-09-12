<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosterPostingLimitService
{
    /**
     * Enforce the daily poster limit. Throws a ValidationException if the limit is reached.
     *
     * @throws ValidationException
     */
    public function enforceDailyLimit(string $phoneNumber, string $type = 'job', string $field = 'phone_number'): void
    {
        $limit = $this->getDailyLimit();
        if ($limit <= 0) {
            return; // 0 or negative means unlimited
        }

        $currentCount = $this->getDailyCount($phoneNumber, $type);

        if ($currentCount >= $limit) {
            $messageTemplate = config(
                'posters.error_message',
                'Daily poster limit reached. You can only post up to :limit posters per day.'
            );
            $message = str_replace(':limit', (string) $limit, $messageTemplate);

            throw ValidationException::withMessages([
                $field => [$message],
            ]);
        }
    }

    /**
     * Get the count of posters created today for this phone number.
     */
    public function getDailyCount(string $phoneNumber, string $type = 'job'): int
    {
        $variants = $this->getPhoneVariants($phoneNumber);
        if (empty($variants)) {
            return 0;
        }

        $startOfDay = now()->startOfDay();
        $mode = config('posters.limit_mode', 'combined');
        $digits = preg_replace('/[^0-9]/', '', $phoneNumber);
        $last10 = strlen($digits) >= 10 ? substr($digits, -10) : null;

        $applyPhoneFilter = function ($query, string $column) use ($variants, $last10) {
            $query->where(function ($q) use ($column, $variants, $last10) {
                $q->whereIn($column, $variants)
                  ->orWhereIn(DB::raw("REPLACE($column, ' ', '')"), $variants);

                if ($last10) {
                    $q->orWhere(DB::raw("REPLACE($column, ' ', '')"), 'LIKE', "%{$last10}");
                }
            });
        };

        if ($mode === 'per_type') {
            if ($type === 'offer') {
                $offerQuery = Offer::where('created_at', '>=', $startOfDay);
                $applyPhoneFilter($offerQuery, 'mobile_number');
                return $offerQuery->count();
            }

            $jobQuery = Job::where('created_at', '>=', $startOfDay);
            $applyPhoneFilter($jobQuery, 'phone_number');
            return $jobQuery->count();
        }

        // Combined mode (default)
        $jobQuery = Job::where('created_at', '>=', $startOfDay);
        $applyPhoneFilter($jobQuery, 'phone_number');
        $jobCount = $jobQuery->count();

        $offerQuery = Offer::where('created_at', '>=', $startOfDay);
        $applyPhoneFilter($offerQuery, 'mobile_number');
        $offerCount = $offerQuery->count();

        return $jobCount + $offerCount;
    }

    /**
     * Get the configured daily limit per phone number.
     */
    public function getDailyLimit(): int
    {
        return (int) config('posters.daily_limit_per_phone', 2);
    }

    /**
     * Generate common representations/variations of a phone number to prevent limit bypass.
     *
     * @return array<int, string>
     */
    public function getPhoneVariants(string $phoneNumber): array
    {
        $raw = trim($phoneNumber);
        if ($raw === '') {
            return [];
        }

        $digits = preg_replace('/[^0-9]/', '', $raw);
        $noSpaces = preg_replace('/\s+/', '', $raw);

        $variants = [$raw, $noSpaces];

        if ($digits !== '') {
            $variants[] = $digits;
            $variants[] = '+' . $digits;

            // If 10 or more digits, extract the last 10 digits
            if (strlen($digits) >= 10) {
                $last10 = substr($digits, -10);
                $variants[] = $last10;
                $variants[] = '0' . $last10;
                $variants[] = '+91' . $last10;
                $variants[] = '91' . $last10;
                $variants[] = '+971' . $last10;
                $variants[] = '971' . $last10;
            }
        }

        return array_values(array_unique(array_filter($variants)));
    }
}
