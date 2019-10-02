<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@itmit-studio.ru',
            'password' => '$2y$10$qYiU8MseeJF5ingZpExLROT8szDD5FWriVGEzSv.rviPd9K9kenq.',
        ]);

        $representative = User::create([
            'name' => 'repr',
            'email' => 'repr@itmit-studio.ru',
            'password' => '$2y$10$qYiU8MseeJF5ingZpExLROT8szDD5FWriVGEzSv.rviPd9K9kenq.',
        ]);

        $dispASB = User::create([
            'name' => 'dispASB',
            'email' => 'doNotDeleteThis@mail.ru',
            'password' => '$2y$10$TU3LRJYJMmjFRmnyvnG.HegXuMXXK4XfhU65ZacCtbSzP0eT3nW0W',
        ]);

        $adminRole = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Главный администратор',
            'description' => 'Пользователь, который имеет все права.'
        ]);

        $adminRole->attachPermission(
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
        ]);

        $representativeRole->attachPermission(
            Permission::create([
                'name' => 'create-dispatcher',
                'display_name' => 'Создовать диспетчеров',
                'description' => 'Разрешает пользователю создовать диспетчеров.'
            ])
        );

        $representative->attachRole($representativeRole);

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

        DB::commit();

    }
}
