<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name'=> 'create-user'],
            ['name'=> 'update-user'],
            ['name'=> 'view-user'],
            ['name'=> 'delete-user'],
            ['name'=> 'view-vacancies'],
            ['name'=> 'view-vacancy'],
            ['name'=> 'create-vacancy'],
            ['name'=> 'update-vacancy'],
            ['name'=> 'delete-vacancy'],
            ['name'=> 'restore-vacancy'],
            ['name'=> 'permanently-delete-vacancy'],
        ];

        DB::table('abilities')->insertOrIgnore($data);
    }
}
