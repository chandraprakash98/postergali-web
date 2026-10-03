<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Offer;
use App\Models\UgcReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UgcReportController extends Controller
{
    /**
     * Store a report for an existing job or offer poster and notify the moderation inbox.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content_id' => ['required', 'integer', 'min:1'],
            'content_type' => ['required', 'string', 'in:job,offer'],
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:5000'],
            'author_id' => ['nullable', 'string', 'max:255'],
        ]);

        $poster = $data['content_type'] === 'job'
            ? Job::find($data['content_id'])
            : Offer::find($data['content_id']);

        if (! $poster) {
            return response()->json([
                'success' => false,
                'message' => 'The reported poster could not be found.',
            ], 404);
        }

        $report = UgcReport::create($data);

        Mail::raw(
            "New UGC Report Received:\n\n".json_encode($report->only([
                'id',
                'content_id',
                'content_type',
                'reason',
                'details',
                'author_id',
                'created_at',
            ]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            function ($message): void {
                $message->to([
                    'contact@postergali.com',
                    'cp474323@gmail.com',
                ])
                    ->subject('[UGC Report] Objectionable Content Flagged');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully.',
            'report_id' => $report->id,
        ], 201);
    }
}
