<?php

namespace App\Http\Controllers;

use App\Models\Ability;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use PHPUnit\Metadata\Uses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $trashed = filter_var($request->get('trashed'), FILTER_VALIDATE_BOOLEAN);

        if ($trashed) {
            $users = User::withTrashed()->get()->toArray();

            return response()->json($users);
        }
        $users = User::withoutTrashed()->get()->toArray();

        return response()->json($users);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            return response()->json($user);
        }

        throw new NotFoundHttpException('Not found user with provided id');
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
                    'username'  => ['string', 'max:50'],
                    'email'     => ['email', 'string', 'max:100'],
                ]
            );

            try {
                $user->fill($request->only(['username', 'email']));
                $user->updated_at = Carbon::now();
                $user->save();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found user with provided id');
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

        throw new NotFoundHttpException('Not found user with provided id');
    }

    public function restore(int $id): Response|JsonResponse
    {
        $user = User::withTrashed()->where('id', $id);

        if ($user instanceof User) {
            $this->authorize('recovery-user', $user);

            try {
                User::withTrashed()->where('id', $id)->restore();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found user with provided id');
    }

    /**
     * Delete vacancy throw change status
     */
    public function delete(int $id): Response|JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $this->authorize('delete-user', $user);

            try {
                $user->delete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found user with provided id');
    }
}
