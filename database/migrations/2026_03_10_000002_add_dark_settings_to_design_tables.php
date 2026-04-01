<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add dark_settings column to both tables
        Schema::table('design_presets', function (Blueprint $table) {
            $table->json('dark_settings')->nullable()->after('settings');
        });

        Schema::table('design_settings', function (Blueprint $table) {
            $table->json('dark_settings')->nullable()->after('settings');
        });

        // 2. Data migration: merge "Dark Mode" into "Default" as its dark_settings
        $darkPreset = DB::table('design_presets')->where('slug', 'dark')->first();
        $defaultPreset = DB::table('design_presets')->where('slug', 'default')->first();

        if ($darkPreset && $defaultPreset) {
            // Copy dark preset settings into default's dark_settings
            DB::table('design_presets')
                ->where('id', $defaultPreset->id)
                ->update(['dark_settings' => $darkPreset->settings]);

            // If active design_settings was pointing to dark preset, switch to default
            DB::table('design_settings')
                ->where('active_preset_id', $darkPreset->id)
                ->update([
                    'active_preset_id' => $defaultPreset->id,
                    'settings'         => $defaultPreset->settings,
                    'dark_settings'    => $darkPreset->settings,
                ]);

            // Delete the standalone dark mode preset
            DB::table('design_presets')->where('id', $darkPreset->id)->delete();
        }

        // 3. Seed dark variants for Classic Blue and Earth Tones
        $classicBlue = DB::table('design_presets')->where('slug', 'classic-blue')->first();
        if ($classicBlue) {
            DB::table('design_presets')
                ->where('id', $classicBlue->id)
                ->update([
                    'dark_settings' => json_encode([
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
                    ]),
                    'sort_order' => 1,
                ]);
        }

        $earthTones = DB::table('design_presets')->where('slug', 'earth-tones')->first();
        if ($earthTones) {
            DB::table('design_presets')
                ->where('id', $earthTones->id)
                ->update([
                    'dark_settings' => json_encode([
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
                    ]),
                    'sort_order' => 2,
                ]);
        }

        // 4. Set dark_settings on design_settings if it's using a preset
        $activeSetting = DB::table('design_settings')->first();
        if ($activeSetting && $activeSetting->active_preset_id) {
            $activePreset = DB::table('design_presets')
                ->where('id', $activeSetting->active_preset_id)
                ->first();
            if ($activePreset && $activePreset->dark_settings) {
                DB::table('design_settings')
                    ->where('id', $activeSetting->id)
                    ->update(['dark_settings' => $activePreset->dark_settings]);
            }
        }

        // If no dark_settings on design_settings yet, seed with default dark
        $activeSetting = DB::table('design_settings')->first();
        if ($activeSetting && empty($activeSetting->dark_settings)) {
            DB::table('design_settings')
                ->where('id', $activeSetting->id)
                ->update(['dark_settings' => json_encode([
                    'bg'           => '#1a1a2e',
                    'surface'      => '#16213e',
                    'surface2'     => '#1a1a2e',
                    'surface3'     => '#0f3460',
                    'border'       => '#2a2a4a',
                    'border_light' => '#2a2a4a',
                    'text'         => '#e0e0e0',
                    'text_muted'   => '#a0a0b0',
                    'text_dim'     => '#6a6a7a',
                    'accent'       => '#5ba4d9',
                    'warm'         => '#d4a843',
                    'rose'         => '#a07cc5',
                    'blue'         => '#5ba4d9',
                    'green'        => '#7fb356',
                    'radius'       => 6,
                    'radius_lg'    => 8,
                    'font_family'  => 'Lato',
                    'font_size'    => 14,
                ])]);
        }
    }

    public function down(): void
    {
        Schema::table('design_presets', function (Blueprint $table) {
            $table->dropColumn('dark_settings');
        });

        Schema::table('design_settings', function (Blueprint $table) {
            $table->dropColumn('dark_settings');
        });
    }
};
