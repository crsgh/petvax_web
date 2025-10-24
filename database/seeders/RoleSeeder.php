<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Staff', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Veterinarian', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Pet Owner', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
