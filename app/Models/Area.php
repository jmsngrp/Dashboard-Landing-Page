<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Area extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_statewide',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_statewide' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function pnlValues(): HasMany
    {
        return $this->hasMany(PnlValue::class);
    }

    public function missionMetrics(): HasMany
    {
        return $this->hasMany(MissionMetric::class);
    }

    public function missionMonthlyData(): HasMany
    {
        return $this->hasMany(MissionMonthlyData::class);
    }

    public function missionAverages(): HasMany
    {
        return $this->hasMany(MissionAverage::class);
    }

    public function efficiencyMetrics(): HasMany
    {
        return $this->hasMany(EfficiencyMetric::class);
    }

    public function revenueSources(): HasMany
    {
        return $this->hasMany(RevenueSource::class);
    }

    public function expenseSummaries(): HasMany
    {
        return $this->hasMany(ExpenseSummary::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function financialSnapshots(): HasMany
    {
        return $this->hasMany(FinancialSnapshot::class);
    }

    public function revenueSharings(): HasMany
    {
        return $this->hasMany(RevenueSharing::class);
    }

    public function localFundraisings(): HasMany
    {
        return $this->hasMany(LocalFundraising::class);
    }

    public function glTransactions(): HasMany
    {
        return $this->hasMany(GlTransaction::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(AreaAlias::class);
    }

    public function startingCashBalance(): HasOne
    {
        return $this->hasOne(StartingCashBalance::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_areas');
    }

    public function budgetBucketAmounts(): HasMany
    {
        return $this->hasMany(BudgetBucketAmount::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
