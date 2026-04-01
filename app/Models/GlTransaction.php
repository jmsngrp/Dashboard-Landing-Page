<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlTransaction extends Model
{
    protected $fillable = [
        'gl_account_id',
        'gl_import_id',
        'area_id',
        'fiscal_year',
        'transaction_date',
        'type',
        'num',
        'name',
        'memo',
        'split_account',
        'amount',
        'balance',
        'memo_area_raw',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class);
    }

    public function glImport(): BelongsTo
    {
        return $this->belongsTo(GlImport::class);
    }

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
