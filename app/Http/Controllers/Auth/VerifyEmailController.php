<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): JsonResponse | Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            $message = $request->user()->hasTwoFactorEnabled() ? 'Your email is already verified! Please approve yourself by 2FA' : 'Your email is already verified! Please setup 2FA';

            return response()->json(['message' => $message, 'two_factor' => $request->user()->hasTwoFactorEnabled() ? 'NEED_APPROVE_2FA' : 'NEED_SETUP_2FA']);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            $message = $request->user()->hasTwoFactorEnabled() ? 'Congratulations! Your mail is verified! Please approve yourself by 2FA' : 'Congratulations! Your mail is verified! Please setup 2FA';

            return response()->json(['message' => $message, 'two_factor' => $request->user()->hasTwoFactorEnabled() ? 'NEED_APPROVE_2FA' : 'NEED_SETUP_2FA']);
        }

        return response()->noContent();
    }
}
