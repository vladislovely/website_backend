<?php

namespace App\Http\Controllers;

use App\Models\Ability;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserPermissionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('abilities')->select(['id', 'username', 'email', 'email_verified_at', 'deleted_at', 'created_at', 'updated_at'])->get()->toArray();

        return response()->json($users);
    }

    public function permissionsList(Request $request): JsonResponse
    {
        $permissions = Permission::query()->orderBy('id')->get()->toArray();

        return response()->json($permissions);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $data              = $user;
            $data['abilities'] = $user->abilities->toArray();

            return response()->json($data);
        }

        throw new NotFoundHttpException('Not found user with provided id');
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user instanceof User) {
            $this->authorize('update-user-permissions', $user);

            $request->validate(
                [
                    'permissions' => ['required', 'json'],
                ]
            );

            $permissions       = json_decode($request->post('permissions'), true, 512, JSON_THROW_ON_ERROR);
            $userPermissions   = UserPermissions::where(['user_id' => $id])->with('ability')->get()->toArray();
            $existsPermissions = [];

            foreach ($userPermissions as $permission) {
                $existsPermissions[] = $permission['ability']['name'];
            }

            $removeAbilitiesList = array_diff($existsPermissions, $permissions);
            $addAbility          = array_diff($permissions, $existsPermissions);

            if (!empty($removeAbilitiesList)) {
                return $this->removeAbilities($removeAbilitiesList, $request);
            }

            if (!empty($addAbility)) {
                return $this->addAbilities($addAbility, $request);
            }
        }

        throw new NotFoundHttpException('Not found user with provided id');
    }

    private function removeAbilities(array $list, Request $request): JsonResponse
    {
        try {
            $abilities = Ability::all()->whereIn('name', $list);

            foreach ($abilities as $ability) {
                if ($ability instanceof Ability) {
                    UserPermissions::where(['user_id' => $request->user()->id, 'ability_id' => $ability->id])->delete();
                }
            }

            $token = $this->createNewToken($request);

            $response = [];
            if (!empty($token)) {
                $response['token'] = $token;
            }

            return response()->json(array_merge(['message' => 'Permissions success updated'], $response));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function addAbilities(array $list, Request $request): JsonResponse
    {
        try {
            $abilities = Ability::all()->whereIn('name', $list);

            $newAbilities = [];
            foreach ($abilities as $ability) {
                if ($ability instanceof Ability) {
                    $newAbilities[] = ['user_id' => $request->user()->id, 'ability_id' => $ability->id, 'created_at' => now(), 'updated_at' => now()];
                }
            }

            UserPermissions::insert($newAbilities);

            $askNewToken = $this->createNewToken($request);

            $response = [];
            if (!empty($askNewToken) && !empty($askNewToken['error'])) {
                $response['token'] = $askNewToken['token'];
            }

            return response()->json(array_merge(['message' => 'Permissions success updated'], $response));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function createNewToken(Request $request): array | JsonResponse
    {
        $isExistToken = DB::table('personal_access_tokens')->where(['tokenable_id' => $request->user()->id])->get()->toArray();

        $userPermissions   = UserPermissions::where(['user_id' => $request->user()->id])->with('ability')->get()->toArray();
        $abilities = [];

        foreach ($userPermissions as $permission) {
            $abilities[] = $permission['ability']['name'];
        }

        if (!empty($isExistToken) && !empty($abilities)) {
            try {
                DB::table('personal_access_tokens')->where(['tokenable_id' => $request->user()->id])->delete();

                $token = $request->user()->createToken('apiToken', $abilities)->plainTextToken;

                return ['token' => $token];
            } catch (\Exception $e) {
                return ['error'=> $e->getMessage()];
            }
        }

        return [];
    }
}
