<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // Appearance Settings
            [
                'key' => 'site_primary_color',
                'value' => '#3b82f6',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Primary color for the application theme'
            ],
            [
                'key' => 'site_secondary_color',
                'value' => '#6b7280',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Secondary color for the application theme'
            ],
            [
                'key' => 'site_success_color',
                'value' => '#10b981',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Success color for notifications and status'
            ],
            [
                'key' => 'site_warning_color',
                'value' => '#f59e0b',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Warning color for alerts and notifications'
            ],
            [
                'key' => 'site_danger_color',
                'value' => '#ef4444',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Danger color for errors and critical actions'
            ],
            [
                'key' => 'site_font_family',
                'value' => 'Poppins',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Primary font family for the application'
            ],
            [
                'key' => 'site_font_size',
                'value' => '14',
                'type' => 'integer',
                'group' => 'appearance',
                'description' => 'Base font size in pixels'
            ],
            [
                'key' => 'site_font_color',
                'value' => '#111827',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Primary font color for text content'
            ],
            [
                'key' => 'site_secondary_font_color',
                'value' => '#6b7280',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Secondary font color for subtitles and descriptions'
            ],
            [
                'key' => 'sidebar_background_color',
                'value' => '#ffffff',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Background color for sidebars and modals'
            ],
            [
                'key' => 'sidebar_header_color',
                'value' => '#f8fafc',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Header background color for sidebars'
            ],
            [
                'key' => 'sidebar_border_color',
                'value' => '#e5e7eb',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'Border color for sidebar elements'
            ],
            
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'PetVax Clinic Management',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Name of the application'
            ],
            [
                'key' => 'site_logo',
                'value' => '/assets/img/logo.png',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Path to the site logo'
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Manila',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default timezone for the application'
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default date format'
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default time format'
            ],
            
            // System Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable maintenance mode'
            ],
            [
                'key' => 'cache_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable application caching'
            ],
            [
                'key' => 'debug_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable debug mode'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
