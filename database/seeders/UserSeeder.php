<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'role_id' => 1,
                'name' => 'admin',
                'email' => 'admin@softui.com',
                'password' => Hash::make('secret'),
                'clinic_id' => 1,
                'is_verified' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        if (config('app.debug')) {
            $users = array_merge($users, [
                [
                    'id' => 2,
                    'role_id' => 1,
                    'name' => 'Super Admin',
                    'email' => 'superadmin@petvax.test',
                    'password' => Hash::make('password'),
                    'clinic_id' => 1,
                    'is_verified' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 3,
                    'role_id' => 2,
                    'name' => 'Clinic Admin',
                    'email' => 'admin@petvax.test',
                    'password' => Hash::make('password'),
                    'clinic_id' => 1,
                    'is_verified' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 4,
                    'role_id' => 3,
                    'name' => 'Staff',
                    'email' => 'staff@petvax.test',
                    'password' => Hash::make('password'),
                    'clinic_id' => 1,
                    'is_verified' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 5,
                    'role_id' => 4,
                    'name' => 'Veterinarian',
                    'email' => 'vet@petvax.test',
                    'password' => Hash::make('password'),
                    'clinic_id' => 1,
                    'is_verified' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 6,
                    'role_id' => 5,
                    'name' => 'Pet Owner',
                    'email' => 'client@petvax.test',
                    'password' => Hash::make('password'),
                    'clinic_id' => 1,
                    'is_verified' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        }

        DB::table('users')->insert($users);
    }
}
