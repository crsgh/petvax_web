<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clinics')->insert([
            [
                'id' => 1,
                'name' => 'Default Clinic',
                'address' => '123 Main Street',
                'contact' => '555-0123',
                'email' => 'clinic@example.com',
                'latitude' => '14.5995',
                'longitude' => '120.9842',
                'status' => 'active',
                'opening_time' => '08:00:00',
                'closing_time' => '18:00:00',
                'operation_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                'description' => 'Default clinic for the system',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
