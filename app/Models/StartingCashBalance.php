<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StartingCashBalance extends Model
{
    protected $fillable = [
        'area_id',
        'balance',
        'as_of_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'balance'    => 'decimal:2',
            'as_of_date' => 'date',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
