@extends('layouts.admin')
@section('title', 'Design')

@php
    $isCustom = empty($settings->active_preset_id);
    $systemPresets = $presets->where('is_system', true);
    $customPresets = $presets->where('is_system', false);
    $darkSettings = $settings->dark_settings ?? \App\Models\DesignSetting::darkDefaults();
@endphp

@section('content')
<div class="page-header">
    <h1>Design Settings</h1>
</div>

{{-- ═══ Settings Form ═══ --}}
<form method="POST" action="{{ route('admin.design.update') }}" id="designForm">
    @csrf
    @method('PUT')
    <input type="hidden" name="active_preset_id" id="activePresetId" value="{{ $settings->active_preset_id }}">

    {{-- ─── Color Scheme ─── --}}
    <div class="admin-card">
        <h3 style="font-size:0.95rem;font-weight:600;color:var(--text);margin-bottom:1rem;">Color Scheme</h3>

        <div class="scheme-grid">
            {{-- Template Cards --}}
            @foreach($systemPresets as $preset)
            <div class="scheme-card {{ $settings->active_preset_id == $preset->id ? 'selected' : '' }}"
                 data-preset-id="{{ $preset->id }}"
                 data-settings='@json($preset->settings)'
                 data-dark-settings='@json($preset->dark_settings ?? \App\Models\DesignSetting::darkDefaults())'>
                {{-- Split light/dark mini preview --}}
                <div class="scheme-preview-split">
                    <div class="scheme-half scheme-half-light" style="background:{{ $preset->settings['bg'] }};">
                        <div class="scheme-preview-bar">
                            <span style="background:{{ $preset->settings['accent'] }};"></span>
                            <span style="background:{{ $preset->settings['green'] }};"></span>
                        </div>
                        <div class="scheme-preview-lines">
                            <div style="background:{{ $preset->settings['text'] }};width:60%;"></div>
                            <div style="background:{{ $preset->settings['text_muted'] }};width:40%;"></div>
                        </div>
                    </div>
                    @php $dp = $preset->dark_settings ?? \App\Models\DesignSetting::darkDefaults(); @endphp
                    <div class="scheme-half scheme-half-dark" style="background:{{ $dp['bg'] }};">
                        <div class="scheme-preview-bar">
                            <span style="background:{{ $dp['accent'] }};"></span>
                            <span style="background:{{ $dp['green'] }};"></span>
                        </div>
                        <div class="scheme-preview-lines">
                            <div style="background:{{ $dp['text'] }};width:60%;"></div>
                            <div style="background:{{ $dp['text_muted'] }};width:40%;"></div>
                        </div>
                    </div>
                </div>
                <div class="scheme-label">
                    <span class="scheme-name">{{ $preset->name }}</span>
                    <span class="scheme-check">&#10003;</span>
                </div>
            </div>
            @endforeach

            {{-- Custom Card --}}
            <div class="scheme-card {{ $isCustom ? 'selected' : '' }}" data-preset-id="custom">
                <div class="scheme-preview scheme-custom-preview">
                    <div class="scheme-custom-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="13.5" cy="6.5" r="2.5"/>
                            <circle cx="17.5" cy="10.5" r="2.5"/>
                            <circle cx="8.5" cy="7.5" r="2.5"/>
                            <circle cx="6.5" cy="12.5" r="2.5"/>
                            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12c0 1.821.487 3.53 1.338 5L6.5 16.5c1-.667 2.333-.333 3.5.5 1 .714 2.5 1.5 4 1.5s2-.5 2-2"/>
                        </svg>
                    </div>
                </div>
                <div class="scheme-label">
                    <span class="scheme-name">Custom</span>
                    <span class="scheme-check">&#10003;</span>
                </div>
            </div>
        </div>

        {{-- Custom presets row --}}
        @if($customPresets->isNotEmpty())
        <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
            <div style="font-size:0.75rem;font-weight:600;color:var(--text-dim);margin-bottom:8px;">Saved Custom Themes</div>
            <div class="scheme-grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));">
                @foreach($customPresets as $preset)
                <div class="scheme-card scheme-card-sm {{ $settings->active_preset_id == $preset->id ? 'selected' : '' }}"
                     data-preset-id="{{ $preset->id }}"
                     data-settings='@json($preset->settings)'
                     data-dark-settings='@json($preset->dark_settings ?? \App\Models\DesignSetting::darkDefaults())'>
                    <div class="scheme-preview-split" style="height:40px;">
                        <div class="scheme-half" style="background:{{ $preset->settings['bg'] }};">
                            <div class="scheme-preview-bar"><span style="background:{{ $preset->settings['accent'] }};"></span></div>
                        </div>
                        @php $dp2 = $preset->dark_settings ?? \App\Models\DesignSetting::darkDefaults(); @endphp
                        <div class="scheme-half" style="background:{{ $dp2['bg'] }};">
                            <div class="scheme-preview-bar"><span style="background:{{ $dp2['accent'] }};"></span></div>
                        </div>
                    </div>
                    <div class="scheme-label" style="padding:6px 10px;">
                        <span class="scheme-name" style="font-size:0.75rem;">{{ $preset->name }}</span>
                        <form method="POST" action="{{ route('admin.design.delete-preset', $preset) }}" style="margin:0;" class="scheme-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this preset?')" style="background:none;border:none;cursor:pointer;font-size:0.7rem;color:var(--text-dim);padding:0;" title="Delete">&#10005;</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ─── Color Editor (collapsible) ─── --}}
    <div class="admin-card" id="customColorEditor" style="{{ $isCustom ? '' : 'display:none;' }}">
        {{-- Mode tabs --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="mode-tabs">
                <button type="button" class="mode-tab active" data-mode="light">&#9788; Light</button>
                <button type="button" class="mode-tab" data-mode="dark">&#9790; Dark</button>
            </div>
            <span style="font-size:0.75rem;color:var(--text-dim);">Editing colors switches to Custom mode</span>
        </div>

        {{-- Light Mode Inputs --}}
        <div id="lightInputs">
            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Brand Colors</div>
            <div class="form-row" style="margin-bottom:1.2rem;">
                @foreach(['accent' => 'Primary', 'green' => 'Green', 'warm' => 'Gold', 'rose' => 'Purple', 'blue' => 'Chart Blue'] as $key => $label)
                <div class="form-group">
                    <label for="hex_{{ $key }}">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="color_{{ $key }}" value="{{ $settings->settings[$key] ?? '' }}" style="width:40px;height:34px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;">
                        <input type="text" name="{{ $key }}" id="hex_{{ $key }}" class="form-control" value="{{ $settings->settings[$key] ?? '' }}" style="font-family:monospace;font-size:0.82rem;" maxlength="7">
                    </div>
                </div>
                @endforeach
            </div>

            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Text Colors</div>
            <div class="form-row" style="margin-bottom:1.2rem;">
                @foreach(['text' => 'Primary Text', 'text_muted' => 'Muted Text', 'text_dim' => 'Dim Text'] as $key => $label)
                <div class="form-group">
                    <label for="hex_{{ $key }}">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="color_{{ $key }}" value="{{ $settings->settings[$key] ?? '' }}" style="width:40px;height:34px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;">
                        <input type="text" name="{{ $key }}" id="hex_{{ $key }}" class="form-control" value="{{ $settings->settings[$key] ?? '' }}" style="font-family:monospace;font-size:0.82rem;" maxlength="7">
                    </div>
                </div>
                @endforeach
            </div>

            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Surfaces &amp; Borders</div>
            <div class="form-row">
                @foreach(['bg' => 'Background', 'surface' => 'Card Surface', 'surface2' => 'Subtle Surface', 'surface3' => 'Darker Surface', 'border' => 'Border', 'border_light' => 'Light Border'] as $key => $label)
                <div class="form-group">
                    <label for="hex_{{ $key }}">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="color_{{ $key }}" value="{{ $settings->settings[$key] ?? '' }}" style="width:40px;height:34px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;">
                        <input type="text" name="{{ $key }}" id="hex_{{ $key }}" class="form-control" value="{{ $settings->settings[$key] ?? '' }}" style="font-family:monospace;font-size:0.82rem;" maxlength="7">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dark Mode Inputs --}}
        <div id="darkInputs" style="display:none;">
            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Brand Colors</div>
            <div class="form-row" style="margin-bottom:1.2rem;">
                @foreach(['accent' => 'Primary', 'green' => 'Green', 'warm' => 'Gold', 'rose' => 'Purple', 'blue' => 'Chart Blue'] as $key => $label)
                <div class="form-group">
                    <label for="hex_dark_{{ $key }}">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="color_dark_{{ $key }}" value="{{ $darkSettings[$key] ?? '' }}" style="width:40px;height:34px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;">
                        <input type="text" name="dark_{{ $key }}" id="hex_dark_{{ $key }}" class="form-control" value="{{ $darkSettings[$key] ?? '' }}" style="font-family:monospace;font-size:0.82rem;" maxlength="7">
                    </div>
                </div>
                @endforeach
            </div>

            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Text Colors</div>
            <div class="form-row" style="margin-bottom:1.2rem;">
                @foreach(['text' => 'Primary Text', 'text_muted' => 'Muted Text', 'text_dim' => 'Dim Text'] as $key => $label)
                <div class="form-group">
                    <label for="hex_dark_{{ $key }}">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="color_dark_{{ $key }}" value="{{ $darkSettings[$key] ?? '' }}" style="width:40px;height:34px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;">
                        <input type="text" name="dark_{{ $key }}" id="hex_dark_{{ $key }}" class="form-control" value="{{ $darkSettings[$key] ?? '' }}" style="font-family:monospace;font-size:0.82rem;" maxlength="7">
                    </div>
                </div>
                @endforeach
            </div>

            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Surfaces &amp; Borders</div>
            <div class="form-row">
                @foreach(['bg' => 'Background', 'surface' => 'Card Surface', 'surface2' => 'Subtle Surface', 'surface3' => 'Darker Surface', 'border' => 'Border', 'border_light' => 'Light Border'] as $key => $label)
                <div class="form-group">
                    <label for="hex_dark_{{ $key }}">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="color_dark_{{ $key }}" value="{{ $darkSettings[$key] ?? '' }}" style="width:40px;height:34px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;">
                        <input type="text" name="dark_{{ $key }}" id="hex_dark_{{ $key }}" class="form-control" value="{{ $darkSettings[$key] ?? '' }}" style="font-family:monospace;font-size:0.82rem;" maxlength="7">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Save as Custom Preset --}}
        <div style="margin-top:20px;padding-top:14px;border-top:1px solid var(--surface3);">
            <div style="display:flex;gap:8px;align-items:flex-end;">
                <div class="form-group" style="margin:0;flex:1;max-width:260px;">
                    <label for="preset_name" style="font-size:0.75rem;">Save as Reusable Preset</label>
                    <input type="text" name="preset_name" id="preset_name" class="form-control" placeholder="My Theme" form="savePresetForm">
                </div>
                <button type="submit" class="btn btn-sm btn-secondary" form="savePresetForm" style="margin-bottom:0;">Save Preset</button>
            </div>
        </div>
    </div>

    {{-- ─── Typography & Spacing ─── --}}
    <div class="admin-card">
        <h3 style="font-size:0.95rem;font-weight:600;color:var(--text);margin-bottom:1rem;">Typography &amp; Spacing</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="fontFamily">Font Family</label>
                <select name="font_family" id="fontFamily" class="form-control">
                    @foreach(['Lato', 'Inter', 'Roboto', 'Open Sans', 'Source Sans Pro', 'system-ui'] as $font)
                    <option value="{{ $font }}" {{ ($settings->settings['font_family'] ?? 'Lato') === $font ? 'selected' : '' }}>{{ $font }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="fontSize">Base Font Size: <span id="fontSizeVal">{{ $settings->settings['font_size'] ?? 14 }}</span>px</label>
                <input type="range" name="font_size" id="fontSize" min="12" max="18" value="{{ $settings->settings['font_size'] ?? 14 }}" style="width:100%;">
            </div>
            <div class="form-group">
                <label for="radius">Border Radius: <span id="radiusVal">{{ $settings->settings['radius'] ?? 6 }}</span>px</label>
                <input type="range" name="radius" id="radius" min="0" max="16" value="{{ $settings->settings['radius'] ?? 6 }}" style="width:100%;">
            </div>
            <div class="form-group">
                <label for="radiusLg">Large Radius: <span id="radiusLgVal">{{ $settings->settings['radius_lg'] ?? 8 }}</span>px</label>
                <input type="range" name="radius_lg" id="radiusLg" min="0" max="20" value="{{ $settings->settings['radius_lg'] ?? 8 }}" style="width:100%;">
            </div>
        </div>
        {{-- Hidden dark radius/font inputs (mirror light values) --}}
        <input type="hidden" name="dark_radius" id="dark_radius" value="{{ $darkSettings['radius'] ?? 6 }}">
        <input type="hidden" name="dark_radius_lg" id="dark_radius_lg" value="{{ $darkSettings['radius_lg'] ?? 8 }}">
        <input type="hidden" name="dark_font_family" id="dark_font_family" value="{{ $darkSettings['font_family'] ?? 'Lato' }}">
        <input type="hidden" name="dark_font_size" id="dark_font_size" value="{{ $darkSettings['font_size'] ?? 14 }}">
    </div>

    {{-- ─── Live Preview ─── --}}
    <div class="admin-card">
        <h3 style="font-size:0.95rem;font-weight:600;color:var(--text);margin-bottom:1rem;">Live Preview</h3>
        <div id="design-preview" style="padding:20px;border-radius:8px;border:1px solid var(--border);transition:all 0.2s;">
            <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
                <div class="preview-kpi" data-color="accent" style="flex:1;min-width:120px;padding:12px;border-radius:6px;border-left:3px solid;border-color:var(--accent);">
                    <div class="preview-kpi-label" style="font-size:0.7rem;font-weight:500;margin-bottom:4px;">Revenue</div>
                    <div class="preview-kpi-value" style="font-size:1.1rem;font-weight:700;">$124,500</div>
                </div>
                <div class="preview-kpi" data-color="green" style="flex:1;min-width:120px;padding:12px;border-radius:6px;border-left:3px solid;border-color:var(--green);">
                    <div class="preview-kpi-label" style="font-size:0.7rem;font-weight:500;margin-bottom:4px;">Families Served</div>
                    <div class="preview-kpi-value" style="font-size:1.1rem;font-weight:700;">87</div>
                </div>
                <div class="preview-kpi" data-color="warm" style="flex:1;min-width:120px;padding:12px;border-radius:6px;border-left:3px solid;border-color:var(--warm);">
                    <div class="preview-kpi-label" style="font-size:0.7rem;font-weight:500;margin-bottom:4px;">Efficiency</div>
                    <div class="preview-kpi-value" style="font-size:1.1rem;font-weight:700;">92%</div>
                </div>
                <div class="preview-kpi" data-color="rose" style="flex:1;min-width:120px;padding:12px;border-radius:6px;border-left:3px solid;border-color:var(--rose);">
                    <div class="preview-kpi-label" style="font-size:0.7rem;font-weight:500;margin-bottom:4px;">Net Margin</div>
                    <div class="preview-kpi-value" style="font-size:1.1rem;font-weight:700;">15.3%</div>
                </div>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:0.78rem;margin-bottom:16px;">
                <thead><tr>
                    <th class="preview-th" style="text-align:left;padding:8px 10px;font-weight:600;border-bottom:1px solid;">Line Item</th>
                    <th class="preview-th" style="text-align:right;padding:8px 10px;font-weight:600;border-bottom:1px solid;">Budget</th>
                    <th class="preview-th" style="text-align:right;padding:8px 10px;font-weight:600;border-bottom:1px solid;">Actual</th>
                </tr></thead>
                <tbody>
                    <tr class="preview-row"><td style="padding:8px 10px;">Direct Fundraising</td><td style="text-align:right;padding:8px 10px;">$50,000</td><td style="text-align:right;padding:8px 10px;">$52,340</td></tr>
                    <tr class="preview-row"><td style="padding:8px 10px;">Program Expenses</td><td style="text-align:right;padding:8px 10px;">$35,000</td><td style="text-align:right;padding:8px 10px;">$33,200</td></tr>
                    <tr class="preview-total"><td style="padding:8px 10px;font-weight:700;">Net Income</td><td style="text-align:right;padding:8px 10px;font-weight:700;">$15,000</td><td style="text-align:right;padding:8px 10px;font-weight:700;">$19,140</td></tr>
                </tbody>
            </table>
            <div style="display:flex;gap:8px;">
                <button type="button" class="preview-btn-primary" style="padding:6px 14px;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;color:#fff;cursor:default;">Primary Button</button>
                <button type="button" class="preview-btn-secondary" style="padding:6px 14px;background:transparent;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:default;border:1px solid;">Secondary Button</button>
            </div>
        </div>
    </div>

    {{-- ─── Actions ─── --}}
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Design Settings</button>
        <button type="button" class="btn btn-secondary" onclick="resetToDefaults()">Reset to Defaults</button>
    </div>
</form>

{{-- Hidden form for saving presets --}}
<form method="POST" action="{{ route('admin.design.save-preset') }}" id="savePresetForm" style="display:none;">
    @csrf
    <input type="hidden" name="preset_name" id="presetNameHidden">
</form>

<style>
    .scheme-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:12px; }
    @media (max-width:900px) { .scheme-grid { grid-template-columns:repeat(2, 1fr); } }

    .scheme-card { border:2px solid var(--border); border-radius:8px; overflow:hidden; cursor:pointer; transition:all 0.15s ease; }
    .scheme-card:hover { border-color:var(--accent); box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .scheme-card.selected { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-dim); }

    .scheme-preview-split { display:flex; height:68px; overflow:hidden; }
    .scheme-half { flex:1; padding:10px 8px; display:flex; flex-direction:column; justify-content:space-between; }
    .scheme-preview-bar { display:flex; gap:4px; }
    .scheme-preview-bar span { width:16px; height:8px; border-radius:2px; }
    .scheme-preview-lines { display:flex; flex-direction:column; gap:3px; }
    .scheme-preview-lines div { height:3px; border-radius:2px; opacity:0.6; }

    .scheme-preview { height:68px; display:flex; align-items:center; justify-content:center; }
    .scheme-custom-preview { background:linear-gradient(135deg, var(--surface2) 0%, var(--surface3) 100%); }
    .scheme-custom-icon { color:var(--text-muted); opacity:0.6; }
    .scheme-card:hover .scheme-custom-icon, .scheme-card.selected .scheme-custom-icon { opacity:1; color:var(--accent); }

    .scheme-label { display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:var(--surface); border-top:1px solid var(--border); }
    .scheme-name { font-size:0.8rem; font-weight:600; color:var(--text); }
    .scheme-check { display:none; font-size:0.75rem; font-weight:700; color:var(--accent); }
    .scheme-card.selected .scheme-check { display:inline; }
    .scheme-delete { display:none; }
    .scheme-card:hover .scheme-delete { display:inline; }
    .scheme-card-sm .scheme-preview-split { height:40px; }

    /* Mode tabs */
    .mode-tabs { display:flex; gap:2px; background:var(--surface2); border-radius:6px; padding:2px; }
    .mode-tab { padding:6px 16px; font-size:0.8rem; font-weight:600; border:none; border-radius:5px; cursor:pointer;
                background:transparent; color:var(--text-muted); font-family:'Lato',sans-serif; transition:all 0.15s; }
    .mode-tab.active { background:var(--surface); color:var(--text); box-shadow:0 1px 3px rgba(0,0,0,0.08); }
    .mode-tab:hover:not(.active) { color:var(--text); }

    /* Preview */
    #design-preview .preview-kpi { background:var(--surface); border:1px solid var(--border); }
    #design-preview .preview-kpi-label { color:var(--text-muted); }
    #design-preview .preview-kpi-value { color:var(--text); }
    #design-preview .preview-th { color:var(--text-muted); background:var(--surface2); border-color:var(--border); }
    #design-preview .preview-row td { color:var(--text); border-bottom:1px solid var(--surface2); }
    #design-preview .preview-total td { color:var(--text); background:var(--surface2); border-top:2px solid var(--border); }
    #design-preview .preview-btn-primary { background:var(--accent); }
    #design-preview .preview-btn-secondary { color:var(--text); border-color:var(--border); }
</style>

<script>
(function() {
    var preview = document.getElementById('design-preview');
    var customEditor = document.getElementById('customColorEditor');
    var presetIdInput = document.getElementById('activePresetId');
    var currentMode = 'light'; // tracks which tab is active

    var colorKeys = [
        'bg','surface','surface2','surface3','border','border_light',
        'text','text_muted','text_dim','accent','warm','rose','blue','green'
    ];
    var cssVarMap = { border_light:'border-light', text_muted:'text-muted', text_dim:'text-dim' };

    function hexToRgb(hex) {
        hex = hex.replace('#','');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        return { r:parseInt(hex.substring(0,2),16), g:parseInt(hex.substring(2,4),16), b:parseInt(hex.substring(4,6),16) };
    }

    function getPrefix() { return currentMode === 'dark' ? 'dark_' : ''; }

    function updatePreview() {
        var prefix = getPrefix();
        colorKeys.forEach(function(key) {
            var hex = document.getElementById('hex_' + prefix + key);
            if (!hex) return;
            var val = hex.value;
            var cssKey = '--' + (cssVarMap[key] || key);
            preview.style.setProperty(cssKey, val);
            if (['accent','warm','rose','blue','green'].indexOf(key) >= 0 && /^#[0-9a-f]{6}$/i.test(val)) {
                var rgb = hexToRgb(val);
                preview.style.setProperty(cssKey + '-dim', 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',0.08)');
            }
        });

        preview.querySelectorAll('.preview-kpi').forEach(function(card) {
            var hex = document.getElementById('hex_' + prefix + card.dataset.color);
            if (hex) card.style.borderLeftColor = hex.value;
        });

        var accentHex = document.getElementById('hex_' + prefix + 'accent');
        if (accentHex) preview.querySelector('.preview-btn-primary').style.background = accentHex.value;

        var fontSelect = document.getElementById('fontFamily');
        var fontVal = fontSelect.value;
        preview.style.fontFamily = fontVal === 'system-ui' ? 'system-ui, -apple-system, sans-serif' : "'" + fontVal + "', sans-serif";

        var fontSize = document.getElementById('fontSize');
        preview.style.fontSize = fontSize.value + 'px';
        document.getElementById('fontSizeVal').textContent = fontSize.value;

        var radius = document.getElementById('radius');
        var radiusLg = document.getElementById('radiusLg');
        preview.style.setProperty('--radius', radius.value + 'px');
        preview.style.setProperty('--radius-lg', radiusLg.value + 'px');
        document.getElementById('radiusVal').textContent = radius.value;
        document.getElementById('radiusLgVal').textContent = radiusLg.value;

        preview.querySelectorAll('.preview-kpi').forEach(function(card) { card.style.borderRadius = radius.value + 'px'; });
        preview.querySelector('.preview-btn-primary').style.borderRadius = radius.value + 'px';
        preview.querySelector('.preview-btn-secondary').style.borderRadius = radius.value + 'px';

        var bgHex = document.getElementById('hex_' + prefix + 'bg');
        if (bgHex) preview.style.background = bgHex.value;
    }

    function fillFormFromSettings(lightSettings, darkSettings) {
        // Fill light inputs
        Object.keys(lightSettings).forEach(function(key) {
            var hexInput = document.getElementById('hex_' + key);
            var colorInput = document.getElementById('color_' + key);
            var rangeMap = { font_size:'fontSize', radius:'radius', radius_lg:'radiusLg' };
            var rangeEl = document.getElementById(rangeMap[key]);
            var select = document.getElementById('fontFamily');

            if (hexInput) { hexInput.value = lightSettings[key]; if (colorInput) colorInput.value = lightSettings[key]; }
            else if (key === 'font_family' && select) select.value = lightSettings[key];
            else if (rangeEl) rangeEl.value = lightSettings[key];
        });

        // Fill dark inputs
        if (darkSettings) {
            Object.keys(darkSettings).forEach(function(key) {
                var hexInput = document.getElementById('hex_dark_' + key);
                var colorInput = document.getElementById('color_dark_' + key);
                if (hexInput) { hexInput.value = darkSettings[key]; if (colorInput) colorInput.value = darkSettings[key]; }
            });
            // Sync hidden dark radius/font
            document.getElementById('dark_radius').value = darkSettings.radius || 6;
            document.getElementById('dark_radius_lg').value = darkSettings.radius_lg || 8;
            document.getElementById('dark_font_family').value = darkSettings.font_family || 'Lato';
            document.getElementById('dark_font_size').value = darkSettings.font_size || 14;
        }

        updatePreview();
    }

    function selectSchemeCard(card) {
        document.querySelectorAll('.scheme-card').forEach(function(c) { c.classList.remove('selected'); });
        card.classList.add('selected');
        var presetId = card.dataset.presetId;

        if (presetId === 'custom') {
            customEditor.style.display = '';
            presetIdInput.value = '';
        } else {
            customEditor.style.display = 'none';
            presetIdInput.value = presetId;
            var ls, ds;
            try { ls = JSON.parse(card.dataset.settings); } catch(e) { return; }
            try { ds = JSON.parse(card.dataset.darkSettings); } catch(e) { ds = null; }
            fillFormFromSettings(ls, ds);
        }
    }

    function switchToCustom() {
        presetIdInput.value = '';
        customEditor.style.display = '';
        document.querySelectorAll('.scheme-card').forEach(function(c) { c.classList.remove('selected'); });
        var customCard = document.querySelector('.scheme-card[data-preset-id="custom"]');
        if (customCard) customCard.classList.add('selected');
    }

    // Scheme card clicks
    document.querySelectorAll('.scheme-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.closest('form.scheme-delete')) return;
            selectSchemeCard(card);
        });
    });

    // Mode tab switching
    document.querySelectorAll('.mode-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.mode-tab').forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            currentMode = tab.dataset.mode;
            document.getElementById('lightInputs').style.display = currentMode === 'light' ? '' : 'none';
            document.getElementById('darkInputs').style.display = currentMode === 'dark' ? '' : 'none';
            updatePreview();
        });
    });

    // Wire up LIGHT color pickers
    colorKeys.forEach(function(key) {
        var colorInput = document.getElementById('color_' + key);
        var hexInput = document.getElementById('hex_' + key);
        if (!colorInput || !hexInput) return;
        colorInput.addEventListener('input', function() { hexInput.value = colorInput.value; updatePreview(); switchToCustom(); });
        hexInput.addEventListener('input', function() {
            if (/^#[0-9a-f]{6}$/i.test(hexInput.value)) colorInput.value = hexInput.value;
            updatePreview(); switchToCustom();
        });
    });

    // Wire up DARK color pickers
    colorKeys.forEach(function(key) {
        var colorInput = document.getElementById('color_dark_' + key);
        var hexInput = document.getElementById('hex_dark_' + key);
        if (!colorInput || !hexInput) return;
        colorInput.addEventListener('input', function() { hexInput.value = colorInput.value; updatePreview(); switchToCustom(); });
        hexInput.addEventListener('input', function() {
            if (/^#[0-9a-f]{6}$/i.test(hexInput.value)) colorInput.value = hexInput.value;
            updatePreview(); switchToCustom();
        });
    });

    // Range sliders
    ['fontSize','radius','radiusLg'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', function() {
            // Sync to dark hidden inputs
            document.getElementById('dark_radius').value = document.getElementById('radius').value;
            document.getElementById('dark_radius_lg').value = document.getElementById('radiusLg').value;
            document.getElementById('dark_font_size').value = document.getElementById('fontSize').value;
            updatePreview();
        });
    });

    document.getElementById('fontFamily').addEventListener('change', function() {
        document.getElementById('dark_font_family').value = this.value;
        updatePreview();
    });

    // Save preset form
    document.getElementById('savePresetForm').addEventListener('submit', function(e) {
        var nameInput = document.querySelector('#preset_name');
        if (!nameInput || !nameInput.value.trim()) { e.preventDefault(); nameInput.focus(); return; }
        document.getElementById('presetNameHidden').value = nameInput.value.trim();
    });

    // Reset to defaults
    window.resetToDefaults = function() {
        var lightDefaults = @json(\App\Models\DesignSetting::defaults());
        var darkDefaults = @json(\App\Models\DesignSetting::darkDefaults());
        fillFormFromSettings(lightDefaults, darkDefaults);
        presetIdInput.value = '';
    };

    updatePreview();
})();
</script>
@endsection
