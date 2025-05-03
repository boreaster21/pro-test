<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{

    public function store(Store $store): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->favorites()->syncWithoutDetaching([$store->id]);

        return response()->json(['status' => 'added']);
    }

    public function destroy(Store $store): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->favorites()->detach($store->id);

        return response()->json(['status' => 'removed']);
    }
}
