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
        $user = new User();
        $user->name = 'Administrator';
        $user->email = 'zvoryginvy@sibedge.com';
        $user->password = Hash::make('qwerty132456');
        $user->email_verified_at = now();
        $user->status = 'STATUS_ACTIVE';
        $user->created_at = now();
        $user->updated_at = now();

        if ($user->save()) {
            $abilities = Ability::all();
            $insertList = [];

            foreach ($abilities as $ability) {
                if ($ability instanceof Ability) {
                    $insertList[] = ['user_id' => $user->id, 'ability_id' => $ability->id, 'created_at' => now(), 'updated_at' => now()];
                }
            }

            DB::table('user_abilities')->insert($insertList);
        }
    }
}
