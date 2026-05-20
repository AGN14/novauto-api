<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VinController extends Controller
{
    public function __construct(private VinService $vinService) {}

    public function decode(Request $request): JsonResponse
    {
        $request->validate([
            'vin' => ['required', 'string', 'size:17'],
        ], [
            'vin.required' => 'Le numéro VIN est obligatoire.',
            'vin.size'     => 'Le numéro VIN doit contenir exactement 17 caractères.',
        ]);

        try {
            $result = $this->vinService->decode($request->vin);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}