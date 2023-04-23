<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (auth('sanctum')->check() === false) {
            return response()->json(
                [
                    'message' => 'Not authorized!'
                ], 401
            );
        }

        $this->authenticate($request, $guards);

        return $next($request);
    }

    protected function redirectTo(Request $request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
