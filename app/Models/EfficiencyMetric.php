<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EfficiencyMetric extends Model
{
    protected $table = 'efficiency_metrics';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'cost_per_individual',
        'cost_per_family',
        'cost_per_hosted',
        'cost_per_intake',
        'cost_per_graduation',
        'cost_per_service_hour',
        'program_cost_ratio',
        'admin_ratio',
        'fundraising_roi',
        'rev_per_volunteer',
        'ind_per_10k_staff',
        'intake_conversion',
        'program_cost',
        'revenue',
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
