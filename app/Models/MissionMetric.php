<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionMetric extends Model
{
    protected $table = 'mission_metrics';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'families_served',
        'individuals_served',
        'avg_monthly_families',
        'hosted_days',
        'hosted_nights',
        'total_hosted',
        'total_volunteers',
        'partner_churches',
        'service_hours',
        'intake',
        'opened',
        'graduations',
        'total_relationships',
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
