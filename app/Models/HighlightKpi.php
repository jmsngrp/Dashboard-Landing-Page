<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HighlightKpi extends Model
{
    protected $fillable = [
        'label', 'key', 'type', 'is_decimal', 'color_class', 'sort_order',
    ];

    protected $casts = [
        'is_decimal' => 'boolean',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(HighlightGroup::class, 'highlight_group_kpi')
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
