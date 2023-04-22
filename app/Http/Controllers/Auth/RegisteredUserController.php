<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ability;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->post('name'),
            'email' => $request->post('email'),
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

        event(new Registered($user));

        $modelAbilities = $user->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        $token = $user->createToken('apiToken', $listAbilities);

        Auth::login($user);

        return response()->json(['token' => $token->plainTextToken, 'user_id' => $user->id, 'status' => $user->status]);
    }
}
