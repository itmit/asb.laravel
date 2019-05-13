<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('x5410041'),
        ]);

        $representative = User::create([
            'name' => 'repr',
            'email' => 'repr@gmail.com',
            'password' => bcrypt('x5410041'),
        ]);

        $adminRole = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Главный администратор',
            'description' => 'Пользователь, который имеет все права.'
        ])->attachPermission(
            Permission::create([
                'name' => 'create-representative',
                'display_name' => 'Создовать представителей.',
                'description' => 'Разрешает пользователю создовать представителей.'
            ])
        );

        $admin->attachRole($adminRole);

        $representativeRole = Role::create([
            'name' => 'representative',
            'display_name' => 'Представитель',
            'description' => 'Пользователь может управлять и редактировать диспетчеров.'
        ])->attachPermission(
            Permission::create([
                'name' => 'create-dispatcher',
                'display_name' => 'Создовать диспетчеров',
                'description' => 'Разрешает пользователю создовать диспетчеров.'
            ])
        );

        $representative->attchRole($representativeRole);

        Role::create([
            'name' => 'dispatcher',
            'display_name' => 'Диспетчер',
            'description' => 'Пользователь, который ответственен за принятие заявок.'
        ])->attachPermission(
            Permission::create([
                'name' => 'accept-applications',
                'display_name' => 'Принимать заявки',
                'description' => 'Разрешает пользователю принимать заявки, поступающие от клиентов.'
            ])
        );

    }
}
