<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @param string ...$guards
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, string ...$guards): JsonResponse
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if (Auth::user()?->hasTwoFactorEnabled() === false) {
                    return $next($request);
                }

                $request->user()->tokens()->delete();

                $modelAbilities = $request->user()->abilities()->orderBy('name')->get(['name'])->toArray();
                $listAbilities  = [];

                foreach ($modelAbilities as $ability) {
                    $listAbilities[] = $ability['name'];
                }

                $token = $request->user()->createToken('apiToken', $listAbilities);

                return response()->json(
                    [
                        'id'             => $request->user()->id,
                        'username'       => $request->user()->username,
                        'email'          => $request->user()->email,
                        'token'          => $token->plainTextToken,
                        'is_super_admin' => $request->user()->isAdministrator()
                    ]
                );
            }
        }

        return $next($request);
    }
}
