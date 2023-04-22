<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insertOrIgnore(
            [
                'name'              => 'Administrator',
                'email'             => User::ADMIN_MAIL,
                'password'          => Hash::make('qwerty132456'),
                'email_verified_at' => now(),
                'status'            => 'STATUS_ACTIVE',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        $user = User::select('id')->where(['email' => User::ADMIN_MAIL])->first();

        $abilities  = Ability::all();
        $insertList = [];

        foreach ($abilities as $ability) {
            if ($ability instanceof Ability && $user instanceof User) {
                $insertList[] = ['user_id' => $user->id, 'ability_id' => $ability->id, 'created_at' => now(), 'updated_at' => now()];
            }
        }

        if (!empty($insertList)) {
            DB::table('user_abilities')->insertOrIgnore($insertList);
        }
    }
}
