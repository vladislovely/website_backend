<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthenticatedUserController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->validate(
            [
                'email'    => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]
        );

        $user = User::where('email', $request->post('email'))->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Bad credentials'
                ], 401
            );
        }

        $request->authenticate($user);

        $modelAbilities = $user->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities  = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        if (!empty($user->tokens)) {
            $user->tokens()->delete();
        }

        Log::info('Authenticated user: '. $user->username . ' with abilities:', $listAbilities);

        $token = $user->createToken('apiToken', $listAbilities)->plainTextToken;

        return response()->json(
            [
                'id'             => $user->id,
                'username'       => $user->username,
                'name'           => $user->name,
                'last_name'      => $user->last_name,
                'email'          => $user->email,
                'status'         => $user->status,
                'token'          => $token,
                'is_super_admin' => $user->isAdministrator()
            ]
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): Response
    {
        auth()->user()?->tokens()->delete();

        return response()->noContent();
    }
}
