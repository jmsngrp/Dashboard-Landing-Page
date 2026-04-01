<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignSetting extends Model
{
    protected $fillable = ['active_preset_id', 'settings', 'dark_settings'];

    protected function casts(): array
    {
        return [
            'settings'      => 'array',
            'dark_settings' => 'array',
        ];
    }

    public function activePreset(): BelongsTo
    {
        return $this->belongsTo(DesignPreset::class, 'active_preset_id');
    }

    /**
     * Get the singleton active settings row, creating with defaults if needed.
     */
    public static function active(): self
    {
        return static::firstOrCreate([], [
            'settings'      => self::defaults(),
            'dark_settings' => self::darkDefaults(),
        ]);
    }

    /**
     * The default CSS variable values (matching the baked-in :root).
     */
    public static function defaults(): array
    {
        return [
            'bg'           => '#ffffff',
            'surface'      => '#ffffff',
            'surface2'     => '#f7f6f3',
            'surface3'     => '#edece9',
            'border'       => '#e8e8e6',
            'border_light' => '#efefef',
            'text'         => '#27303B',
            'text_muted'   => '#787774',
            'text_dim'     => '#a3a29e',
            'accent'       => '#4a88b0',
            'warm'         => '#b09030',
            'rose'         => '#7b649a',
            'blue'         => '#4a88b0',
            'green'        => '#6b9146',
            'radius'       => 6,
            'radius_lg'    => 8,
            'font_family'  => 'Lato',
            'font_size'    => 14,
        ];
    }

    /**
     * Default dark mode color values.
     */
    public static function darkDefaults(): array
    {
        return [
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
        ];
    }
}
