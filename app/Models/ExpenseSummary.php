<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSummary extends Model
{
    protected $table = 'expense_summaries';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'program',
        'admin',
        'fundraising',
        'total',
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
