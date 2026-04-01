<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlImport extends Model
{
    protected $fillable = [
        'filename',
        'source',
        'fiscal_year',
        'sync_start_date',
        'sync_end_date',
        'total_rows',
        'matched_rows',
        'unmatched_rows',
        'new_accounts',
        'status',
        'error_log',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'sync_start_date' => 'date',
            'sync_end_date'   => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GlTransaction::class);
    }
}
