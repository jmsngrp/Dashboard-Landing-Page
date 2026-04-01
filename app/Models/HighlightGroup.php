<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HighlightGroup extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'color', 'sort_order',
    ];

    public function kpis(): BelongsToMany
    {
        return $this->belongsToMany(HighlightKpi::class, 'highlight_group_kpi')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }
}
