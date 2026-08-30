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
        'latitude', 'longitude', 'radius', 'distance', 'per_page', 'page',
        'sub_categories', 'sub_category', 'category', 'categories',
        'is_expiry', 'expiry',
        'job_type', 'job_types',
        'salary', 'min_salary', 'max_salary'
    ];

    public const ALLOWED_JOB_SEARCH_PARAMS = [
        'device_id', 'latitude', 'longitude', 'radius', 'distance', 'phone_number', 'mobile_number',
        'per_page', 'page',
        'sub_categories', 'sub_category', 'category', 'categories',
        'is_expiry', 'expiry',
        'job_type', 'job_types',
        'salary', 'min_salary', 'max_salary'
    ];

    public const ALLOWED_OFFER_INDEX_PARAMS = [
        'latitude', 'longitude', 'radius', 'distance', 'min_radius', 'max_radius', 'min_distance', 'max_distance', 'per_page', 'page',
        'sub_categories', 'sub_category', 'category', 'categories',
        'is_expiry', 'expiry',
        'offer_type', 'offer_types'
    ];

    public const ALLOWED_OFFER_SEARCH_PARAMS = [
        'device_id', 'latitude', 'longitude', 'radius', 'distance', 'min_radius', 'max_radius', 'min_distance', 'max_distance', 'mobile_number', 'phone_number',
        'per_page', 'page',
        'sub_categories', 'sub_category', 'category', 'categories',
        'is_expiry', 'expiry',
        'offer_type', 'offer_types'
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
        $raw = $request->input('sub_categories', $request->input('sub_category', $request->input('categories', $request->input('category'))));

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

        $stopWords = ['and', 'or', 'the', 'of', 'in', 'at', 'for', 'to', 'a', 'an', 'is', 'by', 'with', 'on', '&'];

        $query->where(function (Builder $q) use ($subCategories, $stopWords, $column) {
            foreach ($subCategories as $phrase) {
                $phrase = trim((string) $phrase);
                if ($phrase === '') {
                    continue;
                }

                // Correct UI typo maintanence -> maintenance
                $cleanedPhrase = str_ireplace('maintanence', 'maintenance', $phrase);

                $words = preg_split('/[\s\/,&]+/', strtolower($cleanedPhrase)) ?: [];
                $words = array_values(array_filter(
                    $words,
                    static fn ($w) => strlen($w) >= 3 && !in_array($w, $stopWords, true)
                ));

                if (stripos($phrase, 'maintan') !== false) {
                    $words[] = 'mainten';
                }

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

        $parts = preg_split('/,/', (string) $raw) ?: [];

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
                $raw = trim((string) $jobType);
                if ($raw === '') {
                    continue;
                }
                $normalized = strtolower(preg_replace('/[\s\-_]+/', '', $raw));
                
                // Matches full_time, full-time, full time, Part-Time, part_time, temporary
                $q->orWhere(DB::raw("REPLACE(REPLACE(REPLACE(LOWER(job_type), '-', ''), '_', ''), ' ', '')"), 'like', '%' . $normalized . '%');
                $q->orWhere(DB::raw('LOWER(job_type)'), 'like', '%' . strtolower($raw) . '%');
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

        $parts = preg_split('/,/', (string) $raw) ?: [];

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
                $raw = trim((string) $offerType);
                if ($raw === '') {
                    continue;
                }
                $normalized = strtolower(preg_replace('/[\s\-_]+/', '', $raw));
                
                $q->orWhere(DB::raw("REPLACE(REPLACE(REPLACE(LOWER(offer_type), '-', ''), '_', ''), ' ', '')"), 'like', '%' . $normalized . '%');
                $q->orWhere(DB::raw('LOWER(offer_type)'), 'like', '%' . strtolower($raw) . '%');
            }
        });
    }

    /**
     * Parse salary filter into numeric min/max range.
     *
     * @throws ValidationException
     */
    public function normalizeSalaryRange(?string $salary, ?string $minSalary = null, ?string $maxSalary = null): ?array
    {
        if ($minSalary !== null || $maxSalary !== null) {
            $range = [];
            if ($minSalary !== null && is_numeric($minSalary)) {
                $range['min'] = (float) $minSalary;
            }
            if ($maxSalary !== null && is_numeric($maxSalary)) {
                $range['max'] = (float) $maxSalary;
            }
            if (!empty($range)) {
                return $range;
            }
        }

        if ($salary === null || trim($salary) === '') {
            return null;
        }

        $normalized = preg_replace('/[₹,\x{20B9}]/u', '', $salary);
        $normalized = strtolower(trim((string) $normalized));
        $normalized = preg_replace('/[\s\-_]+/', '_', $normalized);

        if (preg_match('/less_than_?10[_,.]?000/', $normalized) || preg_match('/less_than_?10k/', $normalized) || preg_match('/under_?10[_,.]?000/', $normalized)) {
            return ['max' => 10000];
        }

        if (preg_match('/less_than_?20[_,.]?000/', $normalized) || preg_match('/less_than_?20k/', $normalized) || preg_match('/under_?20[_,.]?000/', $normalized)) {
            return ['max' => 20000];
        }

        if (preg_match('/21[_,.]?000_and_above/', $normalized) || preg_match('/21k_and_above/', $normalized) || preg_match('/above_21[_,.]?000/', $normalized) || preg_match('/above_20[_,.]?000/', $normalized) || preg_match('/21000\+/', $normalized)) {
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
            '1' => 1,
            'within_a_day' => 1,
            'within_1_day' => 1,
            'within1day' => 1,
            'within_day' => 1,
            'withina_day' => 1,
            'withinaday' => 1,
            '1_day' => 1,
            '1day' => 1,
            '3' => 3,
            'within_3_days' => 3,
            'within3days' => 3,
            'within3_days' => 3,
            'within_3_day' => 3,
            'within3day' => 3,
            '3_days' => 3,
            '3days' => 3,
            '7' => 7,
            'within_a_week' => 7,
            'withinaweek' => 7,
            'within_week' => 7,
            'within1week' => 7,
            'within_7_days' => 7,
            'within7days' => 7,
            '7_days' => 7,
            '7days' => 7,
            'a_week' => 7,
            '1_week' => 7,
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

    /**
     * Parse distance / radius filter into numeric [min, max] range in km.
     * Supports:
     * - "0-5 Km", "5-10 Km", "10-15 Km", "15-20 Km", "0-5", "5-10", "10-15"
     * - "0_5", "5_10", "10_15"
     * - "5" or 5.0 (means 0 to 5 km)
     * - min_distance / max_distance / min_radius / max_radius query params
     *
     * @throws ValidationException
     */
    public function normalizeDistanceRange(
        mixed $distance = null,
        mixed $radius = null,
        mixed $minDistance = null,
        mixed $maxDistance = null,
        mixed $minRadius = null,
        mixed $maxRadius = null
    ): array {
        $min = $minDistance ?? $minRadius;
        $max = $maxDistance ?? $maxRadius;

        if ($min !== null || $max !== null) {
            $minVal = ($min !== null && is_numeric($min)) ? max((float) $min, 0.0) : 0.0;
            $maxVal = ($max !== null && is_numeric($max)) ? (float) $max : 100.0;
            return [
                'min' => $minVal,
                'max' => min(max($maxVal, $minVal > 0 ? $minVal + 0.1 : 0.1), 100.0),
            ];
        }

        $raw = $distance ?? $radius;
        if ($raw === null || trim((string) $raw) === '') {
            return ['min' => 0.0, 'max' => 5.0];
        }

        $str = strtolower(trim((string) $raw));
        $str = str_replace(['km', 'kms', 'k.m.', ' '], '', $str);

        // Handle ranges like "0-5", "5-10", "10-15", "0_5", "5_10", "10_15"
        if (preg_match('/^(\d+(?:\.\d+)?)(?:-|_|to)(\d+(?:\.\d+)?)$/', $str, $matches)) {
            $minVal = max((float) $matches[1], 0.0);
            $maxVal = min(max((float) $matches[2], $minVal), 100.0);
            return [
                'min' => $minVal,
                'max' => $maxVal,
            ];
        }

        if (is_numeric($str)) {
            $maxVal = min(max((float) $str, 0.1), 100.0);
            return [
                'min' => 0.0,
                'max' => $maxVal,
            ];
        }

        // Handle "under 5" / "less than 5"
        if (preg_match('/^(?:under|less_than|below)(\d+(?:\.\d+)?)$/', $str, $matches)) {
            return [
                'min' => 0.0,
                'max' => min((float) $matches[1], 100.0),
            ];
        }

        // Handle "above 10" / "more than 10"
        if (preg_match('/^(?:above|more_than|greater_than)(\d+(?:\.\d+)?)$/', $str, $matches)) {
            $minVal = (float) $matches[1];
            return [
                'min' => $minVal,
                'max' => 100.0,
            ];
        }

        throw ValidationException::withMessages([
            'distance' => ["Invalid distance filter: '$raw'."],
        ]);
    }
}
