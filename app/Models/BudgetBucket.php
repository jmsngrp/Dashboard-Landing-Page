<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetBucket extends Model
{
    protected $fillable = [
        'name',
        'category',
        'is_summary',
        'summary_formula',
        'semantic_key',
        'sort_order',
        'is_active',
        'legacy_pnl_line_item_id',
    ];

    protected function casts(): array
    {
        return [
            'is_summary' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function amounts(): HasMany
    {
        return $this->hasMany(BudgetBucketAmount::class);
    }

    public function glAccounts(): HasMany
    {
        return $this->hasMany(GlAccount::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSummaries($query)
    {
        return $query->where('is_summary', true);
    }

    public function scopeNonSummary($query)
    {
        return $query->where('is_summary', false);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
