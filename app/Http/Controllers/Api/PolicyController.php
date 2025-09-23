<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Get all active policies
     */
    public function index(): JsonResponse
    {
        $policies = Policy::active()->orderBy('type')->get();

        return response()->json([
            'message' => 'Policies retrieved successfully.',
            'data' => $policies,
            'code' => 200,
            'success' => true
        ]);
    }

    /**
     * Get specific policy by type (privacy, terms, return, shipping, warranty...)
     */
    public function show($type): JsonResponse
    {
        $policies = Policy::active()->ofType($type)->get();

        if ($policies->isEmpty()) {
            return response()->json([
                'message' => "No policies of type '{$type}' found.",
                'data' => [],
                'code' => 404,
                'success' => false
            ], 404);
        }

        return response()->json([
            'message' => "Policies of type '{$type}' retrieved successfully.",
            'data' => $policies,
            'code' => 200,
            'success' => true
        ]);
    }
}
