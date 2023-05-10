<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ability;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate(
            [
                'username' => ['required', 'string', 'max:50'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', Rules\Password::defaults()],
            ]
        );

        try {
            $user = User::create(
                [
                    'username' => $request->post('username'),
                    'email'    => $request->post('email'),
                    'password' => Hash::make($request->post('password')),
                ]
            );

            $abilities = Ability::all()->whereIn('name', User::DEFAULT_ABILITIES);

            $abilitiesList = [];
            foreach ($abilities as $ability) {
                if ($ability instanceof Ability) {
                    $abilitiesList[] = ['user_id' => $user->id, 'ability_id' => $ability->id, 'created_at' => now(), 'updated_at' => now()];
                }
            }

            DB::table('user_abilities')->insert($abilitiesList);

            Log::info('Registered new user: ' . $user->username . ' with abilities:', $abilitiesList);

            event(new Registered($user));

            Auth::login($user);

            return response()->json(['status' => 'NEED_VERIFY_EMAIL']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
