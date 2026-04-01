<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueSharing extends Model
{
    protected $table = 'revenue_sharing';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'amount',
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
