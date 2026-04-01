<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $table = 'budgets';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'revenue',
        'individual_donations',
        'church_giving',
        'grant_revenue',
        'foundation_revenue',
        'fundraising_events',
        'institutional',
        'cogs',
        'program_costs',
        'admin_costs',
        'total_expenses',
        'gross_profit',
        'net_operating',
        'rev_sharing',
        'net_revenue',
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
