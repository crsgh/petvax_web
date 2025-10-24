<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'id' => 1,
                'name' => 'Pet Vaccination',
                'category' => 'vaccination',
                'description' => 'Complete vaccination package for pets',
                'price' => 500.00,
                'duration' => 30,
                'species' => 1, // Dog
                'size' => 'medium',
                'status' => 'active',
                'clinic_id' => 1,
                'home_service' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Pet Grooming',
                'category' => 'grooming',
                'description' => 'Professional pet grooming service',
                'price' => 300.00,
                'duration' => 60,
                'species' => null,
                'size' => 'medium',
                'status' => 'active',
                'clinic_id' => 1,
                'home_service' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Health Checkup',
                'category' => 'checkup',
                'description' => 'Comprehensive health examination',
                'price' => 800.00,
                'duration' => 45,
                'species' => null,
                'size' => 'medium',
                'status' => 'active',
                'clinic_id' => 1,
                'home_service' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
