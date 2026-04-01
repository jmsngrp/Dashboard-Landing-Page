<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignPreset extends Model
{
    protected $fillable = ['name', 'slug', 'is_system', 'settings', 'dark_settings', 'sort_order'];

    protected function casts(): array
    {
        return [
            'settings'      => 'array',
            'dark_settings' => 'array',
            'is_system'     => 'boolean',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
