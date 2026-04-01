<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'organization_id', 'is_active', 'qbo_account_id', 'qbo_account_name'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
