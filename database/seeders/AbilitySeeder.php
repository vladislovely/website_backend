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
            ['name'=> 'create-user', 'friendly' => 'Добавление пользователя'],
            ['name'=> 'update-user', 'friendly' => 'Обновление полей пользователя'],
            ['name'=> 'delete-user', 'friendly' => 'Удаление пользователя'],
            ['name'=> 'recovery-user', 'friendly' => 'Восстановление пользователя'],
            ['name'=> 'permanently-delete-user', 'friendly' => 'Перманентное удаление пользователя'],
            ['name'=> 'create-vacancy', 'friendly' => 'Создание вакансии'],
            ['name'=> 'update-vacancy', 'friendly' => 'Обновление полей вакансии'],
            ['name'=> 'delete-vacancy', 'friendly' => 'Удаление вакансии'],
            ['name'=> 'recovery-vacancy', 'friendly' => 'Восстановление вакансии'],
            ['name'=> 'permanently-delete-vacancy', 'friendly' => 'Перманентное удаление вакансии'],
            ['name'=> 'update-user-permissions', 'friendly' => 'Обновление доступов пользователя'],
            ['name'=> 'create-article', 'friendly' => 'Создание статьи'],
            ['name'=> 'update-article', 'friendly' => 'Обновление полей статьи'],
            ['name'=> 'delete-article', 'friendly' => 'Удаление статьи'],
            ['name'=> 'recovery-article', 'friendly' => 'Восстановление статьи'],
            ['name'=> 'permanently-delete-article', 'friendly' => 'Перманентное удаление статьи'],
        ];

        DB::table('abilities')->insertOrIgnore($data);
    }
}
