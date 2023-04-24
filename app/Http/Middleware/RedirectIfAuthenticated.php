<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): JsonResponse
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $request->user()->tokens()->delete();

                $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
                $listAbilities = [];

                foreach ($modelAbilities as $ability) {
                    $listAbilities[] = $ability['name'];
                }

                $token = $request->user()->createToken('apiToken', $listAbilities);

                return response()->json([
                                            'id' => $request->user()->id,
                                            'username' => $request->user()->name,
                                            'email' => $request->user()->email,
                                            'status' => $request->user()->status,
                                            'token' => $token->plainTextToken,
                                        ]);
            }
        }

        return $next($request);
    }
}
