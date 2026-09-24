<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactUs::create($data);

        return response()->json([
            'message' => 'Thank you for contacting PosterGali. Our team will get back to you within 24 hours.',
        ], 201);
    }
}