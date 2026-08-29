<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FilterService
{
    /**
     * Whitelist of allowed parameters for Jobs and Offers.
     */
    public const ALLOWED_JOB_INDEX_PARAMS = [
        'latitude', 'longitude', 'radius', 'per_page', 'page',
        'sub_categories', 'sub_category', 'is_expiry', 'job_type', 'job_types', 'salary'
    ];

    public const ALLOWED_JOB_SEARCH_PARAMS = [
        'device_id', 'latitude', 'longitude', 'radius', 'phone_number', 'mobile_number',
        'per_page', 'page', 'sub_categories', 'sub_category', 'is_expiry', 'job_type', 'job_types', 'salary'
    ];

    public const ALLOWED_OFFER_INDEX_PARAMS = [
        'latitude', 'longitude', 'radius', 'per_page', 'page',
        'sub_categories', 'sub_category', 'is_expiry', 'offer_type', 'offer_types'
    ];

    public const ALLOWED_OFFER_SEARCH_PARAMS = [
        'device_id', 'latitude', 'longitude', 'radius', 'mobile_number', 'phone_number',
        'per_page', 'page', 'sub_categories', 'sub_category', 'is_expiry', 'offer_type', 'offer_types'
    ];

    /**
     * Validate that no unsupported query parameters are passed in the request.
     *
     * @throws ValidationException
     */
    public function rejectUnsupportedParams(Request $request, array $allowedParams): void
    {
        $keys = array_keys($request->query());
        $unsupported = array_diff($keys, $allowedParams);

        if (!empty($unsupported)) {
            $invalidKeys = implode(', ', $unsupported);
            throw ValidationException::withMessages([
                'filters' => ["Unsupported query filter parameter(s): $invalidKeys."],
            ]);
        }
    }

    /**
     * Normalize subcategory filter inputs (string, comma-separated, or array).
     */
    public function normalizeSubCategories(Request $request): array
    {
        $raw = $request->input('sub_categories', $request->input('sub_category'));

        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $raw)));
        }

        $parts = preg_split('/,/', (string) $raw) ?: [];

        return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $parts)));
    }

    /**
     * Apply subcategory filter with partial and per-word matching.
     */
    public function applySubCategoryFilter(Builder $query, array $subCategories, string $column = 'subcategory'): void
    {
        if (empty($subCategories)) {
            return;
        }

        $stopWords = ['and', 'or', 'the', 'of', 'in', 'at', 'for', 'to', 'a', 'an', 'is', 'by', 'with', 'on'];

        $query->where(function (Builder $q) use ($subCategories, $stopWords, $column) {
            foreach ($subCategories as $phrase) {
                $phrase = trim((string) $phrase);
                if ($phrase === '') {
                    continue;
                }

                $words = preg_split('/[\s]+/', strtolower($phrase)) ?: [];
                $words = array_values(array_filter(
                    $words,
                    static fn ($w) => strlen($w) >= 3 && !in_array($w, $stopWords, true)
                ));

                if (empty($words)) {
                    $q->orWhere(DB::raw("LOWER($column)"), 'like', '%' . strtolower($phrase) . '%');
                    continue;
                }

                foreach ($words as $word) {
                    $q->orWhere(DB::raw("LOWER($column)"), 'like', '%' . $word . '%');
                }
            }
        });
    }

    /**
     * Normalize job types (string, comma-separated, or array).
     */
    public function normalizeJobTypes(Request $request): array
    {
        $raw = $request->input('job_types', $request->input('job_type'));

        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $raw)));
        }

        $parts = preg_split('/[,\s]+/', (string) $raw) ?: [];

        return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $parts)));
    }

    /**
     * Apply job type filter.
     */
    public function applyJobTypeFilter(Builder $query, array $jobTypes): void
    {
        if (empty($jobTypes)) {
            return;
        }

        $query->where(function (Builder $q) use ($jobTypes) {
            foreach ($jobTypes as $jobType) {
                $normalized = strtolower(trim((string) $jobType));
                if ($normalized === '') {
                    continue;
                }
                $q->orWhere(DB::raw('LOWER(job_type)'), 'like', '%' . $normalized . '%');
            }
        });
    }

    /**
     * Normalize offer types (string, comma-separated, or array).
     */
    public function normalizeOfferTypes(Request $request): array
    {
        $raw = $request->input('offer_types', $request->input('offer_type'));

        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $raw)));
        }

        $parts = preg_split('/[,\s]+/', (string) $raw) ?: [];

        return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $parts)));
    }

    /**
     * Apply offer type filter.
     */
    public function applyOfferTypeFilter(Builder $query, array $offerTypes): void
    {
        if (empty($offerTypes)) {
            return;
        }

        $query->where(function (Builder $q) use ($offerTypes) {
            foreach ($offerTypes as $offerType) {
                $normalized = strtolower(trim((string) $offerType));
                if ($normalized === '') {
                    continue;
                }
                $q->orWhere(DB::raw('LOWER(offer_type)'), 'like', '%' . $normalized . '%');
            }
        });
    }

    /**
     * Parse salary filter into numeric min/max range.
     *
     * @throws ValidationException
     */
    public function normalizeSalaryRange(?string $salary): ?array
    {
        if ($salary === null || trim($salary) === '') {
            return null;
        }

        $normalized = preg_replace('/[₹,\x{20B9}]/u', '', $salary);
        $normalized = strtolower(trim((string) $normalized));
        $normalized = preg_replace('/[\s\-_]+/', '_', $normalized);

        if (preg_match('/less_than_?10[_,.]?000/', $normalized) || preg_match('/less_than_?10k/', $normalized)) {
            return ['max' => 9999];
        }

        if (preg_match('/less_than_?20[_,.]?000/', $normalized) || preg_match('/less_than_?20k/', $normalized)) {
            return ['max' => 19999];
        }

        if (preg_match('/21[_,.]?000_and_above/', $normalized) || preg_match('/21k_and_above/', $normalized) || preg_match('/above_21[_,.]?000/', $normalized)) {
            return ['min' => 21000];
        }

        if (is_numeric($salary)) {
            $num = (float) $salary;
            if ($num < 0) {
                throw ValidationException::withMessages([
                    'salary' => ['Salary filter cannot be negative.'],
                ]);
            }
            return ['max' => $num];
        }

        throw ValidationException::withMessages([
            'salary' => ["Invalid salary filter format: '$salary'."],
        ]);
    }

    /**
     * Apply salary filter to query.
     */
    public function applySalaryFilter(Builder $query, ?array $salaryRange): void
    {
        if (!$salaryRange) {
            return;
        }

        if (isset($salaryRange['max']) && $salaryRange['max'] !== null) {
            $query->where('salary', '<=', $salaryRange['max']);
        }

        if (isset($salaryRange['min']) && $salaryRange['min'] !== null) {
            $query->where('salary', '>=', $salaryRange['min']);
        }
    }

    /**
     * Parse expiry window filter.
     *
     * @throws ValidationException
     */
    public function normalizeExpiryWindow(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[\s\-_]+/', '_', $normalized);

        $dayMap = [
            'within_a_day' => 1,
            'withinaday' => 1,
            'within_1_day' => 1,
            'within1day' => 1,
            'within_3_days' => 3,
            'within3days' => 3,
            'within3_days' => 3,
            'within_3_day' => 3,
            'within3day' => 3,
            'within_a_week' => 7,
            'withinaweek' => 7,
            'within_week' => 7,
            'within1week' => 7,
        ];

        if (isset($dayMap[$normalized])) {
            return [
                'from' => now(),
                'to' => now()->addDays($dayMap[$normalized]),
            ];
        }

        if (preg_match('/^within(?:\s+|_)?(?:a|an)?(?:\s+|_)?(\d+)?(?:\s+|_)?(day|days|week|weeks)?$/', $normalized, $matches)) {
            $amount = (int) ($matches[1] ?? 1);
            $unit = $matches[2] ?? 'day';
            $days = ($unit === 'week' || $unit === 'weeks') ? 7 * $amount : $amount;

            if ($days > 0) {
                return [
                    'from' => now(),
                    'to' => now()->addDays($days),
                ];
            }
        }

        throw ValidationException::withMessages([
            'is_expiry' => ["Invalid expiry filter window: '$value'."],
        ]);
    }

    /**
     * Apply expiry window filter to query.
     */
    public function applyExpiryFilter(Builder $query, ?array $expiryWindow): void
    {
        if (!$expiryWindow) {
            return;
        }

        $query->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$expiryWindow['from'], $expiryWindow['to']]);
    }
}
