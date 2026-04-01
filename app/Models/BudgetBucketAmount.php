<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetBucketAmount extends Model
{
    protected $fillable = [
        'budget_bucket_id',
        'area_id',
        'fiscal_year',
        'budget_amount',
        'manual_actual',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'budget_amount'  => 'decimal:2',
            'manual_actual'  => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function bucket(): BelongsTo
    {
        return $this->belongsTo(BudgetBucket::class, 'budget_bucket_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeForYear($query, int $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeForArea($query, int $areaId)
    {
        return $query->where('area_id', $areaId);
    }
}
