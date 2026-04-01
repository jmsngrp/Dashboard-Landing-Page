<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'organization_id',
        'modules',
        'is_default',
    ];

    protected $casts = [
        'modules'    => 'array',
        'is_default' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get module slugs this role grants access to.
     * Works with both old format (flat array) and new format (object).
     */
    public function moduleSlugs(): array
    {
        $modules = $this->modules ?? [];

        // New format: {"management": ["*"], "fundraising": ["deposits"]}
        if (!empty($modules) && !array_is_list($modules)) {
            return array_keys($modules);
        }

        // Old format: ["management", "fundraising"]
        return $modules;
    }

    /**
     * Get page slugs for a specific module.
     * Returns ["*"] for all pages, or specific page slugs.
     */
    public function pagesForModule(string $module): array
    {
        $modules = $this->modules ?? [];

        // New format
        if (!empty($modules) && !array_is_list($modules)) {
            return $modules[$module] ?? [];
        }

        // Old format: if module exists, grant all pages
        if (in_array($module, $modules, true)) {
            return ['*'];
        }

        return [];
    }

    /**
     * Check if this role grants access to a specific page.
     */
    public function hasPageAccess(string $module, string $page): bool
    {
        $pages = $this->pagesForModule($module);

        return in_array('*', $pages, true) || in_array($page, $pages, true);
    }
}
