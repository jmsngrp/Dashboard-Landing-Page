<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'batch_id', 'user_id', 'campaign_name', 'deposit_date',
        'check_count', 'total_amount', 'payload', 'webhook_status', 'status',
        'qbo_push_status', 'qbo_push_result', 'qbo_pushed_at', 'qbo_push_attempts', 'qbo_push_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'deposit_date' => 'date',
        'total_amount' => 'decimal:2',
        'qbo_push_result' => 'array',
        'qbo_pushed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
