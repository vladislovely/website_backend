<?php

namespace App\Http\Controllers;

use App\Models\Ability;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view-users', User::class);
        $trashed = filter_var($request->get('trashed'), FILTER_VALIDATE_BOOLEAN);

        if ($trashed) {
            $vacancies = User::withTrashed()->get()->toArray();

            return response()->json($vacancies);
        }
        $vacancies = User::withoutTrashed()->get()->toArray();

        return response()->json($vacancies);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorize('view-user', User::class);

        $user = User::find($id);

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, Request $request)
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $this->authorize('update-user', $user);

            $request->validate(
                [
                    'name'      => ['string', 'max:50', 'nullable'],
                    'username'  => ['string', 'max:50'],
                    'last_name' => ['string', 'max:50', 'nullable'],
                    'email'     => ['email', 'string', 'max:100'],
                ]
            );

            try {
                $user->fill($request->only(['username', 'name', 'email', 'last_name']));
                $user->updated_at = Carbon::now();
                $user->save();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $this->authorize('permanently-delete-user', $user);

            try {
                $user->forceDelete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function restore(int $id): Response|JsonResponse
    {
        $user = User::withTrashed()->where('id', $id);

        if ($user instanceof User) {
            $this->authorize('restore-vacancy', $user);

            try {
                User::withTrashed()->where('id', $id)->restore();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Delete vacancy throw change status
     */
    public function delete(int $id): Response|JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $this->authorize('delete-vacancy', $user);

            try {
                $user->delete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function changePermissions(int $id, Request $request): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $this->authorize('update-user', $user);

            $request->validate(
                [
                    'permissions' => ['required', 'json'],
                ]
            );

            try {
                $permissions = json_decode($request->post('permissions'), true, 512, JSON_THROW_ON_ERROR);

                $abilities = Ability::all()->whereIn('name', $permissions);
                $abilitiesList = [];
                foreach ($abilities as $ability) {
                    if ($ability instanceof Ability) {
                        $abilitiesList[] = ['user_id' => $user->id, 'ability_id' => $ability->id, 'created_at' => now(), 'updated_at' => now()];
                    }
                }

                \DB::table('user_abilities')->updateOrInsert($abilitiesList);

                return response()->json(['message' => 'Permissions success updated']);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }
}
