<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlAccount extends Model
{
    protected $fillable = [
        'account_number',
        'account_name',
        'account_type',
        'parent_account_id',
        'depth',
        'pnl_line_item_id',
        'budget_bucket_id',
        'qbo_account_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function pnlLineItem(): BelongsTo
    {
        return $this->belongsTo(PnlLineItem::class);
    }

    public function budgetBucket(): BelongsTo
    {
        return $this->belongsTo(BudgetBucket::class);
    }

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'parent_account_id');
    }

    public function childAccounts(): HasMany
    {
        return $this->hasMany(GlAccount::class, 'parent_account_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GlTransaction::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_account_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('account_number');
    }

    public function scopeMapped($query)
    {
        return $query->whereNotNull('pnl_line_item_id');
    }

    public function scopeUnmapped($query)
    {
        return $query->whereNull('pnl_line_item_id');
    }

    public function scopeMappedToBucket($query)
    {
        return $query->whereNotNull('budget_bucket_id');
    }

    public function scopeUnmappedToBucket($query)
    {
        return $query->whereNull('budget_bucket_id');
    }
}
