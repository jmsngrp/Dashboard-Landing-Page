<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignPreset;
use App\Models\DesignSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDesignController extends Controller
{
    private array $colorRules = [
        'bg'           => 'required|string|max:20',
        'surface'      => 'required|string|max:20',
        'surface2'     => 'required|string|max:20',
        'surface3'     => 'required|string|max:20',
        'border'       => 'required|string|max:20',
        'border_light' => 'required|string|max:20',
        'text'         => 'required|string|max:20',
        'text_muted'   => 'required|string|max:20',
        'text_dim'     => 'required|string|max:20',
        'accent'       => 'required|string|max:20',
        'warm'         => 'required|string|max:20',
        'rose'         => 'required|string|max:20',
        'blue'         => 'required|string|max:20',
        'green'        => 'required|string|max:20',
        'radius'       => 'required|integer|min:0|max:16',
        'radius_lg'    => 'required|integer|min:0|max:20',
        'font_family'  => 'required|string|in:Lato,Inter,Roboto,Open Sans,Source Sans Pro,system-ui',
        'font_size'    => 'required|integer|min:12|max:18',
    ];

    /**
     * GET /admin/design — Show the design panel.
     */
    public function index()
    {
        $settings = DesignSetting::active();
        $presets = DesignPreset::ordered()->get();

        return view('admin.design.index', compact('settings', 'presets'));
    }

    /**
     * PUT /admin/design — Save the current design settings (light + dark).
     */
    public function update(Request $request)
    {
        // Validate light settings
        $lightValidated = $request->validate($this->colorRules);

        // Validate dark settings (prefixed with dark_)
        $darkRules = [];
        foreach ($this->colorRules as $key => $rule) {
            $darkRules["dark_{$key}"] = $rule;
        }
        $darkValidated = $request->validate($darkRules);

        // Strip dark_ prefix to get the settings array
        $darkSettings = [];
        foreach ($darkValidated as $key => $val) {
            $darkSettings[str_replace('dark_', '', $key)] = $val;
        }

        $settings = DesignSetting::active();
        $settings->update([
            'settings'          => $lightValidated,
            'dark_settings'     => $darkSettings,
            'active_preset_id'  => $request->input('active_preset_id') ?: null,
        ]);

        return redirect()->route('admin.design.index')
            ->with('success', 'Design settings saved.');
    }

    /**
     * POST /admin/design/apply-preset — Apply a preset theme.
     */
    public function applyPreset(Request $request)
    {
        $request->validate(['preset_id' => 'required|exists:design_presets,id']);

        $preset = DesignPreset::findOrFail($request->preset_id);
        $settings = DesignSetting::active();
        $settings->update([
            'settings'          => $preset->settings,
            'dark_settings'     => $preset->dark_settings ?? DesignSetting::darkDefaults(),
            'active_preset_id'  => $preset->id,
        ]);

        return redirect()->route('admin.design.index')
            ->with('success', "Applied preset: {$preset->name}");
    }

    /**
     * POST /admin/design/save-preset — Save current settings as a new preset.
     */
    public function savePreset(Request $request)
    {
        $request->validate(['preset_name' => 'required|string|max:50']);

        $settings = DesignSetting::active();
        $maxSort = DesignPreset::max('sort_order') ?? 0;

        DesignPreset::create([
            'name'          => $request->preset_name,
            'slug'          => Str::slug($request->preset_name) . '-' . Str::random(4),
            'is_system'     => false,
            'settings'      => $settings->settings,
            'dark_settings' => $settings->dark_settings,
            'sort_order'    => $maxSort + 1,
        ]);

        return redirect()->route('admin.design.index')
            ->with('success', 'Preset saved.');
    }

    /**
     * DELETE /admin/design/preset/{preset} — Delete a custom preset.
     */
    public function deletePreset(DesignPreset $preset)
    {
        if ($preset->is_system) {
            return redirect()->route('admin.design.index')
                ->with('error', 'Cannot delete a built-in preset.');
        }

        $preset->delete();

        return redirect()->route('admin.design.index')
            ->with('success', 'Preset deleted.');
    }
}
