<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionAverage extends Model
{
    protected $table = 'mission_averages';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'avg_families',
        'avg_individuals',
        'avg_volunteers',
        'avg_active_volunteers',
        'avg_partner_churches',
        'avg_hosting',
        'avg_friendships',
        'avg_coaching',
        'avg_relationships',
        'unique_families',
        'unique_individuals',
        'total_intake',
        'total_opened',
        'total_matched',
        'total_graduations',
        'total_hosted_days',
        'total_hosted_nights',
        'total_hosted',
        'total_service_hours',
        'dec_families',
        'dec_individuals',
        'dec_volunteers',
        'dec_active_volunteers',
        'dec_partner_churches',
        'dec_hosting',
        'dec_friendships',
        'dec_coaching',
        'dec_relationships',
        'ind_fam_ratio',
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
