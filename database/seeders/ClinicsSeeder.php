<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinics = [
            [
                'id' => 1,
                'name' => 'PetVax Main Clinic',
                'contact' => '+63 912 345 6789',
                'email' => 'main@petvax.com',
                'address' => '123 Main Street, Makati City, Metro Manila, Philippines',
                'latitude' => '14.5547',
                'longitude' => '121.0244',
                'image' => null,
                'status' => 'active',
                'opening_time' => '08:00:00',
                'closing_time' => '18:00:00',
                'operation_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']),
                'tags' => 'veterinary, pet care, grooming, vaccination',
                'description' => 'Full-service veterinary clinic providing comprehensive pet care services.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'PetVax Branch Clinic',
                'contact' => '+63 917 654 3210',
                'email' => 'branch@petvax.com',
                'address' => '456 Branch Avenue, Quezon City, Metro Manila, Philippines',
                'latitude' => '14.6760',
                'longitude' => '121.0437',
                'image' => null,
                'status' => 'active',
                'opening_time' => '09:00:00',
                'closing_time' => '17:00:00',
                'operation_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                'tags' => 'veterinary, emergency care, surgery',
                'description' => 'Specialized clinic focusing on emergency care and surgical procedures.',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('clinics')->insert($clinics);
    }
}
