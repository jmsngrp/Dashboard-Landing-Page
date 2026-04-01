<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionMonthlyData extends Model
{
    protected $table = 'mission_monthly_data';

    protected $fillable = [
        'area_id',
        'fiscal_year',
        'month',
        'families',
        'individuals',
        'matched',
        'intake',
        'hosted_days',
        'hosted_nights',
        'volunteers',
        'active_volunteers',
        'partner_churches',
        'active_hosting',
        'active_friendships',
        'active_coaching',
        'graduations',
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
