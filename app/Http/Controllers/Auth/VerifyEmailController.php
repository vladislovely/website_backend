<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->noContent();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            $request->user()->update(['status' => UserStatus::getKey(UserStatus::STATUS_VERIFIED)]);
        }

        return response()->noContent();
    }
}
