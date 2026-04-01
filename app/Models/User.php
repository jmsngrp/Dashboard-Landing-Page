<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'organization_id', 'role_id', 'module_overrides',
        'default_account', 'default_class',
        'is_admin', 'is_active',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_active'         => 'boolean',
            'module_overrides'  => 'array',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function userRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'user_areas');
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->is_admin || $this->role === 'admin';
    }

    public function hasArea(int $areaId): bool
    {
        return $this->isAdmin() || $this->areas()->where('areas.id', $areaId)->exists();
    }

    public function accessibleModules(): array
    {
        $validModules = array_keys(config('erp.modules', []));
        if (empty($validModules)) return [];

        if ($this->isAdmin()) {
            return $validModules;
        }

        $roleModules = $this->userRole?->moduleSlugs() ?? [];
        $overrides   = $this->module_overrides ?? [];

        $modules = array_merge($roleModules, $overrides['add'] ?? []);
        $modules = array_diff($modules, $overrides['remove'] ?? []);

        return array_values(array_intersect(array_unique($modules), $validModules));
    }

    public function accessiblePages(string $module): array
    {
        if ($this->isAdmin()) return ['*'];

        $overrides = $this->module_overrides ?? [];
        if (in_array($module, $overrides['remove'] ?? [], true)) return [];
        if (in_array($module, $overrides['add'] ?? [], true)) return ['*'];

        return $this->userRole?->pagesForModule($module) ?? [];
    }

    public function canAccessPage(string $module, string $page): bool
    {
        $pages = $this->accessiblePages($module);
        return in_array('*', $pages, true) || in_array($page, $pages, true);
    }

    public function getAuthPassword(): string
    {
        return $this->password ?? '';
    }
}
