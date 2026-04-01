<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueSource extends Model
{
    protected $table = 'revenue_sources';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'label',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeForYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeForArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }
}
