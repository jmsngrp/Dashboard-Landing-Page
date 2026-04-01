<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PnlLineItem extends Model
{
    protected $table = 'pnl_line_items';

    protected $fillable = [
        'label',
        'category',
        'is_total',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_total' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function pnlValues(): HasMany
    {
        return $this->hasMany(PnlValue::class, 'line_item_id');
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
}
