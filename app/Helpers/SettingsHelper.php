<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    /**
     * Get CSS variables based on settings
     */
    public static function getCssVariables()
    {
        $settings = Setting::getByGroup('appearance');
        
        $cssVariables = [
            '--primary-color' => $settings['site_primary_color'] ?? '#3b82f6',
            '--secondary-color' => $settings['site_secondary_color'] ?? '#6b7280',
            '--success-color' => $settings['site_success_color'] ?? '#10b981',
            '--warning-color' => $settings['site_warning_color'] ?? '#f59e0b',
            '--danger-color' => $settings['site_danger_color'] ?? '#ef4444',
            '--font-family' => $settings['site_font_family'] ?? 'Poppins',
            '--font-size' => ($settings['site_font_size'] ?? 14) . 'px',
            '--font-color' => $settings['site_font_color'] ?? '#111827',
            '--secondary-font-color' => $settings['site_secondary_font_color'] ?? '#6b7280',
            '--sidebar-bg-color' => $settings['sidebar_background_color'] ?? '#ffffff',
            '--sidebar-header-color' => $settings['sidebar_header_color'] ?? '#f8fafc',
            '--sidebar-border-color' => $settings['sidebar_border_color'] ?? '#e5e7eb',
        ];
        
        return $cssVariables;
    }
    
    /**
     * Generate CSS string from variables
     */
    public static function generateCss()
    {
        $variables = self::getCssVariables();
        
        $css = ":root {\n";
        foreach ($variables as $property => $value) {
            $css .= "    {$property}: {$value};\n";
        }
        $css .= "}\n";
        
        return $css;
    }
    
    /**
     * Get specific setting value
     */
    public static function get($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}
