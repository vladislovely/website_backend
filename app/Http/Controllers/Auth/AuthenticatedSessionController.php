<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequestBasic;
use App\Http\Requests\Auth\RequestConfirmTwoFactor;
use App\Http\Requests\Auth\RequestValidateTwoFactor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequestBasic $request): JsonResponse
    {
        $request->authenticate();

        return response()->json(['status' => $request->user()->hasTwoFactorEnabled() ? 'NEED_APPROVE_2FA' : 'NEED_SETUP_2FA']);
    }

    public function prepareTwoFactor(Request $request): JsonResponse
    {
        if ($request->user()->hasTwoFactorEnabled() === false) {
            $secret = $request->user()->createTwoFactorAuth();

            return response()->json(
                [
                    'qr_code' => $secret->toQr(),
                    'uri'     => $secret->toUri(),
                    'string'  => $secret->toString(),
                    'status' => 'NEED_CONFIRM_2FA',
                ]
            );
        }
        return new JsonResponse(data: [
                                          'message' => 'У пользователя уже включена 2FA',
                                      ], status: 422);
    }

    public function confirmTwoFactor(RequestConfirmTwoFactor $request): JsonResponse
    {
        $request->authenticate();

        $confirmed = $request->user()->confirmTwoFactorAuth($request->string('code'));

        if ($confirmed) {
            $request->session()->regenerate();

            $request->user()->tokens()->delete();

            $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
            $listAbilities = [];

            foreach ($modelAbilities as $ability) {
                $listAbilities[] = $ability['name'];
            }

            $data = [
                'id'             => $request->user()->id,
                'username'       => $request->user()->username,
                'email'          => $request->user()->email,
                'is_super_admin' => $request->user()->isAdministrator(),
                'token'          => $request->user()->createToken('apiToken', $listAbilities)->plainTextToken,
                'recovery_codes' => $request->user()->getRecoveryCodes(),
            ];

            return response()->json($data);
        }

        return new JsonResponse(data: ['message' => 'Пожалуйста, для начала настройте 2FA',], status: 422);
    }

    public function validateTwoFactor(RequestValidateTwoFactor $request): JsonResponse
    {
        if ($request->user()->hasTwoFactorEnabled() === false) {
            return new JsonResponse(data: ['message' => 'Пожалуйста, для начала настройте 2FA'], status: 500);
        }

        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->tokens()->delete();

        $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        $data = [
            'id'                => $request->user()->id,
            'username'          => $request->user()->username,
            'email'             => $request->user()->email,
            'is_super_admin'    => $request->user()->isAdministrator(),
            'token'             => $request->user()->createToken('apiToken', $listAbilities)->plainTextToken,
            'email_verified_at' => $request->user()->email_verified_at,
        ];

        return response()->json($data);
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
}
