<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->tokens()->delete();

        $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities  = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        $token = $request->user()->createToken('apiToken', $listAbilities)->plainTextToken;

        return response()->json(
            [
                'id'             => $request->user()->id,
                'username'       => $request->user()->username,
                'email'          => $request->user()->email,
                'token'          => $token,
                'is_super_admin' => $request->user()->isAdministrator()
            ]
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if (!empty($request->user()->tokens)) {
            $request->user()->tokens()->delete();
        }

        return response()->noContent();
    }

    public function prolongate(Request $request): Response
    {
        $request->session()->regenerate();

        return response()->noContent();
    }
}
