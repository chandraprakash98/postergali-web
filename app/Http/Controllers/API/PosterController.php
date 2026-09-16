<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PosterPostingLimitService;
use Illuminate\Http\Request;

class PosterController extends Controller
{
    public function __construct(
        protected PosterPostingLimitService $posterLimitService = new PosterPostingLimitService(),
    ) {}

    public function check(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required_without_all:mobile_number,mobile', 'nullable', 'string', 'max:20'],
            'mobile_number' => ['sometimes', 'string', 'max:20'],
            'mobile' => ['sometimes', 'string', 'max:20'],
            'type' => ['sometimes', 'string', 'in:job,jobs,offer,offers,all'],
        ]);

        $phoneNumber = $validated['phone_number']
            ?? $validated['mobile_number']
            ?? $validated['mobile'];
        $requestedType = strtolower((string) ($validated['type'] ?? 'all'));
        $type = in_array($requestedType, ['offer', 'offers'], true) ? 'offer' : 'job';
        $limit = $this->posterLimitService->getDailyLimit();
        $count = $this->posterLimitService->getDailyCount($phoneNumber, $type);
        $remaining = $limit > 0 ? max(0, $limit - $count) : null;
        $canPost = $limit <= 0 || $count < $limit;

        $response = [
            'success' => true,
            'can_post' => $canPost,
            'daily_count' => $count,
            'daily_limit' => $limit,
            'remaining' => $remaining,
            'type' => $requestedType,
        ];

        if (!$canPost) {
            $response['message'] = str_replace(
                ':limit',
                (string) $limit,
                config('posters.error_message', 'Daily poster limit reached. You can only post up to :limit posters per day.')
            );
        }

        return response()->json($response);
    }
}