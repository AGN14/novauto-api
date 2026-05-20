<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marque;
use App\Models\Modele;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarqueController extends Controller
{
    public function index(): JsonResponse
    {
        $marques = Marque::orderBy('nom')->get();
        return response()->json($marques);
    }

    public function modeles(int $id): JsonResponse
    {
        $modeles = Modele::where('marque_id', $id)->orderBy('nom')->get();
        return response()->json($modeles);
    }
}