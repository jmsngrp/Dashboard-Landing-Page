<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialSnapshot extends Model
{
    protected $table = 'financial_snapshots';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'equity',
        'net_assets',
        'net_income_bs',
        'staffing_budget',
        'target_reserve',
    ];

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
