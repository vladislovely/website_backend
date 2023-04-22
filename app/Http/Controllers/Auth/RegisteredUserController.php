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
use Illuminate\Validation\ValidationException;

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
                               'name'     => ['required', 'string', 'max:255'],
                               'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                               'password' => ['required', Rules\Password::defaults()],
                           ]);

        $user = User::create([
                                 'name'     => $request->post('name'),
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

        event(new Registered($user));

        $modelAbilities = $user->abilities()->orderBy('name')->get(['name'])->toArray();
        $listAbilities  = [];

        foreach ($modelAbilities as $ability) {
            $listAbilities[] = $ability['name'];
        }

        Log::info($request->user()->name . ' abilities:', $listAbilities);

        $token = $user->createToken('apiToken', $listAbilities);

        Auth::login($user);

        return response()->json(
            [
                'id' => $request->user()->id,
                'username' => $request->user()->name,
                'email' => $request->user()->email,
                'status' => $request->user()->status,
                'token'   => $token->plainTextToken,
                'is_super_admin' => $request->user()->isAdministrator()
            ]
        );
    }
}
