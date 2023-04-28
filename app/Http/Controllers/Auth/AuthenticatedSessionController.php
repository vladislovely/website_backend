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

//        $request->session()->regenerate();
//
//        $request->user()->tokens()->delete();
//
//        $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
//        $listAbilities  = [];
//
//        foreach ($modelAbilities as $ability) {
//            $listAbilities[] = $ability['name'];
//        }
//
//        $data = [
//            'id'             => $request->user()->id,
//            'username'       => $request->user()->username,
//            'email'          => $request->user()->email,
//            'is_super_admin' => $request->user()->isAdministrator(),
//            'token'          => $request->user()->createToken('apiToken', $listAbilities)->plainTextToken,
//        ];

        return response()->json(['status' => 'NEED_APPROVE_2FA']);
    }

    public function prepareTwoFactor(Request $request): JsonResponse
    {
        $secret = $request->user()->createTwoFactorAuth();

        return response()->json(
            [
                'qr_code' => $secret->toQr(),
                'uri'     => $secret->toUri(),
                'string'  => $secret->toString(),
            ]
        );
    }

    public function confirmTwoFactor(Request $request): JsonResponse
    {
        $request->validate(
            [
                'code' => 'required|numeric'
            ]
        );

        $activated = $request->user()->confirmTwoFactorAuth($request->post('code'));

        if ($activated) {
            return response()->json(['recovery_codes' => $request->user()->getRecoveryCodes()]);
        }

        return response()->json(['message' => 'Code is invalid. Double check it and try again.']);
    }

    public function validateTwoFactor(Request $request): JsonResponse
    {
        $request->validate(
            [
                'code' => 'required|numeric'
            ]
        );

        $activated = $request->user()->validateTwoFactorCode($request->post('code'));

        if ($activated) {
            return response()->json(['message' => 'Everything is okey!']);
        }

        return response()->json(['message' => 'Code is invalid. Double check it and try again.']);
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
