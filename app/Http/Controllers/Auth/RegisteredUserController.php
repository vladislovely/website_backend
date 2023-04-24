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
        $request->validate([
                               'username' => ['required', 'string', 'max:50'],
                               'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                               'password' => ['required', Rules\Password::defaults()],
                           ]);

        $user = User::create([
                                 'username' => $request->post('username'),
                                 'email'    => $request->post('email'),
                                 'password' => Hash::make($request->post('password')),
                             ]);

        if ($user) {
            $abilities = Ability::all()->whereIn('name', User::DEFAULT_ABILITIES);

            $abilitiesList = [];
            foreach ($abilities as $ability) {
                if ($ability instanceof Ability) {
                    $abilitiesList[] = ['user_id' => $user->id, 'ability_id' => $ability->id, 'created_at' => now(), 'updated_at' => now()];
                }
            }

            DB::table('user_abilities')->insert($abilitiesList);
        }

        $modelAbilities = $user->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities  = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        Log::info('Registered new user: ' . $user->username . ' with abilities:', $listAbilities);

        $token = $user->createToken('apiToken', $listAbilities);

        event(new Registered($user));

        Auth::login($user);

        return response()->json(
            [
                'id'             => $user->id,
                'username'       => $user->username,
                'name'           => $user->name,
                'last_name'      => $user->last_name,
                'email'          => $user->email,
                'token'          => $token->plainTextToken,
                'is_super_admin' => $user->isAdministrator()
            ]
        );
    }
}
