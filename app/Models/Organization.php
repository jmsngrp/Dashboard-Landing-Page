<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'is_active', 'is_super_admin', 'icon_base64', 'primary_color', 'heading_font', 'paragraph_font'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(AccountingClass::class);
    }

    public function qboToken(): HasOne
    {
        return $this->hasOne(QboToken::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function donorAccounts(): HasMany
    {
        return $this->hasMany(DonorAccount::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function volunteers(): HasMany
    {
        return $this->hasMany(Volunteer::class);
    }

    public function fundRequests(): HasMany
    {
        return $this->hasMany(FundRequest::class);
    }
}
