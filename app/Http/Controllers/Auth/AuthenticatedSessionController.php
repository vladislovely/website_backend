<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequestBasic;
use App\Http\Requests\Auth\RequestConfirmTwoFactor;
use App\Http\Requests\Auth\RequestValidateTwoFactor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequestBasic $request): JsonResponse
    {
        $request->authenticate();

        return response()->json(['status' => 'NEED_APPROVE_2FA']);
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
                ]
            );
        }
        return new JsonResponse(data: [
            'message' => 'У пользователя уже включена 2FA',
        ], status:                    422, json: true);
    }

    public function confirmTwoFactor(RequestConfirmTwoFactor $request): JsonResponse
    {
        $activated = $request->user()->confirmTwoFactorAuth($request->post('code'));

        if ($activated) {
            $request->session()->regenerate();

            $request->user()->tokens()->delete();

            $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
            $listAbilities  = [];

            foreach ($modelAbilities as $ability) {
                $listAbilities[] = $ability['name'];
            }

            $data = [
                'id'             => $request->user()->id,
                'username'       => $request->user()->username,
                'email'          => $request->user()->email,
                'is_super_admin' => $request->user()->isAdministrator(),
                'token'          => $request->user()->createToken('apiToken', $listAbilities)->plainTextToken,
                'recovery_codes' => $request->user()->getRecoveryCodes()
            ];

            return response()->json($data);
        }

        return new JsonResponse(data: ['message' => 'Пожалуйста, для начала настройте 2FA',], status: 422, json: true);
    }

    public function validateTwoFactor(RequestValidateTwoFactor $request): JsonResponse
    {
        $request->authenticate();

        if ($request->user()->hasTwoFactorEnabled() === false) {
            return new JsonResponse(data: ['message' => 'Пожалуйста, для начала настройте 2FA'], status: 500, json: true);
        }

        $validated = $request->user()->validateTwoFactorCode($request->post('code'));

        if ($validated) {
            $request->session()->regenerate();

            $request->user()->tokens()->delete();

            $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
            $listAbilities  = [];

            foreach ($modelAbilities as $ability) {
                $listAbilities[] = $ability['name'];
            }

            $data = [
                'id'             => $request->user()->id,
                'username'       => $request->user()->username,
                'email'          => $request->user()->email,
                'is_super_admin' => $request->user()->isAdministrator(),
                'token'          => $request->user()->createToken('apiToken', $listAbilities)->plainTextToken,
            ];

            return response()->json($data);
        }

        return new JsonResponse(data: ['message' => 'Неверный код. Перепроверьте и попробуйте снова'], status: 422, json: true);
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
