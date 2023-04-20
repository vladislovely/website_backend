<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->tokens()->delete();

        $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        $token = $request->user()->createToken('apiToken', $listAbilities)->plainTextToken;

        return response()->json([
            'id' => $request->user()->id,
            'username' => $request->user()->name,
            'email' => $request->user()->email,
            'status' => $request->user()->status,
            'token' => $token,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if (!empty($request->user()->tokens)) {
            $request->user()->tokens()->delete();
        }

        return response()->noContent();
    }
}
