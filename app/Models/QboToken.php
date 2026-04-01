<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QboToken extends Model
{
    protected $fillable = [
        'realm_id',
        'access_token',
        'refresh_token',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'company_name',
        'connected_by',
    ];

    protected function casts(): array
    {
        return [
            'access_token'              => 'encrypted',
            'refresh_token'             => 'encrypted',
            'access_token_expires_at'   => 'datetime',
            'refresh_token_expires_at'  => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    // ── Singleton Access ────────────────────────────────────────────

    /**
     * Get the singleton token record (or null if not connected).
     */
    public static function current(): ?self
    {
        return static::first();
    }

    // ── Token Status ────────────────────────────────────────────────

    /**
     * Check if access token is expired or expiring within 5 minutes.
     */
    public function isAccessTokenExpired(): bool
    {
        return $this->access_token_expires_at->subMinutes(5)->isPast();
    }

    /**
     * Check if refresh token is expired (100-day lifetime).
     */
    public function isRefreshTokenExpired(): bool
    {
        return $this->refresh_token_expires_at->isPast();
    }

    /**
     * Check if connection is fully healthy.
     */
    public function isHealthy(): bool
    {
        return ! $this->isRefreshTokenExpired();
    }
}
