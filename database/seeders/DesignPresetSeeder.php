<?php

namespace Database\Seeders;

use App\Models\DesignPreset;
use App\Models\DesignSetting;
use Illuminate\Database\Seeder;

class DesignPresetSeeder extends Seeder
{
    public function run(): void
    {
        $presets = [
            [
                'name'          => 'Default',
                'slug'          => 'default',
                'is_system'     => true,
                'sort_order'    => 0,
                'settings'      => DesignSetting::defaults(),
                'dark_settings' => DesignSetting::darkDefaults(),
            ],
            [
                'name'          => 'Classic Blue',
                'slug'          => 'classic-blue',
                'is_system'     => true,
                'sort_order'    => 1,
                'settings'      => [
                    'bg'           => '#f0f4f8',
                    'surface'      => '#ffffff',
                    'surface2'     => '#e8eef4',
                    'surface3'     => '#d0dbe6',
                    'border'       => '#c0cdd8',
                    'border_light' => '#dce4ec',
                    'text'         => '#1a365d',
                    'text_muted'   => '#4a6785',
                    'text_dim'     => '#7a94ad',
                    'accent'       => '#2b6cb0',
                    'warm'         => '#c07b2a',
                    'rose'         => '#805ad5',
                    'blue'         => '#2b6cb0',
                    'green'        => '#38a169',
                    'radius'       => 4,
                    'radius_lg'    => 6,
                    'font_family'  => 'Inter',
                    'font_size'    => 14,
                ],
                'dark_settings' => [
                    'bg'           => '#0f1a2e',
                    'surface'      => '#162033',
                    'surface2'     => '#1a2840',
                    'surface3'     => '#0d1f3a',
                    'border'       => '#2a3f5a',
                    'border_light' => '#253550',
                    'text'         => '#d4dde8',
                    'text_muted'   => '#8aa0b8',
                    'text_dim'     => '#5a7490',
                    'accent'       => '#5b9bd5',
                    'warm'         => '#d4943a',
                    'rose'         => '#9b7be0',
                    'blue'         => '#5b9bd5',
                    'green'        => '#55b57a',
                    'radius'       => 4,
                    'radius_lg'    => 6,
                    'font_family'  => 'Inter',
                    'font_size'    => 14,
                ],
            ],
            [
                'name'          => 'Earth Tones',
                'slug'          => 'earth-tones',
                'is_system'     => true,
                'sort_order'    => 2,
                'settings'      => [
                    'bg'           => '#faf8f5',
                    'surface'      => '#ffffff',
                    'surface2'     => '#f3efe9',
                    'surface3'     => '#e8e0d5',
                    'border'       => '#d5cbb8',
                    'border_light' => '#e8e0d5',
                    'text'         => '#3d3428',
                    'text_muted'   => '#7a6e5d',
                    'text_dim'     => '#a89c8a',
                    'accent'       => '#8b6f4e',
                    'warm'         => '#b8860b',
                    'rose'         => '#8b5e3c',
                    'blue'         => '#5b7f95',
                    'green'        => '#6b8e4e',
                    'radius'       => 8,
                    'radius_lg'    => 10,
                    'font_family'  => 'Source Sans Pro',
                    'font_size'    => 14,
                ],
                'dark_settings' => [
                    'bg'           => '#1e1a15',
                    'surface'      => '#2a2520',
                    'surface2'     => '#252018',
                    'surface3'     => '#332b20',
                    'border'       => '#4a3f30',
                    'border_light' => '#3d3428',
                    'text'         => '#d8d0c4',
                    'text_muted'   => '#a89c8a',
                    'text_dim'     => '#7a6e5d',
                    'accent'       => '#c09060',
                    'warm'         => '#d4a030',
                    'rose'         => '#b07850',
                    'blue'         => '#7a9eb5',
                    'green'        => '#8aaa60',
                    'radius'       => 8,
                    'radius_lg'    => 10,
                    'font_family'  => 'Source Sans Pro',
                    'font_size'    => 14,
                ],
            ],
        ];

        // Remove old standalone "Dark Mode" preset if it exists
        DesignPreset::where('slug', 'dark')->delete();

        foreach ($presets as $preset) {
            DesignPreset::updateOrCreate(
                ['slug' => $preset['slug']],
                $preset
            );
        }

        // Create the singleton design_settings row if it doesn't exist
        $defaultPreset = DesignPreset::where('slug', 'default')->first();
        DesignSetting::firstOrCreate([], [
            'active_preset_id' => $defaultPreset?->id,
            'settings'         => DesignSetting::defaults(),
            'dark_settings'    => DesignSetting::darkDefaults(),
        ]);
    }
}
