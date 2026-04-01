<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Safe Families for Children – Wisconsin | Board Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#ffffff;--surface:#ffffff;--surface2:#f7f6f3;--surface3:#edece9;
  --border:#e8e8e6;--border-light:#efefef;
  --text:#27303B;--text-muted:#787774;--text-dim:#a3a29e;
  --accent:#4a88b0;--accent-dim:rgba(107,158,200,0.08);
  --warm:#b09030;--warm-dim:rgba(196,162,75,0.08);
  --rose:#7b649a;--rose-dim:rgba(125,107,157,0.08);
  --blue:#4a88b0;--blue-dim:rgba(107,158,200,0.08);
  --green:#6b9146;--green-dim:rgba(138,170,94,0.08);
  --radius:6px;--radius-lg:8px;
}
html{font-size:14px;scroll-behavior:smooth}
body{font-family:'Lato',sans-serif;background:var(--bg);color:var(--text);line-height:1.6;min-height:100vh}


/* Filter bar */
.filter-bar{background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100}
.filter-inner{max-width:1320px;margin:0 auto;display:flex;flex-direction:column}
.nav-group{display:flex;overflow-x:auto;flex:1}
.nav-btn{padding:0.75rem 1.2rem;font-size:0.8rem;font-weight:500;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:all 0.15s;font-family:'Lato',sans-serif}
.nav-btn:hover{color:var(--text);background:var(--surface2)}
.nav-btn.active{color:var(--accent);border-bottom-color:var(--accent);font-weight:600}

.area-filter{display:none;align-items:center;gap:0;border-top:1px solid var(--border);background:var(--surface2);overflow-x:auto}
.area-filter label{font-size:0.68rem;font-weight:500;color:var(--text-dim);white-space:nowrap;padding:0 0.8rem}
.area-btn{padding:0.45rem 0.9rem;font-size:0.75rem;font-weight:500;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap;transition:all 0.15s;font-family:'Lato',sans-serif;border-bottom:2px solid transparent}
.area-btn:hover{color:var(--text);background:var(--surface2)}
.area-btn.active{color:var(--warm);border-bottom-color:var(--warm);font-weight:600;background:var(--warm-dim)}

.main{max-width:1320px;margin:0 auto;padding:1.5rem 2rem 4rem}
.section{display:none;animation:fadeIn 0.3s ease}.section.active{display:block}
.exec-row:hover{background:var(--surface2)!important}
@media(max-width:768px){#exec-inbox{flex-direction:column!important;min-height:auto!important}#exec-sidebar{width:100%!important;min-width:auto!important;border-right:none!important;border-bottom:1px solid var(--border)}#exec-hero{min-height:400px}}
.sm-sub{display:none}.sm-sub.active{display:block}
.df-sub{display:none}.df-sub.active{display:block}
.df-area-tabs{display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:1rem;overflow-x:auto}
.df-area-tab{padding:0.4rem 0.8rem;font-size:0.72rem;font-weight:500;color:var(--text-dim);background:none;border:none;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:all 0.1s;font-family:"Lato",sans-serif}
.df-area-tab:hover{color:var(--text);background:var(--surface2)}
.df-area-tab.active{color:var(--accent);border-bottom-color:var(--accent);font-weight:600}
.exp-row{display:flex;justify-content:space-between;align-items:center;padding:0.7rem 0;border-bottom:1px solid var(--surface2)}
.exp-row.parent{cursor:pointer;font-weight:600}.exp-row.parent:hover{background:var(--accent-dim);margin:0 -0.5rem;padding-left:0.5rem;padding-right:0.5rem;border-radius:6px}
.exp-row.child{padding-left:1.5rem;font-size:0.88rem;color:var(--text-muted)}
.exp-children{display:none;overflow:hidden}.exp-children.open{display:block}
.exp-toggle{font-size:0.7rem;color:var(--text-dim);transition:transform 0.2s;margin-right:0.4rem}
.exp-children.open+.exp-row .exp-toggle,.exp-row.expanded .exp-toggle{transform:rotate(90deg)}
.area-sub-tab{padding:0.4rem 0.8rem;font-size:0.72rem;font-weight:500;color:var(--text-dim);background:none;border:none;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:all 0.15s;font-family:"Lato",sans-serif}
.area-sub-tab:hover{color:var(--text)}.area-sub-tab.active{color:var(--accent);border-bottom-color:var(--accent);font-weight:600}
.sm-tab{padding:0.75rem 1.2rem;font-size:0.8rem;font-weight:500;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:all 0.15s;font-family:'Lato',sans-serif}
.sm-tab:hover{color:var(--text);background:var(--surface2)}
.sm-tab.active{color:var(--accent);border-bottom-color:var(--accent);font-weight:600}
.kpi-card.kpi-selected{outline:none;transform:translateY(-2px);background:#fff;border:2px solid var(--accent);border-left-width:3px}
.kpi-card.warm.kpi-selected{border-color:var(--warm)}.kpi-card.rose.kpi-selected{border-color:var(--rose)}.kpi-card.blue.kpi-selected{border-color:var(--blue)}.kpi-card.green.kpi-selected{border-color:var(--green)}.kpi-card.accent.kpi-selected{border-color:var(--accent)}
.kpi-card[onclick]{cursor:pointer;transition:all 0.15s;opacity:0.7}
.kpi-card[onclick]:hover{opacity:0.85;transform:translateY(-1px)}
.kpi-card[onclick].kpi-selected{opacity:1}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}

/* KPI */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(185px,1fr));gap:0.8rem;margin-bottom:1.5rem}
.kpi-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:1rem 1.2rem;border-left:3px solid var(--accent)}
.kpi-card.warm{border-left-color:var(--warm)}.kpi-card.rose{border-left-color:var(--rose)}.kpi-card.blue{border-left-color:var(--blue)}.kpi-card.green{border-left-color:var(--green)}.kpi-card.accent{border-left-color:var(--accent)}
.kpi-label{font-size:0.75rem;color:var(--text-muted);margin-bottom:0.3rem;font-weight:500}
.kpi-value{font-size:1.6rem;font-weight:700;color:var(--text)}
.kpi-change{font-size:0.75rem;margin-top:0.2rem;font-weight:500;color:var(--accent)}
.kpi-card.warm .kpi-change{color:var(--warm)}.kpi-card.rose .kpi-change{color:var(--rose)}.kpi-card.blue .kpi-change{color:var(--blue)}.kpi-card.green .kpi-change{color:var(--green)}.kpi-card.accent .kpi-change{color:var(--accent)}

/* Tables */
.table-wrap{overflow-x:auto;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:1.5rem;background:var(--surface)}
td.arw{font-size:0.62rem;font-weight:700;text-align:center;padding:0.2rem 0.15rem;width:35px;min-width:35px;max-width:35px;opacity:0.85;line-height:1;vertical-align:middle;white-space:nowrap}
table{width:100%;border-collapse:collapse;font-size:0.84rem;table-layout:fixed}
thead th{background:var(--surface2);color:var(--text-muted);font-weight:600;font-size:0.72rem;padding:0.7rem 1rem;text-align:right;border-bottom:1px solid var(--border);white-space:nowrap}
thead th:first-child{text-align:left;width:28%}
tbody td{padding:0.65rem 1rem;border-bottom:1px solid var(--surface2);text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}
tbody td:first-child{text-align:left;color:var(--text-muted);font-weight:600;white-space:normal}

tbody tr:hover{background:var(--surface2)}
tbody tr.total-row{background:var(--surface2)}
tbody tr.total-row td{border-top:2px solid var(--border);font-weight:700}
tbody tr.total-row td:first-child{color:var(--accent)}
.gl-drillable{cursor:pointer;transition:background 0.1s}
.gl-drillable:hover{background:var(--accent-dim)!important}
.gl-arrow{font-size:0.6rem;color:var(--accent);opacity:0.5;transition:transform 0.15s;display:inline-block}
.gl-drillable.gl-open .gl-arrow{transform:rotate(90deg);opacity:1}
/* Side-peek panel */
.gl-peek-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.25);z-index:200;opacity:0;transition:opacity 0.2s;pointer-events:none}
.gl-peek-overlay.active{opacity:1;pointer-events:auto}
.gl-peek{position:fixed;top:0;right:0;bottom:0;width:520px;max-width:92vw;background:var(--surface);z-index:201;box-shadow:-4px 0 24px rgba(0,0,0,0.12);transform:translateX(100%);transition:transform 0.25s cubic-bezier(0.4,0,0.2,1);display:flex;flex-direction:column}
.gl-peek.active{transform:translateX(0)}
.gl-peek-header{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.2rem;border-bottom:1px solid var(--border);flex-shrink:0}
.gl-peek-header h3{font-size:0.85rem;font-weight:700;color:var(--accent);margin:0}
.gl-peek-close{background:none;border:none;font-size:1.2rem;color:var(--text-dim);cursor:pointer;padding:0.2rem 0.5rem;border-radius:4px;transition:background 0.1s}
.gl-peek-close:hover{background:var(--surface2);color:var(--text)}
.gl-peek-body{flex:1;overflow-y:auto;padding:0.8rem 1.2rem}
.gl-peek-body table{width:100%;border-collapse:collapse;font-size:0.78rem}
.gl-peek-body th{text-align:left;padding:0.35rem 0.5rem;color:var(--text-dim);font-size:0.68rem;font-weight:600;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--surface)}
.gl-peek-body td{padding:0.35rem 0.5rem;border-bottom:1px solid var(--surface2)}
.gl-acct-row{cursor:pointer;transition:background 0.1s}
.gl-acct-row:hover{background:var(--accent-dim)}
.gl-acct-row.gl-acct-open{background:var(--accent-dim);font-weight:700}
.gl-txn-wrap{background:var(--surface2);border-radius:6px;margin:0.2rem 0 0.6rem;overflow:hidden}
.gl-txn-wrap table{font-size:0.72rem}
.gl-txn-wrap th{background:var(--surface2);font-size:0.6rem}
.gl-txn-wrap td{padding:0.25rem 0.4rem;border-bottom:1px solid var(--surface3)}
.gl-txn-wrap .gl-txn-scroll{max-height:280px;overflow-y:auto}
.gl-loading{text-align:center;padding:1rem;color:var(--text-dim);font-size:0.8rem}
.neg{color:var(--rose)}.pos{color:var(--green)}

.section-title{font-size:1.35rem;font-weight:700;margin-bottom:1.5rem}
.section-desc{font-size:0.85rem;color:var(--text-muted);margin-bottom:1.2rem;max-width:680px}
.area-badge{display:inline-block;background:var(--accent-dim);color:var(--accent);padding:0.15rem 0.6rem;border-radius:4px;font-size:0.75rem;font-weight:700;margin-left:0.5rem;vertical-align:middle}
.area-select-wrap{display:inline-block;position:relative;margin-left:0.5rem;vertical-align:middle}
.area-select-btn{display:inline-flex;align-items:center;gap:0.4rem;background:var(--accent-dim);color:var(--accent);padding:0.35rem 0.7rem;border-radius:6px;font-size:0.78rem;font-weight:600;border:1px solid rgba(74,136,176,0.2);cursor:pointer;font-family:"Lato",sans-serif;transition:all 0.15s;user-select:none}
.area-select-btn:hover{background:rgba(74,136,176,0.1);border-color:var(--accent)}
.area-select-btn svg{width:10px;height:10px;transition:transform 0.2s}
.area-select-wrap.open .area-select-btn svg{transform:rotate(180deg)}
.area-select-drop{display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:200px;background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 4px 16px rgba(0,0,0,0.08);z-index:200;padding:0.3rem 0;max-height:320px;overflow-y:auto}
.area-select-wrap.open .area-select-drop{display:block}
.area-select-item{display:block;width:100%;text-align:left;padding:0.5rem 0.9rem;font-size:0.78rem;font-weight:500;color:var(--text-muted);background:none;border:none;cursor:pointer;font-family:"Lato",sans-serif;transition:all 0.1s}
.area-select-item:hover{background:var(--accent-dim);color:var(--accent)}
.area-select-item.active{color:var(--accent);font-weight:600;background:var(--accent-dim)}

.chart-row{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-bottom:1.5rem}
@media(max-width:900px){.chart-row{grid-template-columns:1fr}}
.chart-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.2rem}
.chart-box h3{font-size:1.2rem;font-weight:600;margin-bottom:0.8rem;color:var(--text)}
.chart-box.full{grid-column:1/-1}

.bar-group{margin-bottom:0.7rem}
.bar-label{font-size:0.76rem;color:var(--text-muted);margin-bottom:0.2rem;display:flex;justify-content:space-between;font-weight:600}
.bar-track{height:20px;background:var(--surface3);border-radius:4px;overflow:hidden}
.bar-fill{height:100%;border-radius:4px;transition:width 0.6s cubic-bezier(0.22,1,0.36,1)}
.bar-fill.a{background:var(--accent)}.bar-fill.b{background:var(--warm)}.bar-fill.c{background:var(--blue)}.bar-fill.d{background:var(--green)}.bar-fill.e{background:var(--rose)}.bar-fill.f{background:#7b68a8}.bar-fill.g{background:#5a8c70}

.stacked-bar{display:flex;height:24px;border-radius:4px;overflow:hidden;margin-bottom:0.4rem}
.legend{display:flex;gap:1.2rem;margin-bottom:0.8rem;flex-wrap:wrap}
.legend-item{display:flex;align-items:center;gap:0.35rem;font-size:0.75rem;color:var(--text-muted);font-weight:500}
.legend-dot{width:10px;height:10px;border-radius:2px}

/* Efficiency */
.eff-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;margin-bottom:1.5rem}
.eff-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1rem}
.eff-card h4{font-size:0.88rem;font-weight:700;margin-bottom:0.6rem}
.eff-row{display:flex;justify-content:space-between;align-items:center;padding:0.35rem 0;border-bottom:1px solid var(--surface2);font-size:0.8rem}
.eff-row:last-child{border-bottom:none}
.eff-row .area{color:var(--text-muted);font-weight:600}
.eff-row .vals{display:flex;gap:0.8rem;align-items:center}
.eff-row .v24{min-width:60px;text-align:right;color:var(--text-dim)}
.eff-row .v25{min-width:60px;text-align:right;font-weight:700}
.eff-row .chg{min-width:50px;text-align:right;font-weight:700;font-size:0.72rem;padding:0.1rem 0.4rem;border-radius:4px}
.chg.good{background:var(--green-dim);color:var(--green)}.chg.bad{background:var(--rose-dim);color:var(--rose)}.chg.neutral{background:var(--surface2);color:var(--text-dim)}

.pill{display:inline-block;padding:0.15rem 0.55rem;border-radius:4px;font-size:0.68rem;font-weight:600}
.pill.up{background:var(--green-dim);color:var(--green)}.pill.down{background:var(--rose-dim);color:var(--rose)}

@media print{.filter-bar{position:static}.section{display:block!important;page-break-inside:avoid}}
@media(max-width:600px){.header h1{font-size:1.3rem}.kpi-grid{grid-template-columns:1fr 1fr}.main{padding:1rem}}

</style>
@if(isset($designSettings))
@php
    $defaults = \App\Models\DesignSetting::defaults();
    $varMap = [
        'bg'=>'bg', 'surface'=>'surface', 'surface2'=>'surface2', 'surface3'=>'surface3',
        'border'=>'border', 'border_light'=>'border-light',
        'text'=>'text', 'text_muted'=>'text-muted', 'text_dim'=>'text-dim',
        'accent'=>'accent', 'warm'=>'warm', 'rose'=>'rose', 'blue'=>'blue', 'green'=>'green',
    ];
    $hasOverrides = false;
    $overrideLines = [];
    foreach ($varMap as $settingKey => $cssVar) {
        $val = $designSettings[$settingKey] ?? null;
        $def = $defaults[$settingKey] ?? null;
        if ($val && strtolower($val) !== strtolower($def)) {
            $overrideLines[] = "--{$cssVar}:{$val}";
            $hasOverrides = true;
        }
    }
    // Dim variants for overridden accent colors
    $dimColors = ['accent', 'warm', 'rose', 'blue', 'green'];
    foreach ($dimColors as $colorKey) {
        $val = $designSettings[$colorKey] ?? null;
        $def = $defaults[$colorKey] ?? null;
        if ($val && strtolower($val) !== strtolower($def)) {
            $hex = ltrim($val, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $overrideLines[] = "--{$colorKey}-dim:rgba({$r},{$g},{$b},0.08)";
        }
    }
    // Radius overrides
    $radiusVal = $designSettings['radius'] ?? 6;
    $radiusLgVal = $designSettings['radius_lg'] ?? 8;
    if ((int)$radiusVal !== 6) { $overrideLines[] = "--radius:{$radiusVal}px"; $hasOverrides = true; }
    if ((int)$radiusLgVal !== 8) { $overrideLines[] = "--radius-lg:{$radiusLgVal}px"; $hasOverrides = true; }

    $fontFamily = $designSettings['font_family'] ?? 'Lato';
    $fontSize = $designSettings['font_size'] ?? 14;
@endphp
@if($hasOverrides || count($overrideLines) > 0)
<style>:root{ {!! implode(';', $overrideLines) !!} }</style>
@endif
@if($fontFamily !== 'Lato')
<style>
body{font-family:'{{ $fontFamily }}',sans-serif}
.nav-btn,.area-btn,.sm-tab,.area-select-btn,.area-select-item,.df-area-tab,.area-sub-tab{font-family:'{{ $fontFamily }}',sans-serif}
</style>
@endif
@if((int)$fontSize !== 14)
<style>html{font-size:{{ $fontSize }}px}</style>
@endif
@if($fontFamily !== 'Lato' && $fontFamily !== 'system-ui')
<link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontFamily) }}:wght@400;500;600;700&display=swap" rel="stylesheet">
@endif
@endif
{{-- ── Dark Mode Variables ── --}}
@if(isset($darkDesignSettings) && is_array($darkDesignSettings))
@php
    $darkVarMap = [
        'bg'=>'bg', 'surface'=>'surface', 'surface2'=>'surface2', 'surface3'=>'surface3',
        'border'=>'border', 'border_light'=>'border-light',
        'text'=>'text', 'text_muted'=>'text-muted', 'text_dim'=>'text-dim',
        'accent'=>'accent', 'warm'=>'warm', 'rose'=>'rose', 'blue'=>'blue', 'green'=>'green',
    ];
    $darkLines = [];
    foreach ($darkVarMap as $settingKey => $cssVar) {
        $val = $darkDesignSettings[$settingKey] ?? null;
        if ($val) {
            $darkLines[] = "--{$cssVar}:{$val}";
        }
    }
    $darkDimColors = ['accent', 'warm', 'rose', 'blue', 'green'];
    foreach ($darkDimColors as $colorKey) {
        $val = $darkDesignSettings[$colorKey] ?? null;
        if ($val && preg_match('/^#[0-9a-f]{6}$/i', $val)) {
            $hex = ltrim($val, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $darkLines[] = "--{$colorKey}-dim:rgba({$r},{$g},{$b},0.08)";
        }
    }
    $darkRadius = $darkDesignSettings['radius'] ?? 6;
    $darkRadiusLg = $darkDesignSettings['radius_lg'] ?? 8;
    $darkLines[] = "--radius:{$darkRadius}px";
    $darkLines[] = "--radius-lg:{$darkRadiusLg}px";
@endphp
@if(count($darkLines) > 0)
<style>html[data-theme="dark"]{ {!! implode(';', $darkLines) !!} }</style>
@endif
@endif
<style>
html,body,*{transition:background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease}
.theme-toggle{background:none;border:1px solid var(--border);border-radius:4px;padding:0.3rem 0.6rem;cursor:pointer;color:var(--text-dim);display:flex;align-items:center;gap:4px;font-size:0.7rem;font-family:'Lato',sans-serif;transition:all 0.15s}
.theme-toggle:hover{border-color:var(--accent);color:var(--accent)}
.theme-toggle svg{width:14px;height:14px}
.theme-toggle .icon-sun,html[data-theme="dark"] .theme-toggle .icon-moon{display:none}
html[data-theme="dark"] .theme-toggle .icon-sun{display:block}
</style>
<script>
(function(){var t=localStorage.getItem('sfcwi-theme');if(t==='dark')document.documentElement.setAttribute('data-theme','dark')})();
</script>
</head>
<body>

<div style="padding:2rem 0 1rem;text-align:center;background:var(--surface)"><h1 style="font-size:1.8rem;font-weight:400;color:var(--text);margin:0">Safe Families for Children <span style="color:var(--accent)">WI</span></h1></div>
<div class="filter-bar"><div class="filter-inner">
  <div class="nav-group">
    <button class="nav-btn active" data-s="exec">Highlights</button>
    <button class="nav-btn" data-s="state-mission">Impact</button>
    <button class="nav-btn" data-s="pnl">Financials</button>
    <button class="nav-btn" data-s="impact-cost">Impact Cost</button>
    <div style="margin-left:auto;display:flex;align-items:center;gap:8px;padding:0.5rem">
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
      </button>
      <form method="POST" action="/logout" style="margin:0">
        @csrf
        <button type="submit" style="font-size:0.7rem;color:var(--text-dim);background:none;border:1px solid var(--border);border-radius:4px;padding:0.3rem 0.8rem;cursor:pointer;font-family:'Lato',sans-serif">Logout</button>
      </form>
    </div>
  </div>
  <div class="area-filter">
    <label>Area</label>
    <div id="areaTabs"></div>
  </div>
</div></div>

<main class="main">
<div class="section active" id="exec">
  <h2 class="section-title">Highlights <span style="font-size:0.75rem;font-weight:600;color:var(--accent);margin-left:0.5rem">Year-End 2025</span></h2>
  <div id="exec-inbox" style="display:flex;gap:0;min-height:480px;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--surface)">
    <div id="exec-sidebar" style="width:360px;min-width:300px;border-right:1px solid var(--border);overflow-y:auto;flex-shrink:0"></div>
    <div id="exec-hero" style="flex:1;padding:1.5rem;overflow-y:auto">
      <div id="exec-kpis" class="kpi-grid" style="margin-bottom:1.2rem"></div>
      <h3 id="exec-chart-title" style="font-size:1.2rem;margin-bottom:0.8rem"></h3>
      <div id="exec-chart"></div>
    </div>
  </div>
</div>



<div class="section" id="pnl">
  <h2 class="section-title">Financials <span style="font-size:0.75rem;font-weight:600;color:var(--accent);margin-left:0.5rem">As of 12/31/2025</span></h2>
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:1.2rem">
    <button class="sm-tab active" data-df="overview" onclick="switchDF(this)">Overview</button>
    <button class="sm-tab" data-df="cashflow" onclick="switchDF(this)">Financial Statement</button>
  </div>



  <div class="df-sub active" id="df-overview">
    <div class="kpi-grid" id="ov-kpis"></div>
    <div class="chart-box full" style="margin-top:1.2rem">
      <h3 id="ov-area-title" style="font-size:1.2rem"></h3>
      <div id="ov-area-bars"></div>
    </div>
  </div>
  <div class="df-sub" id="df-cashflow">
    <div class="chart-box full">
      <h3 style="font-size:1.2rem"><span id="df-inline-cashflow" style="display:inline"></span></h3>
      <div id="cashflow-content"></div>
    </div>
  </div>
</div>

<div class="section" id="state-mission">
  <h2 class="section-title">Impact</h2>
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:1.2rem">
    <button class="sm-tab active" data-sm="people" onclick="switchSM(this)">People Served</button>
    <button class="sm-tab" data-sm="vol" onclick="switchSM(this)">Volunteering</button>
    <button class="sm-tab" data-sm="rel" onclick="switchSM(this)">Relationships</button>
    <button class="sm-tab" data-sm="outcomes" onclick="switchSM(this)">Outcomes</button>
    <button class="sm-tab" data-sm="intake" onclick="switchSM(this)">Referrals</button>
  </div>
  <div class="sm-sub active" id="sm-people">
    <div class="kpi-grid" id="sm-people-kpis"></div>
    <div class="chart-box full" style="margin-top:1rem"><h3 id="sm-people-area-title" style="font-size:1.2rem">Avg Monthly Families By Area</h3><div id="sm-people-area"></div></div>
  </div>
  <div class="sm-sub" id="sm-vol">
    <div class="kpi-grid" id="sm-vol-kpis"></div>
    <div class="chart-box full" style="margin-top:1rem"><h3 id="sm-vol-area-title" style="font-size:1.2rem">Total Volunteers (Year-End) By Area</h3><div id="sm-vol-area"></div></div>
  </div>
  <div class="sm-sub" id="sm-rel">
    <div class="kpi-grid" id="sm-rel-kpis"></div>
    <div class="chart-box full" style="margin-top:1rem"><h3 id="sm-rel-area-title" style="font-size:1.2rem">Total Active Relationships By Area</h3><div id="sm-rel-area"></div></div>
  </div>
  <div class="sm-sub" id="sm-outcomes">
    <div class="kpi-grid" id="sm-outcomes-kpis"></div>
    <div class="chart-box full" style="margin-top:1rem"><h3 id="sm-outcomes-area-title" style="font-size:1.2rem">Graduations By Area</h3><div id="sm-outcomes-area"></div></div>
  </div>
  <div class="sm-sub" id="sm-intake">
    <div class="kpi-grid" id="sm-intake-kpis"></div>
    <div class="chart-box full" style="margin-top:1rem"><h3 id="sm-intake-area-title" style="font-size:1.2rem">Total Intakes By Area</h3><div id="sm-intake-area"></div></div>
  </div>
</div>

<div class="section" id="local-mission" style="display:none!important">
  <h2 class="section-title">Area Summary <span class="area-badge" id="lm-badge"></span></h2>
  <div style="display:flex;justify-content:flex-end;margin-bottom:1rem"><button id="lm-share-btn" onclick="shareLmLink()" style="padding:0.4rem 1rem;font-size:0.72rem;font-weight:700;background:var(--accent);color:#fff;border:none;border-radius:6px;cursor:pointer;font-family:Lato,sans-serif">Copy Share Link</button></div>
  <div id="lm-summary"></div>
</div>

<div class="section" id="impact-cost">
  <h2 class="section-title">Impact Cost</h2>
  <div class="kpi-grid" id="ic-kpis"></div>
  <div class="chart-box full" style="margin-top:1.2rem">
    <h3 id="ic-area-title" style="font-size:1.2rem"></h3>
    <div id="ic-area-bars"></div>
  </div>
</div>

</main>

<script>
const D = @json($dashboardData);

const $=s=>document.querySelector(s);
const fmt=n=>n==null?'—':n===0?'':n<0?'-$'+Math.abs(Math.round(n)).toLocaleString():'$'+Math.round(n).toLocaleString();
const fmtK=n=>n==null?'—':n===0?'':Math.abs(n)>=1e6?(n<0?'-':'')+'$'+(Math.abs(n)/1e6).toFixed(2)+'M':Math.abs(n)>=1e3?(n<0?'-':'')+'$'+(Math.abs(n)/1e3).toFixed(1)+'K':n<0?'-$'+Math.abs(Math.round(n)):'$'+Math.round(n);
const fmtN=n=>n==null?'—':Math.round(n).toLocaleString();
const fmtPct=n=>n==null?'—':(n*100).toFixed(1)+'%';
const yoy=(a,b)=>(!a||a===0)?null:(b-a)/Math.abs(a);
const chgCls=(v,lower)=>{if(v==null)return'neutral';if(lower)return v<-0.05?'good':v>0.05?'bad':'neutral';return v>0.05?'good':v<-0.05?'bad':'neutral'};
const pill=(a,b)=>{if(!a||!b||a===0)return'';const c=(b-a)/Math.abs(a);return` <span class="pill ${c>0.02?'up':c<-0.02?'down':''}">${c>0?'+':''}${(c*100).toFixed(0)}%</span>`};
const barColors=['a','b','c','d','e','f','g'];

const areaLabel=n=>n==='WI Statewide'?'Statewide Support':n==='All Areas'?'WI Combined':n==='__local_grouped__'?'Local Sites Combined':n;
let area='All Areas';
let barView='area'; // 'area' or 'grouped'

function buildViewSelector(titleEl,metricLabel){
  const el=$(titleEl);
  if(!el)return;
  const viewLabel=barView==='area'?'By Area':'Local & Statewide Support';
  el.innerHTML=metricLabel+' <span class="area-select-wrap" style="display:inline;vertical-align:baseline;margin-left:0"><button class="area-select-btn" onclick="toggleBarView(this,\''+titleEl.replace('#','')+'\',\''+metricLabel.replace(/'/g,"\\'")+'\');event.stopPropagation()" style="font-size:inherit;padding:0;border:none;background:none;color:var(--accent);font-weight:inherit;cursor:pointer"><span>'+viewLabel+'</span> <svg viewBox="0 0 10 6" width="8" height="8" style="vertical-align:middle;opacity:0.5"><path d="M0 0l5 6 5-6z" fill="currentColor"/></svg></button><div class="area-select-drop" style="min-width:180px"><button class="area-select-item'+(barView==='area'?' active':'')+'" onclick="setBarView(\'area\');event.stopPropagation()">By Area</button><button class="area-select-item'+(barView==='grouped'?' active':'')+'" onclick="setBarView(\'grouped\');event.stopPropagation()">Local & Statewide Support</button></div></span>';
}

window.toggleBarView=function(btn){
  const wrap=btn.closest('.area-select-wrap');
  document.querySelectorAll('.area-select-wrap.open').forEach(w=>{if(w!==wrap)w.classList.remove('open')});
  wrap.classList.toggle('open');
};

window.setBarView=function(v){
  barView=v;
  document.querySelectorAll('.area-select-wrap').forEach(w=>w.classList.remove('open'));
  render();
};


// Populate area tabs
const tabsEl=$('#areaTabs');
// Area dropdown builder for Financial Detail sub-tabs
const dfAreaOptions=[
  {value:'All Areas',label:'WI Combined'},
  {value:'__local__',label:'Local Sites (Excl. Statewide)'},
  ...D.field_areas.map(a=>({value:a,label:areaLabel(a)}))
];

function buildDfInline(containerId,prefix){
  const el=$('#'+containerId);
  if(!el)return;
  el.innerHTML='';
  const currentLabel=area==='__local__'?'Local Sites (Excl. Statewide)':areaLabel(area);
  let html=prefix?'<span style="color:var(--text)">'+prefix+' </span>':'';html+='<span class="area-select-wrap df-area-dd" style="display:inline;vertical-align:baseline;margin-left:0">';
  html+='<button class="area-select-btn" onclick="toggleDfDd(this)" style="font-size:inherit;padding:0;border:none;background:none;color:var(--accent);font-weight:inherit;cursor:pointer">';
  html+='<span class="dd-label">'+currentLabel+'</span> <svg viewBox="0 0 10 6" width="8" height="8" style="vertical-align:middle;opacity:0.5"><path d="M0 0l5 6 5-6z" fill="currentColor"/></svg>';
  html+='</button><div class="area-select-drop dd-drop"></div></span>';
  el.innerHTML=html;
  const drop=el.querySelector('.dd-drop');
  dfAreaOptions.forEach(opt=>{
    const b=document.createElement('button');
    b.className='area-select-item'+(opt.value===area?' active':'');
    b.textContent=opt.label;
    b.onclick=()=>{
      area=opt.value;
      document.querySelectorAll('.df-area-dd .dd-label').forEach(l=>l.textContent=opt.label);
      document.querySelectorAll('.df-area-dd .area-select-item').forEach(x=>{x.classList.remove('active');if(x.textContent===opt.label)x.classList.add('active')});
      document.querySelectorAll('.area-select-wrap').forEach(w=>w.classList.remove('open'));
      render();
    };
    drop.appendChild(b);
  });
}

function buildDfDropdown(containerId){
  const el=$('#'+containerId);
  if(!el)return;
  el.innerHTML='';
  const wrap=document.createElement('div');
  wrap.className='area-select-wrap';
  const currentLabel=area==='__local__'?'Local Sites (Excl. Statewide)':areaLabel(area);
  wrap.innerHTML='<button class="area-select-btn" onclick="toggleDfDd(this)"><span class="dd-label">'+currentLabel+'</span><svg viewBox="0 0 10 6" width="10" height="10"><path d="M0 0l5 6 5-6z" fill="currentColor"/></svg></button><div class="area-select-drop dd-drop"></div>';
  const drop=wrap.querySelector('.dd-drop');
  dfAreaOptions.forEach(opt=>{
    const b=document.createElement('button');
    b.className='area-select-item'+(opt.value===area?' active':'');
    b.textContent=opt.label;
    b.onclick=()=>{
      area=opt.value;
      document.querySelectorAll('.df-area-dd .dd-label').forEach(l=>l.textContent=opt.label);
      document.querySelectorAll('.df-area-dd .area-select-item').forEach(x=>{x.classList.remove('active');if(x.textContent===opt.label)x.classList.add('active')});
      document.querySelectorAll('.area-select-wrap').forEach(w=>w.classList.remove('open'));
      render();
    };
    drop.appendChild(b);
  });
  el.appendChild(wrap);
}

window.toggleDfDd=function(btn){
  const wrap=btn.closest('.area-select-wrap');
  document.querySelectorAll('.area-select-wrap.open').forEach(w=>{if(w!==wrap)w.classList.remove('open')});
  wrap.classList.toggle('open');
};
document.addEventListener('click',e=>{if(!e.target.closest('.area-select-wrap'))document.querySelectorAll('.area-select-wrap.open').forEach(w=>w.classList.remove('open'))});


buildDfInline('df-inline-cashflow','Financial Statement for');


// Share link + URL params
function shareLmLink(){
  const url=new URL(window.location.href);
  url.searchParams.set('area',area);url.searchParams.set('tab','local-mission');
  navigator.clipboard.writeText(url.toString()).then(()=>{
    const btn=$('#lm-share-btn');btn.textContent='Link Copied!';btn.style.background='var(--green)';
    setTimeout(()=>{btn.textContent='Copy Share Link';btn.style.background='var(--accent)'},2000);
  });
}
try{const params=new URLSearchParams(window.location.search);
if(params.get('area')){area=params.get('area');}
if(params.get('tab')){setTimeout(()=>{const btn=document.querySelector('.nav-btn[data-s="'+params.get('tab')+'"]');if(btn)btn.click()},100);}
}catch(e){}

// Populate main area tabs
D.areas.forEach(a=>{const b=document.createElement('button');b.className='area-btn'+(a==='All Areas'?' active':'');b.textContent=areaLabel(a);b.dataset.area=a;b.addEventListener('click',()=>{document.querySelectorAll('.area-btn').forEach(x=>x.classList.remove('active'));b.classList.add('active');area=a;document.querySelectorAll('.df-area-dd .dd-label').forEach(l=>l.textContent=areaLabel(a));render()});tabsEl.appendChild(b)});

// Nav
document.querySelectorAll('.nav-btn').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.nav-btn').forEach(x=>x.classList.remove('active'));b.classList.add('active');
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));$('#'+b.dataset.s).classList.add('active');
  document.querySelector('.area-filter').style.display=(b.dataset.s==='exec'||b.dataset.s==='state-mission'||b.dataset.s==='overview'||b.dataset.s==='pnl'||b.dataset.s==='impact-cost')?'none':'flex';
}));

function render(){
  const renderKpis=(kpis,el,opts)=>{let h='';kpis.forEach((k,i)=>{const dir=k.inv?(k.c>0.01?'down':k.c<-0.01?'up':'flat'):(k.c>0.01?'up':k.c<-0.01?'down':'flat');const click=opts&&opts.onClick?` onclick="${opts.onClick}(${i})"`:'';
h+=`<div class="kpi-card ${k.cl||''}${opts&&opts.onClick&&i===0?' kpi-selected':''}"${click}><div class="kpi-label">${k.l}</div><div class="kpi-value">${k.v}</div><div class="kpi-change ${dir}">${k.c!=null?((dir==='up'?'\u25b2':dir==='down'?'\u25bc':'\u2014')+' '+(k.c>0?'+':'')+(k.c*100).toFixed(1)+'% vs 2024'):(k.sub||'')}</div></div>`;});$(el).innerHTML=h;};

  // Handle __local__ aggregation
  const isLocalView=area==='__local__';
  const a=isLocalView?'All Areas':area;
  const fa=D.field_areas;
  // For local view, we'll filter out WI Statewide in area bars
  document.querySelectorAll('.area-badge').forEach(b=>b.textContent=areaLabel(a));

  // ===== OVERVIEW (always All Areas) =====
  const oa='All Areas';
  const pnl=D.pnl_by_area[oa];
  const inc=pnl[8], prog=pnl[14], admin=pnl[15], opex=pnl[16], net_inc=pnl[18];
  const m24=D.mission['2024'][oa]||{}, m25=D.mission['2025'][oa]||{};

  const rs=D.rev_sharing;
  const rs25=rs['2025'][oa]||0, rs24=rs['2024'][oa]||0;
  const net25=(inc['2025']||0)+rs25, net24=(inc['2024']||0)+rs24;

  const eq=D.equity[oa]||0;
  const b26=D.budget_2026[oa]||{};
  const b26TotalExp=(b26.total_expenses||0)+(b26.cogs||0);
  const b26Rev=b26.revenue||0;
  const monthlyBurn=b26TotalExp/12;
  const reserveMonths=monthlyBurn>0?eq/monthlyBurn:0;
  const reservePct=b26TotalExp>0?eq/b26TotalExp:0;
  const act25Exp=(opex['2025']||0)+(pnl[9]['2025']||0);
  const targetRes=D.target_reserve[oa]||0;
  const reservePctTarget=targetRes>0?eq/targetRes:0;
  const staffing26=D.staffing_2026[oa]||0;
  const staffingMonths=staffing26>0?eq/(staffing26/12):0;
  const growthFunds=eq-targetRes;

  const lf=D.local_fundraising;
  const df25=lf['2025'][oa]||0, df24=lf['2024'][oa]||0;
  const opex25=opex['2025']||0, opex24=opex['2024']||0;

  // Financial area bars renderer
  window.renderFinAreaBars=(metricKey,label,el,barColor)=>{
    let fas;
    if(barView==='grouped'){
      fas=['__local_grouped__','WI Statewide'];
    } else if(isLocalView){
      fas=D.field_areas.filter(ar=>ar!=='WI Statewide');
    } else {
      fas=D.field_areas;
    }
    const barColorMap={'opex':'b','direct_fundraising':'d','gross_revenue':'d','net_income':'e','equity':'c','cogs':'b'};
    const barCls=barColorMap[metricKey]||'d';
    let h='';
    const getGroupedVal=(key,yr)=>{
      const locals=D.field_areas.filter(x=>x!=='WI Statewide');
      return locals.reduce((s,ar)=>{
        const p=D.pnl_by_area[ar];
        if(key==='direct_fundraising')return s+(D.local_fundraising[yr][ar]||0);
        if(key==='net_margin'){const p2=D.pnl_by_area[ar];const g=p2[8][yr]||0;const lf=D.local_fundraising[yr][ar]||0;return s+lf;}
        if(key==='rev_sharing')return s+(D.rev_sharing[yr][ar]||0);
        if(key==='fundraising_roi'){const p2=D.pnl_by_area[ar];return s+(p2[8][yr]||0);}
        if(key==='opex')return s+(p[16][yr]||0);
        if(key==='net_income')return s+(p[18][yr]||0);
        if(key==='equity')return s+(D.equity[ar]||0);
        if(key==='gross_revenue')return s+(p[8][yr]||0);
        if(key==='cogs')return s+(p[9][yr]||0);
        return s;
      },0);
    };
    const getFinVal=(ar,key)=>{
      if(ar==='__local_grouped__')return getGroupedVal(key,'2025');
      const p=D.pnl_by_area[ar];
      if(key==='direct_fundraising') return D.local_fundraising['2025'][ar]||0;
      if(key==='net_margin'){const p2=D.pnl_by_area[ar];const g=p2[8]['2025']||0;const lf=D.local_fundraising['2025'][ar]||0;return g>0?lf/g*100:0;}
      if(key==='rev_sharing'){const v=D.rev_sharing['2025'][ar]||0;return v;}
      if(key==='fundraising_roi'){const p2=D.pnl_by_area[ar];const g=p2[8]['2025']||0;const fc=p2[9]['2025']||0;return fc>0?g/fc:0;}
      if(key==='opex') return p[16]['2025']||0;
      if(key==='net_income') return p[18]['2025']||0;
      if(key==='equity') return D.equity[ar]||0;
      if(key==='gross_revenue') return p[8]['2025']||0;
      if(key==='cogs') return p[9]['2025']||0;
      return 0;
    };
    const getFinVal24=(ar,key)=>{
      if(ar==='__local_grouped__')return getGroupedVal(key,'2024');
      const p=D.pnl_by_area[ar];
      if(key==='direct_fundraising') return D.local_fundraising['2024'][ar]||0;
      if(key==='net_margin'){const p2=D.pnl_by_area[ar];const g=p2[8]['2024']||0;const lf=D.local_fundraising['2024'][ar]||0;return g>0?lf/g*100:0;}
      if(key==='rev_sharing'){return D.rev_sharing['2024'][ar]||0;}
      if(key==='fundraising_roi'){const p2=D.pnl_by_area[ar];const g=p2[8]['2024']||0;const fc=p2[9]['2024']||0;return fc>0?g/fc:0;}
      if(key==='opex') return p[16]['2024']||0;
      if(key==='net_income') return p[18]['2024']||0;
      if(key==='equity') return 0;
      if(key==='gross_revenue') return p[8]['2024']||0;
      if(key==='cogs') return p[9]['2024']||0;
      return 0;
    };
    // Explainer text per metric
    const explainers={
      'direct_fundraising':{shown:'Gross revenue minus fundraising costs, excluding the DOA Grant for like-for-like area comparison. Before revenue sharing.',insight:'Net fundraising revenue by area for 2025, with year-over-year comparison.'},
      'net_margin':{shown:'Net fundraising divided by gross revenue.',insight:'Net fundraising margin by area for 2025.'},
      'rev_sharing':{shown:'Net transfers between areas. Positive = receiving, negative = contributing.',insight:'Revenue sharing by area for 2025.'},
      'fundraising_roi':{shown:'Gross revenue divided by fundraising costs.',insight:'Fundraising ROI by area for 2025.'},
      'opex':{shown:'Program costs plus admin costs. Does not include fundraising costs.',insight:'This metric shows what it costs to run each area — and whether spending growth is keeping pace with fundraising.'},
      'net_income':{shown:'All revenue minus all expenses and revenue sharing.',insight:'This metric shows the bottom line for each area — whether it\'s building reserves or drawing them down.'},
      'equity':{shown:'Ending cash split into reserve minimum (4.5 months of staffing) and growth funds above that.',insight:'Cash on hand by area as of 12/31/2025, shown against the 4.5-month staffing reserve target.'},
      'gross_revenue':{shown:'All income before any deductions — donations, grants, events, and other sources.',insight:'Total gross revenue by area for 2025, before any deductions.'},
      'cogs':{shown:'Event expenses, platform fees, and other costs directly tied to fundraising activities.',insight:'This metric shows the direct costs of raising revenue — what it takes to bring dollars in the door.'},
    };
    const explainerColors={'direct_fundraising':'var(--green)','net_margin':'var(--green)','rev_sharing':'var(--accent)','fundraising_roi':'var(--blue)','opex':'var(--warm)','net_income':'var(--rose)','equity':'var(--blue)','gross_revenue':'var(--accent)','cogs':'var(--warm)'};
    if(explainers[metricKey]){
      const eCol=explainerColors[metricKey]||'var(--accent)';
      const ex=explainers[metricKey];
      h+=`<div style="margin-bottom:1.4rem;line-height:1.6"><div style="font-size:0.88rem;font-weight:600;color:${eCol};margin-bottom:0.4rem">${ex.insight}</div><div style="font-size:0.8rem;color:var(--text);opacity:0.65"><strong style="opacity:1;color:var(--text-muted)">How It\u2019s Calculated:</strong> ${ex.shown}</div></div>`;
    }

    // Special case: equity shows min target + growth funds split
    if(metricKey==='equity'){
      const maxEq=Math.max(...fas.map(ar=>D.equity[ar]||0));
      [...fas].sort((x,y)=>{const ve=x==='__local_grouped__'?getGroupedVal('equity','2025'):(D.equity[x]||0);const vy=y==='__local_grouped__'?getGroupedVal('equity','2025'):(D.equity[y]||0);return vy-ve}).forEach(ar=>{
        const aeq=ar==='__local_grouped__'?getGroupedVal('equity','2025'):(D.equity[ar]||0);
        const atr=ar==='__local_grouped__'?D.field_areas.filter(x=>x!=='WI Statewide').reduce((s,x)=>s+(D.target_reserve[x]||0),0):(D.target_reserve[ar]||0);
        const aGrowth=aeq-atr;
        const tgtW=maxEq>0?Math.min(Math.min(aeq,atr)/maxEq*100,100):0;
        const growthW=aGrowth>0?(aGrowth/maxEq*100):0;
        const gapW=aGrowth<0?((atr-aeq)/maxEq*100):0;
        h+=`<div class="bar-group"><div class="bar-label"><span>${areaLabel(ar)}</span><span>${fmtK(aeq)} <span style="font-size:0.68rem;color:${(()=>{const s=D.staffing_2026[ar]||0;const m=s>0?aeq/(s/12):0;return m>=4.5?'var(--green)':m>=3?'var(--warm)':'var(--rose)'})()};font-weight:700">${(()=>{const s=D.staffing_2026[ar]||0;return s>0?(aeq/(s/12)).toFixed(1)+' mo staffing':'N/A'})()}</span></span></div><div class="bar-track"><div style="display:flex;height:100%;border-radius:4px;overflow:hidden"><div style="width:${tgtW}%;background:var(--warm)"></div>${growthW>0?`<div style="width:${growthW}%;background:var(--accent)"></div>`:''}${gapW>0?`<div style="width:${gapW}%;background:#e0e0e0"></div>`:''}</div></div></div>`;
      });
      h+=`<div class="legend" style="margin-top:0.6rem"><div class="legend-item"><div class="legend-dot" style="background:var(--warm)"></div>Minimum Target</div><div class="legend-item"><div class="legend-dot" style="background:var(--accent)"></div>Growth Funds</div></div>`;
      $(el).innerHTML=h;
      return;
    }
    // Special case: fundraising margin shows gross revenue bar with net fundraising fill
    if(metricKey==='net_margin'){
      const maxGross=Math.max(...fas.map(ar=>{
        if(ar==='__local_grouped__'){return D.field_areas.filter(x=>x!=='WI Statewide').reduce((s,x)=>s+(D.pnl_by_area[x][8]['2025']||0),0);}
        return D.pnl_by_area[ar][8]['2025']||0;
      }));
      [...fas].sort((x,y)=>{
        const gx=x==='__local_grouped__'?D.field_areas.filter(a=>a!=='WI Statewide').reduce((s,a)=>s+(D.pnl_by_area[a][8]['2025']||0),0):(D.pnl_by_area[x][8]['2025']||0);
        const gy=y==='__local_grouped__'?D.field_areas.filter(a=>a!=='WI Statewide').reduce((s,a)=>s+(D.pnl_by_area[a][8]['2025']||0),0):(D.pnl_by_area[y][8]['2025']||0);
        return gy-gx;
      }).forEach(ar=>{
        const getYr=(a,yr)=>{
          if(a==='__local_grouped__'){
            const locals=D.field_areas.filter(x=>x!=='WI Statewide');
            const g=locals.reduce((s,x)=>s+(D.pnl_by_area[x][8][yr]||0),0);
            const n=locals.reduce((s,x)=>s+(D.local_fundraising[yr][x]||0),0);
            return{gross:g,net:n,margin:g>0?n/g*100:0};
          }
          const g=D.pnl_by_area[a][8][yr]||0;
          const n=D.local_fundraising[yr][a]||0;
          return{gross:g,net:n,margin:g>0?n/g*100:0};
        };
        const d25=getYr(ar,'2025'),d24=getYr(ar,'2024');
        const mChg=yoy(d24.margin/100,d25.margin/100);
        const mCol=mChg!=null?(mChg>0?'var(--green)':'var(--rose)'):'var(--text-dim)';
        const mPct=mChg!=null?(mChg>=0?'+':'')+(mChg*100).toFixed(0)+'%':'';
        const grossW=maxGross>0?d25.gross/maxGross*100:0;
        const netPct=d25.gross>0?d25.net/d25.gross*100:0;

        h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
        // 2025: gross as full bar track, net as fill portion
        h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px;position:relative"><div style="position:absolute;left:0;top:0;height:100%;width:${grossW}%;background:var(--green);opacity:0.2;border-radius:4px"></div><div style="position:absolute;left:0;top:0;height:100%;width:${grossW*netPct/100}%;background:var(--green);border-radius:4px"></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:140px;text-align:left;padding-left:0.5rem">${d25.margin.toFixed(0)}% <span style="font-size:0.68rem;color:var(--text-dim)">${fmtK(d25.net)} of ${fmtK(d25.gross)}</span>${mPct?' <span style="color:'+mCol+';font-weight:600;font-size:0.72rem">'+mPct+'</span>':''}</span></div>`;
        // 2024
        const grossW24=maxGross>0?d24.gross/maxGross*100:0;
        const netPct24=d24.gross>0?d24.net/d24.gross*100:0;
        if(d24.gross)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px;position:relative"><div style="position:absolute;left:0;top:0;height:100%;width:${grossW24}%;background:var(--green);opacity:0.1;border-radius:4px"></div><div style="position:absolute;left:0;top:0;height:100%;width:${grossW24*netPct24/100}%;background:var(--green);opacity:0.5;border-radius:4px"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:140px;text-align:left;padding-left:0.5rem">${d24.margin.toFixed(0)}% <span style="font-size:0.62rem">${fmtK(d24.net)} of ${fmtK(d24.gross)}</span></span></div>`;
        h+=`</div>`;
      });
      h+=`<div class="legend" style="margin-top:0.6rem"><div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div>Net Fundraising</div><div class="legend-item"><div class="legend-dot" style="background:var(--green);opacity:0.2"></div>Gross Revenue</div></div>`;
      $(el).innerHTML=h;
      return;
    }
    const getFinVal23=(ar,key)=>{
      if(ar==='__local_grouped__')return getGroupedVal(key,'2023');
      const p=D.pnl_by_area[ar];
      if(key==='direct_fundraising') return D.local_fundraising['2023'][ar]||0;
      if(key==='net_margin'){const p2=D.pnl_by_area[ar];const g=p2[8]['2023']||0;const lf=D.local_fundraising['2023'][ar]||0;return g>0?lf/g*100:0;}
      if(key==='rev_sharing'){return D.rev_sharing['2023'][ar]||0;}
      if(key==='fundraising_roi'){const p2=D.pnl_by_area[ar];const g=p2[8]['2023']||0;const fc=p2[9]['2023']||0;return fc>0?g/fc:0;}
      if(key==='opex') return p[16]['2023']||0;
      if(key==='net_income') return p[18]['2023']||0;
      if(key==='equity') return 0;
      if(key==='gross_revenue') return p[8]['2023']||0;
      if(key==='cogs') return p[9]['2023']||0;
      return 0;
    };
    const maxV=Math.max(...fas.map(ar=>Math.max(Math.abs(getFinVal(ar,metricKey)),Math.abs(getFinVal24(ar,metricKey)),Math.abs(getFinVal23(ar,metricKey)))));
    // Special case: net_income and rev_sharing use center-zero diverging bars
    if(metricKey==='net_income'||metricKey==='rev_sharing'){
      const maxAbs=Math.max(...fas.map(ar=>Math.max(Math.abs(getFinVal(ar,metricKey)),Math.abs(getFinVal24(ar,metricKey)),Math.abs(getFinVal23(ar,metricKey)))));

      const divBar=(v,h_px,op)=>{
        const barPct=maxAbs>0?Math.abs(v)/maxAbs*50:0;
        const barCol=v>=0?'var(--green)':'var(--rose)';
        const opStyle=op<1?'opacity:'+op+';':'';
        if(v<0){
          return '<div style="display:flex;height:'+h_px+'px;'+opStyle+'"><div style="width:'+(50-barPct)+'%"></div><div style="width:'+barPct+'%;background:'+barCol+';border-radius:4px 0 0 4px"></div><div style="width:1px;background:var(--border)"></div><div style="width:50%;background:transparent"></div></div>';
        } else {
          return '<div style="display:flex;height:'+h_px+'px;'+opStyle+'"><div style="width:50%;background:transparent"></div><div style="width:1px;background:var(--border)"></div><div style="width:'+barPct+'%;background:'+barCol+';border-radius:0 4px 4px 0"></div><div style="flex:1"></div></div>';
        }
      };

      [...fas].sort((x,y)=>getFinVal(y,metricKey)-getFinVal(x,metricKey)).forEach(ar=>{
        const v3=getFinVal23(ar,metricKey);
        const v4=getFinVal24(ar,metricKey);
        const v5=getFinVal(ar,metricKey);
        const vc5=v5<0?'var(--rose)':'var(--green)';
        const vc4=v4<0?'var(--rose)':'var(--green)';
        const vc3=v3<0?'var(--rose)':'var(--green)';

        h+='<div style="margin-bottom:1.8rem">';
        h+='<div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">'+areaLabel(ar)+'</span></div>';
        // 2025
        h+='<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div style="flex:1;background:var(--surface2);border-radius:4px;overflow:hidden">'+divBar(v5,22,1)+'</div><span style="font-size:0.85rem;font-weight:700;min-width:100px;text-align:left;padding-left:0.5rem;color:'+vc5+'">'+fmt(v5)+'</span></div>';
        // 2024
        h+='<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div style="flex:1;background:var(--surface2);border-radius:4px;overflow:hidden">'+divBar(v4,8,0.5)+'</div><span style="font-size:0.68rem;min-width:100px;text-align:left;padding-left:0.5rem;color:'+vc4+'">'+fmt(v4)+'</span></div>';
        // 2023
        if(v3){
          h+='<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2023</span><div style="flex:1;background:var(--surface2);border-radius:4px;overflow:hidden">'+divBar(v3,8,0.3)+'</div><span style="font-size:0.68rem;min-width:100px;text-align:left;padding-left:0.5rem;color:'+vc3+'">'+fmt(v3)+'</span></div>';
        }
        h+='</div>';
      });
      $(el).innerHTML=h;
      return;
    }


    [...fas].sort((x,y)=>getFinVal(y,metricKey)-getFinVal(x,metricKey)).forEach(ar=>{
      const v3=getFinVal23(ar,metricKey);
      const v4=getFinVal24(ar,metricKey);
      const v5=getFinVal(ar,metricKey);
      const c1=yoy(v3,v4);
      const c2=yoy(v4,v5);
      const isExpense=metricKey==='opex'||metricKey==='cogs';
      const good1=isExpense?(c1<0):(c1>0);
      const good2=isExpense?(c2<0):(c2>0);
      const col1=c1!=null?(good1?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const col2=c2!=null?(good2?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const pct1=c1!=null?(c1>=0?'+':'')+(c1*100).toFixed(0)+'%':'';
      const pct2=c2!=null?(c2>=0?'+':'')+(c2*100).toFixed(0)+'%':'';
      const valCol=v5<0?'var(--rose)':'var(--text)';
      const w5=maxV>0?Math.max(v5,0)/maxV*100:0;

      h+=`<div style="margin-bottom:1.8rem">`;
      h+=`<div style="margin-bottom:0.3rem">`;
      h+=`<span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span>`;
      h+=`</div>`;
      // 2025 bar - segmented for opex, solid for others
      if(metricKey==='opex'&&ar!=='__local_grouped__'){
        const p=D.pnl_by_area[ar];
        const aProg=p[14]['2025']||0;
        const aAdm=p[15]['2025']||0;
        const progPct=v5>0?aProg/v5*100:0;
        const admPct=v5>0?aAdm/v5*100:0;
        h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="width:28px"></span><div style="flex:1;display:flex;font-size:0.58rem;font-weight:700;color:var(--text-dim);margin-bottom:0.1rem"><div style="width:${w5}%;display:flex"><span style="width:${progPct}%;text-align:center">${progPct>=10?Math.round(progPct)+'% Program':''}</span><span style="width:${admPct}%;text-align:center">${admPct>=8?Math.round(admPct)+'% Admin':''}</span></div></div><span style="min-width:100px"></span></div>`;
        h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div style="display:flex;height:100%;width:${w5}%;border-radius:4px;overflow:hidden"><div style="width:${progPct}%;background:var(--warm)"></div><div style="width:${admPct}%;background:var(--warm);opacity:0.45"></div></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:100px;text-align:left;padding-left:0.5rem">${metricKey==='net_margin'?v5.toFixed(0)+'%':metricKey==='fundraising_roi'?v5.toFixed(0)+'x':fmtK(v5)}${pct2?' <span style="color:'+col2+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
      } else {
        h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div class="bar-fill ${barCls}" style="width:${w5}%"></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:100px;text-align:left;padding-left:0.5rem">${metricKey==='net_margin'?v5.toFixed(0)+'%':metricKey==='fundraising_roi'?v5.toFixed(0)+'x':fmtK(v5)}${pct2?' <span style="color:'+col2+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
      }
      // 2024 bar (lighter)
      const w4=maxV>0?Math.max(v4,0)/maxV*100:0;
      const w3=maxV>0?Math.max(v3,0)/maxV*100:0;
      h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${barCls}" style="width:${w4}%;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${metricKey==='net_margin'?v4.toFixed(0)+'%':metricKey==='fundraising_roi'?v4.toFixed(0)+'x':fmtK(v4)}${pct1?' <span style="color:'+col1+';font-weight:600">'+pct1+'</span>':''}</span></div>`;
      // 2023 bar (lightest)
      if(v3>0){h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2023</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${barCls}" style="width:${w3}%;opacity:0.3"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${metricKey==='net_margin'?v3.toFixed(0)+'%':metricKey==='fundraising_roi'?v3.toFixed(0)+'x':fmtK(v3)}</span></div>`;}
      h+=`</div>`;
    });
    if(metricKey==='opex')h+=`<div class="legend" style="margin-top:0.6rem"><div class="legend-item"><div class="legend-dot" style="background:var(--warm)"></div>Program</div><div class="legend-item"><div class="legend-dot" style="background:var(--warm);opacity:0.45"></div>Admin</div></div>`;
    $(el).innerHTML=h;
  };

  // Overview KPIs - clickable
  window._ovMetrics=[
    {l:'Net Fundraising',k:'direct_fundraising',bc:'d'},
    {l:'Fundraising Margin',k:'net_margin',bc:'d'},
    {l:'Operating Expenses',k:'opex',bc:'b'},
    {l:'Revenue Sharing',k:'rev_sharing',bc:'a'},
    {l:'Net Income',k:'net_income',bc:'e'},
    {l:'Cash Reserves',k:'equity',bc:'c'},
  ];

  const grossRev25=pnl[8]['2025']||0,grossRev24=pnl[8]['2024']||0;
  const margin25=grossRev25>0?df25/grossRev25:0;
  const margin24=grossRev24>0?df24/grossRev24:0;
  const totalShared25=D.field_areas.reduce((s,ar)=>{const v=D.rev_sharing['2025'][ar]||0;return v>0?s+v:s},0);
  const totalShared24=D.field_areas.reduce((s,ar)=>{const v=D.rev_sharing['2024'][ar]||0;return v>0?s+v:s},0);
  const roiOv25=pnl[9]['2025']>0?grossRev25/pnl[9]['2025']:0;
  const roiOv24=pnl[9]['2024']>0?grossRev24/pnl[9]['2024']:0;

  const ovKpiData=[
    {l:'Net Fundraising',v:fmtK(df25),c:yoy(df24,df25),cl:'green'},
    {l:'Fundraising Margin',v:(margin25*100).toFixed(0)+'%',c:yoy(margin24,margin25),cl:'green',sub:fmtK(df25)+' of '+fmtK(grossRev25)},
    {l:'Operating Expenses',v:fmtK(opex25),c:yoy(opex24,opex25),cl:'warm',inv:1},
    {l:'Revenue Sharing',v:fmtK(totalShared25),c:yoy(totalShared24,totalShared25),cl:'',sub:'Shared with local sites'},
    {l:'Net Income',v:fmt(net_inc['2025']),c:yoy(net_inc['2024'],net_inc['2025']),cl:net_inc['2025']>=0?'green':'rose'},
    {l:'Cash Reserves',v:fmtK(eq),c:yoy(eq-net_inc['2025'],eq),cl:'blue',sub:reserveMonths.toFixed(1)+' Mo in Reserve'},
  ];

  renderKpis(ovKpiData,'#ov-kpis',{onClick:'clickOvKpi'});
  if(window._lastOvIdx==null)window._lastOvIdx=0;
  // Re-select the last clicked KPI after re-render
  setTimeout(()=>{clickOvKpi(window._lastOvIdx||0)},0);


  // Mission data (needed by exec summary and later)
  const mv23=(D.mission_v2||{})['2023']||{};
  const mv24=(D.mission_v2||{})['2024']||{};
  const mv25=(D.mission_v2||{})['2025']||{};
  const mAll23=mv23['All Areas']||{};
  const mAll24=mv24['All Areas']||{};
  const mAll25=mv25['All Areas']||{};

  // ===== EXECUTIVE SUMMARY =====
  {
    const p=D.pnl_by_area['All Areas'];
    const rev24=p[8]['2024']||0,rev25=p[8]['2025']||0;
    const exp24=(p[16]['2024']||0)+(p[9]['2024']||0),exp25=(p[16]['2025']||0)+(p[9]['2025']||0);
    const expGr=yoy(exp24,exp25);
    const fam25=mAll25.avg_families||0,fam24=mAll24.avg_families||0;
    const ind25=mAll25.avg_individuals||0,ind24=mAll24.avg_individuals||0;
    const cpf25=fam25>0?(opex25/12/fam25):0,cpf24=fam24>0?((p[16]['2024']||0)/12/fam24):0;
    const cpi25=ind25>0?(opex25/12/ind25):0,cpi24=ind24>0?((p[16]['2024']||0)/12/ind24):0;
    const famChg=yoy(fam24,fam25),indChg=yoy(ind24,ind25);
    const cpfChg=yoy(cpf24,cpf25),cpiChg=yoy(cpi24,cpi25);
    const dfChg=yoy(df24,df25);

    // Dynamic KPI value resolver for executive summary
    const allOpex25=exp25, allOpex24=exp24;
    const allDf25=D.local_fundraising['2025']['All Areas']||0;
    const allDf24=D.local_fundraising['2024']['All Areas']||0;
    function execKpiValue(key,type){
      if(type==='mission'){return{v25:mAll25[key]||0,v24:mAll24[key]||0};}
      if(type==='cost'){
        if(key==='fam'){const f5=mAll25.avg_families||0,f4=mAll24.avg_families||0;return{v25:f5>0?allOpex25/12/f5:0,v24:f4>0?allOpex24/12/f4:0};}
        if(key==='ind'){const i5=mAll25.avg_individuals||0,i4=mAll24.avg_individuals||0;return{v25:i5>0?allOpex25/12/i5:0,v24:i4>0?allOpex24/12/i4:0};}
      }
      if(type==='fin'){
        if(key==='direct_fundraising')return{v25:allDf25,v24:allDf24};
        if(key==='opex')return{v25:allOpex25,v24:allOpex24};
      }
      return{v25:0,v24:0};
    }

    // Build dynamic groups from D.highlight_groups
    const colorMap={green:'var(--green)',accent:'var(--accent)',warm:'var(--warm)',rose:'var(--rose)',blue:'var(--blue)'};
    const execMetrics=[];
    const groups=[];
    let kpiIdx=0;
    (D.highlight_groups||[]).forEach(hg=>{
      const startIdx=kpiIdx;
      const groupKpis=[];
      hg.kpis.forEach(kpi=>{
        const{v25,v24}=execKpiValue(kpi.k,kpi.type);
        const chg=yoy(v24,v25);
        let formatted,up;
        if(kpi.type==='mission'){formatted=kpi.dec?v25.toFixed(1):v25.toFixed(0);up=v25>v24;}
        else if(kpi.type==='cost'){formatted=fmt(Math.round(v25));up=v25<v24;}
        else{formatted=fmtK(v25);up=v25>v24;}
        execMetrics.push({l:kpi.l,k:kpi.k,dec:kpi.dec,bc:kpi.bc,type:kpi.type});
        groupKpis.push({label:kpi.l,value:formatted,chg,up,cl:hg.color});
        kpiIdx++;
      });
      groups.push({color:colorMap[hg.color]||('var(--'+hg.color+')'),title:hg.title,sub:hg.sub,startIdx,kpis:groupKpis});
    });
    window._execMetrics=execMetrics;
    window._execGroups=groups;
    window._execGroupStarts=groups.map(g=>g.startIdx);

    const selIdx=window._lastExecIdx||0;
    const selGroup=execIdxToGroup(selIdx);

    let sh='';
    groups.forEach((g,gi)=>{
      const isActive=gi===selGroup;
      sh+=`<div class="exec-row${isActive?' exec-row-active':''}" data-group="${gi}" style="padding:1.5rem 1.4rem;cursor:pointer;border-left:4px solid ${isActive?g.color:'transparent'};background:${isActive?'var(--surface2)':'transparent'};transition:all 0.15s" onclick="clickExecGroup(${gi})">`;
      sh+=`<div style="font-size:1.15rem;font-weight:600;color:${g.color};line-height:1.35">${g.title}</div>`;
      sh+=`<div style="font-size:0.82rem;color:var(--text-muted);margin-top:0.4rem;line-height:1.55">${g.sub}</div>`;
      sh+=`</div>`;
      if(gi<groups.length-1)sh+=`<div style="height:1px;background:var(--border)"></div>`;
    });
    $('#exec-sidebar').innerHTML=sh;

    // KPI cards in the hero body
    renderExecKpiCards(selIdx);

    if(window._lastExecIdx==null)window._lastExecIdx=0;
    renderExecChart(window._lastExecIdx);
  }

  // ===== DETAILED FINANCIALS =====
  // Use filtered area for detailed financials
  const dfPnl=D.pnl_by_area[a];
  // Area badges


  // ===== CASH FLOW STATEMENT =====
  {
    const dP=D.pnl_by_area[a];
    const aEq=D.equity[a]||0;
    const aNi=dP[18]['2025']||0;
    const startCash=aEq-aNi;
    const rsp23=D.rev_sharing['2023'][a]||0,rsp24=D.rev_sharing['2024'][a]||0,rsp25=D.rev_sharing['2025'][a]||0;

    const pctInline=(prev,cur)=>{if(!prev||!cur||prev===0)return'';const cc=(cur-prev)/Math.abs(prev)*100;const col=cc>1?'var(--green)':cc<-1?'var(--rose)':'var(--text-dim)';return cc!==0?' <span style="font-size:0.62rem;font-weight:700;color:'+col+'">'+(cc>0?'+':'')+cc.toFixed(0)+'%</span>':'';};
    const vc=(v)=>`<td>${fmt(v)}</td>`;
    const vc25=(v)=>`<td style="border-left:2px solid #e8e8e6;font-weight:700">${fmt(v)}</td>`;
    const sectionHdr=(label)=>`<tr><td colspan="4" style="background:var(--surface2);color:var(--accent);font-weight:700;padding:0.5rem 0.9rem;font-size:0.72rem;">${label}</td></tr>`;
    const _glReady=D.gl_available_years&&D.gl_available_years.length>0;
    const dataRow=(label,r,style,liIdx)=>{const gl=(_glReady&&liIdx!==undefined);return`<tr${gl?' class="gl-drillable" data-li-idx="'+liIdx+'"':''}${style?' style="'+style+'"':''}><td>${gl?'<span class="gl-arrow">&#9656;</span> ':''}${label}</td>${vc(r['2023'])}<td>${fmt(r['2024'])}${pctInline(r['2023'],r['2024'])}</td><td style="border-left:2px solid #e8e8e6;font-weight:700">${fmt(r['2025'])}${pctInline(r['2024'],r['2025'])}</td></tr>`;};
    const totalRow=(label,v23,v24,v25,style)=>`<tr class="total-row"${style?' style="'+style+'"':''}><td>${label}</td>${vc(v23)}<td>${fmt(v24)}${pctInline(v23,v24)}</td><td style="border-left:2px solid #e8e8e6;font-weight:700">${fmt(v25)}${pctInline(v24,v25)}</td></tr>`;

    const nf23=(dP[8]['2023']||0)-(dP[9]['2023']||0);
    const nf24=(dP[8]['2024']||0)-(dP[9]['2024']||0);
    const nf25=(dP[8]['2025']||0)-(dP[9]['2025']||0);

    let h='<table style="width:100%;border-collapse:collapse;table-layout:fixed"><colgroup><col style="width:auto"><col style="width:16%"><col style="width:18%"><col style="width:18%"></colgroup><tbody>';

    // Reverse-engineer cash balances for all 3 years
    const end25=aEq;
    const start25=end25-aNi;
    const ni24y=dP[18]['2024']||0;
    const end24=start25;
    const start24=end24-ni24y;
    const ni23y=dP[18]['2023']||0;
    const end23=start24;
    const start23=end23-ni23y;

    // STARTING CASH
    h+=`<tr style="background:var(--blue-dim)"><td style="font-weight:700;color:var(--blue);padding:0.7rem 0.9rem">STARTING CASH (1/1)</td><td style="text-align:right;font-weight:700;color:var(--blue);padding:0.7rem 0.9rem;opacity:0.55">${fmtK(start23)}</td><td style="text-align:right;font-weight:700;color:var(--blue);padding:0.7rem 0.9rem;opacity:0.7">${fmtK(start24)}</td><td style="text-align:right;font-weight:700;color:var(--blue);padding:0.7rem 0.9rem;border-left:2px solid #e8e8e6">${fmtK(start25)}</td></tr>`;

    // REVENUE HEADER
    h+=`<tr><td style="background:var(--surface2);color:var(--accent);font-weight:700;padding:0.5rem 0.9rem;font-size:0.72rem;">Revenue</td><td style="background:var(--surface2);color:var(--accent);font-weight:700;font-size:0.72rem;text-align:right">2023</td><td style="background:var(--surface2);color:var(--accent);font-weight:700;font-size:0.72rem;text-align:right">2024</td><td style="background:var(--surface2);color:var(--accent);font-weight:700;font-size:0.72rem;text-align:right;border-left:2px solid #e8e8e6">2025</td></tr>`;
    for(let i=0;i<8;i++) h+=dataRow(dP[i].label,dP[i],null,i);
    h+=totalRow('GROSS REVENUE',dP[8]['2023'],dP[8]['2024'],dP[8]['2025']);
    h+=dataRow(dP[9].label,dP[9],'background:#fff',9);
    h+=totalRow('NET FUNDRAISING',nf23,nf24,nf25);

    // OPERATING EXPENSES
    h+=sectionHdr('Operating Expenses');
    for(let i=10;i<=13;i++) h+=dataRow(dP[i].label,dP[i],'padding-left:1.5rem',i);
    h+=totalRow(dP[14].label,dP[14]['2023'],dP[14]['2024'],dP[14]['2025'],'padding-left:1.5rem');
    h+=totalRow(dP[15].label,dP[15]['2023'],dP[15]['2024'],dP[15]['2025'],'padding-left:1.5rem');

    const gfe23=(dP[17]['2023']||0)-(dP[18]['2023']||0)-(rsp23||0);
    const gfe24=(dP[17]['2024']||0)-(dP[18]['2024']||0)-(rsp24||0);
    const gfe25=(dP[17]['2025']||0)-(dP[18]['2025']||0)-(rsp25||0);
    if(gfe23||gfe24||gfe25){
      h+=totalRow('TOTAL GROWTH FUND EXPENSES',gfe23,gfe24,gfe25,'padding-left:1.5rem');
    }

    const totOpex23=(dP[16]['2023']||0)+gfe23,totOpex24=(dP[16]['2024']||0)+gfe24,totOpex25=(dP[16]['2025']||0)+gfe25;
    h+=totalRow('TOTAL OPERATING EXPENSES',totOpex23,totOpex24,totOpex25);

    const noi23=nf23-totOpex23,noi24=nf24-totOpex24,noi25=nf25-totOpex25;
    h+=totalRow('NET OPERATING INCOME',noi23,noi24,noi25);

    if(rsp23||rsp24||rsp25||a!=='All Areas'){
      h+=dataRow('Revenue Sharing (Net Transfers)',{'2023':rsp23,'2024':rsp24,'2025':rsp25});
    }

    // NET INCOME = NET CASH CHANGE
    h+=totalRow('NET INCOME / CASH CHANGE',dP[18]['2023'],dP[18]['2024'],dP[18]['2025'],'background:var(--green-dim)');

    // ENDING CASH
    h+=`<tr style="background:var(--blue-dim)"><td style="font-weight:700;color:var(--blue);padding:0.7rem 0.9rem">ENDING CASH (12/31)</td><td style="text-align:right;font-weight:700;color:var(--blue);padding:0.7rem 0.9rem;opacity:0.55">${fmtK(end23)}</td><td style="text-align:right;font-weight:700;color:var(--blue);padding:0.7rem 0.9rem;opacity:0.7">${fmtK(end24)}</td><td style="text-align:right;font-weight:700;color:var(--blue);padding:0.7rem 0.9rem;border-left:2px solid #e8e8e6">${fmtK(end25)}</td></tr>`;

    h+='</tbody></table>';
    if($('#cashflow-content'))$('#cashflow-content').innerHTML=h;
  }

  // ===== REVENUE DETAIL — clickable KPIs =====
  {
    const dP=D.pnl_by_area[a];
    const g25=dP[8]['2025']||0,g24=dP[8]['2024']||0;
    const fc25=dP[9]['2025']||0,fc24=dP[9]['2024']||0;
    const n25=g25-fc25,n24=g24-fc24;
    const roi25=fc25>0?g25/fc25:0,roi24=fc24>0?g24/fc24:0;
    const ars25=D.rev_sharing['2025'][a]||0,ars24=D.rev_sharing['2024'][a]||0;
    const totalShared25=D.field_areas.reduce((s,ar)=>{const v=D.rev_sharing['2025'][ar]||0;return v>0?s+v:s},0);
    const totalShared24=D.field_areas.reduce((s,ar)=>{const v=D.rev_sharing['2024'][ar]||0;return v>0?s+v:s},0);

    window._revMetrics=[
      {l:'Gross / Costs / Net',k:'waterfall',bc:'d'},
      {l:'Fundraising ROI',k:'roi',bc:'c'},
      {l:'Revenue Sharing',k:'sharing',bc:'d'},
      {l:'Revenue Sources',k:'sources',bc:'a'},
    ];

    const isAllAreas=a==='All Areas';
    const rsDisplay=isAllAreas?totalShared25:ars25;
    const rsDisplay24=isAllAreas?totalShared24:ars24;
    const rsSub=isAllAreas?'Shared with local sites':ars25>0?'Receiving support':ars25<0?'Contributing to others':'';

    const revKpis=[
      {l:'Net Fundraising',v:fmtK(n25),c:yoy(n24,n25),cl:'green',sub:fmtK(g25)+' gross \u2212 '+fmtK(fc25)+' costs'},
      {l:'Fundraising ROI',v:roi25>0?roi25.toFixed(0)+'x':'\u2014',c:yoy(roi24,roi25),cl:'blue'},
      {l:'Revenue Sharing',v:rsDisplay?fmtK(rsDisplay):'\u2014',c:yoy(rsDisplay24,rsDisplay),cl:isAllAreas?'accent':ars25>=0?'green':'rose',sub:rsSub},
      {l:'Revenue Mix',v:(()=>{const srcs=(D.rev_sources['2025'][a]||[]);const top=srcs.length?srcs.sort((a,b)=>b.value-a.value)[0]:null;const topPct=top&&g25>0?(top.value/g25*100).toFixed(0)+'%':'';return top?topPct+' '+top.label:'\u2014'})(),cl:'',sub:((D.rev_sources['2025'][a]||[]).length||0)+' revenue streams'},
    ];

    const rkEl=$('#rev-kpis');
    if(rkEl){let rh='';revKpis.forEach((k,i)=>{const dir=k.c>0.01?'up':k.c<-0.01?'down':'flat';
    rh+='<div class="kpi-card '+(k.cl||'')+(i===0?' kpi-selected':'')+'" onclick="clickRevKpi('+i+')"><div class="kpi-label">'+k.l+'</div><div class="kpi-value">'+k.v+'</div><div class="kpi-change '+dir+'">'+(k.c!=null?((dir==='up'?'\u25b2':dir==='down'?'\u25bc':'\u2014')+' '+(k.c>0?'+':'')+(k.c*100).toFixed(1)+'% vs 2024'):(k.sub||''))+'</div></div>'});rkEl.innerHTML=rh;}
    if($('#rev-detail-content'))renderRevContent(window._lastRevIdx||0);
  }

  // ===== EXPENSE DETAIL — clickable KPIs =====
  {
    const dP=D.pnl_by_area[a];
    const prog25=dP[14]['2025']||0,prog24=dP[14]['2024']||0;
    const adm25=dP[15]['2025']||0,adm24=dP[15]['2024']||0;
    const fc25=dP[9]['2025']||0,fc24=dP[9]['2024']||0;
    const totExp25=(dP[16]['2025']||0)+fc25,totExp24=(dP[16]['2024']||0)+fc24;
    const progPct=totExp25>0?(prog25/totExp25*100).toFixed(0)+'%':'';
    const admPct=totExp25>0?(adm25/totExp25*100).toFixed(0)+'%':'';

    window._expMetrics=[
      {l:'Program Costs',k:'program',bc:'e'},
      {l:'Admin Costs',k:'admin',bc:'b'},
      {l:'Fundraising Costs',k:'fundraising',bc:'d'},
      {l:'Total Expenses',k:'total',bc:'c'},
    ];

    const expKpis=[
      {l:'Program Costs',v:fmtK(prog25),c:yoy(prog24,prog25),cl:'rose',inv:1,sub:progPct+' of total'},
      {l:'Admin Costs',v:fmtK(adm25),c:yoy(adm24,adm25),cl:'warm',inv:1,sub:admPct+' of total'},
      {l:'Fundraising Costs',v:fmtK(fc25),c:yoy(fc24,fc25),cl:'',inv:1},
      {l:'Total Expenses',v:fmtK(totExp25),c:yoy(totExp24,totExp25),cl:'blue',inv:1},
    ];

    const ekEl=$('#exp-kpis');
    if(ekEl){let eh='';expKpis.forEach((k,i)=>{const dir=k.inv?(k.c>0.01?'down':k.c<-0.01?'up':'flat'):(k.c>0.01?'up':k.c<-0.01?'down':'flat');
    eh+='<div class="kpi-card '+(k.cl||'')+(i===0?' kpi-selected':'')+'" onclick="clickExpKpi('+i+')"><div class="kpi-label">'+k.l+'</div><div class="kpi-value">'+k.v+'</div><div class="kpi-change '+dir+'">'+(k.c!=null?((dir==='up'?'\u25b2':dir==='down'?'\u25bc':'\u2014')+' '+(k.c>0?'+':'')+(k.c*100).toFixed(1)+'% vs 2024'):(k.sub||''))+'</div></div>'});ekEl.innerHTML=eh;}
    if($('#exp-detail-content'))renderExpContent(window._lastExpIdx||0);
  }




  // ===== IMPACT OVERVIEW =====
  const ti25s=mAll25.total_intake||0,to25s=mAll25.total_opened||0,tm25s=mAll25.total_matched||0;
  const compAreas=D.field_areas.filter(ar=>ar!=='WI Statewide'&&ar!=='Chippewa Valley');

  // Helper for KPI rendering (supports clickable mode)

  // Helper for area comparison bars
  const missionExplainers={
    'avg_families':{shown:'Monthly average of families with an active case during 2025.',insight:'This metric shows the active caseload — how many families your team and volunteers are walking alongside in a typical month.'},
    'avg_individuals':{shown:'Monthly average of all individuals in families with an active case.',insight:'This connects caseload to human impact — every individual represents a life being stabilized.'},
    'unique_families':{shown:'Count of distinct families with any active service period during the year.',insight:'This metric shows total annual reach — every distinct family that received services this year.'},
    'total_matched':{shown:'Families newly matched during 2025, not including carryover cases from prior years.',insight:'New families matched with volunteers for the first time in 2025.'},
    'total_graduations':{shown:'Count of individuals whose cases were closed as graduated/transitioned during the year.',insight:'This metric shows program completions — families who graduated to independence and stability.'},
    'total_hosted':{shown:'Sum of all hosting days and hosting nights across all areas.',insight:'This is the heart of Safe Families — every hosted night is a child kept out of the foster system.'},
    'dec_volunteers':{shown:'Year-end count of all volunteers with active approved status.',insight:'This metric shows the total approved volunteer base — your full bench of people ready to serve.'},
    'avg_active_volunteers':{shown:'Monthly average of volunteers with at least one active assignment.',insight:'The gap between total and active volunteers is your reserve capacity — people ready to step in when needed.'},
    'dec_partner_churches':{shown:'Year-end count of churches with active partnership status.',insight:'Total partner churches as of December 2025.'},
    'total_service_hours':{shown:'Sum of all logged volunteer hours across all areas and assignment types.',insight:'This metric shows the collective time volunteers invested — hours that would cost millions to replace with paid staff.'},
    'avg_relationships':{shown:'All active volunteer-family connections in a typical month — hosting, friendships, and coaching combined.',insight:'This metric shows all active volunteer-family connections — hosting, friendships, and coaching combined.'},
    'avg_hosting':{shown:'Monthly average of volunteers actively hosting children.',insight:'This metric shows volunteers providing temporary safe housing for children — the most intensive and impactful relationship type.'},
    'avg_friendships':{shown:'Monthly average of active friendship-type volunteer assignments.',insight:'This metric shows volunteers walking alongside families as consistent, relational friends — the foundation of trust.'},
    'avg_coaching':{shown:'Monthly average of active coaching-type volunteer assignments.',insight:'Coaching gives families the tools to thrive independently — it\'s the bridge from crisis to long-term stability.'},
    'total_intake':{shown:'Count of all new intake referrals across all areas.',insight:'This metric shows total new referrals — the front door of the program and a measure of community awareness.'},
    'total_opened':{shown:'Referrals that were opened after assessment as appropriate for services.',insight:'This metric shows how many referrals were assessed as a good fit — families where Safe Families can meaningfully help.'},
  };
  window.renderAreaBars=(metric,label,el,dec,bc,kpiColor)=>{
    let h='';
    const bcToColor={'d':'var(--green)','c':'var(--blue)','b':'var(--warm)','e':'var(--rose)','a':'var(--accent)'};
    const eCol=bcToColor[bc]||'var(--green)';
    if(missionExplainers[metric]){
      const mx=missionExplainers[metric];
      h+=`<div style="margin-bottom:1.4rem;line-height:1.6"><div style="font-size:0.88rem;font-weight:600;color:${eCol};margin-bottom:0.4rem">${mx.insight}</div><div style="font-size:0.8rem;color:var(--text);opacity:0.65"><strong style="opacity:1;color:var(--text-muted)">How It\u2019s Calculated:</strong> ${mx.shown}</div></div>`;
    }
    const getMVal=(ar,yr)=>((D.mission_v2||{})[yr]||{})[ar]?.[metric]||0;
    const maxV=Math.max(...compAreas.map(ar=>Math.max(getMVal(ar,'2025'),getMVal(ar,'2024'))));
    [...compAreas].sort((x,y)=>getMVal(y,'2025')-getMVal(x,'2025')).forEach(ar=>{
      const v5=getMVal(ar,'2025');
      const v4=getMVal(ar,'2024');
      const c2=yoy(v4,v5);
      const col2=c2!=null?(c2>0?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const pct2=c2!=null?(c2>=0?'+':'')+(c2*100).toFixed(0)+'%':'';
      const fmt5=dec?v5.toFixed(1):fmtN(Math.round(v5));
      const fmt4=dec?v4.toFixed(1):fmtN(Math.round(v4));
      const w5=maxV>0?v5/maxV*100:0;
      const w4=maxV>0?v4/maxV*100:0;

      h+=`<div style="margin-bottom:1.8rem">`;
      h+=`<div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
      h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div class="bar-fill ${bc||'d'}" style="width:${w5}%"></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:100px;text-align:left;padding-left:0.5rem">${fmt5}${pct2?' <span style="color:'+col2+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
      if(v4)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${bc||'d'}" style="width:${w4}%;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${fmt4}</span></div>`;
      h+=`</div>`;
    });
    $(el).innerHTML=h;
  };

  // ── PEOPLE SERVED ──
  const newFamS=tm25s;
  const newFam24S=mAll24.total_matched||0;
  const ratioS=mAll25.ind_fam_ratio||3;
  const newIndS=Math.round(newFamS*ratioS);
  const newInd24S=Math.round(newFam24S*(mAll24.ind_fam_ratio||3));
  const uniqueInd25=mAll25.unique_individuals||Math.round((mAll25.unique_families||0)*ratioS);
  const uniqueInd24=mAll24.unique_individuals||Math.round((mAll24.unique_families||0)*(mAll24.ind_fam_ratio||3));
  window._peopleMetrics=[
    {l:'Unique Families (Annual)',k:'unique_families',dec:false,bc:'d'},
    {l:'Unique Individuals (Annual)',k:'unique_individuals',dec:false,bc:'c'},
    {l:'Avg Monthly Families',k:'avg_families',dec:true,bc:'d'},
    {l:'Avg Monthly Individuals',k:'avg_individuals',dec:true,bc:'c'},
    {l:'New Families This Year',k:'total_matched',dec:false,bc:'b'},
    {l:'Est. New Individuals',k:null,dec:false,bc:'d'},
  ];
  renderKpis([
    {l:'Unique Families (Annual)',v:fmtN(mAll25.unique_families),c:yoy(mAll24.unique_families,mAll25.unique_families),cl:'green'},
    {l:'Unique Individuals (Annual)',v:fmtN(uniqueInd25),c:yoy(uniqueInd24,uniqueInd25),cl:'blue'},
    {l:'Avg Monthly Families',v:(mAll25.avg_families||0).toFixed(0),c:yoy(mAll24.avg_families,mAll25.avg_families),cl:'green',sub:'Active Caseload'},
    {l:'Avg Monthly Individuals',v:(mAll25.avg_individuals||0).toFixed(0),c:yoy(mAll24.avg_individuals,mAll25.avg_individuals),cl:'blue',sub:'Active Caseload'},
    {l:'New Families This Year',v:fmtN(newFamS),c:yoy(newFam24S,newFamS),cl:'warm'},
    {l:'Est. New Individuals',v:fmtN(newIndS),c:yoy(newInd24S,newIndS),cl:'green'},
  ],'#sm-people-kpis',{onClick:'clickPeopleKpi'});

  // People by area (default: first KPI)
  $('#sm-people-area-title').textContent='Unique Families (Annual) By Area';
  renderAreaBars('unique_families','Unique Families (Annual)','#sm-people-area',false,'d');

  // ── VOLUNTEERING ──
  // ── VOLUNTEERING ──
  window._volMetrics=[
    {l:'Total Volunteers (Year-End)',k:'dec_volunteers',dec:false,bc:'d'},
    {l:'Avg Active Volunteers/Mo',k:'avg_active_volunteers',dec:true,bc:'c'},
    {l:'Partner Churches (Year-End)',k:'dec_partner_churches',dec:false,bc:'b'},
    {l:'Total Service Hours',k:'total_service_hours',dec:false,bc:'d'},
  ];
  renderKpis([
    {l:'Total Volunteers (Year-End)',v:fmtN(mAll25.dec_volunteers),c:yoy(mAll24.dec_volunteers,mAll25.dec_volunteers),cl:'green'},
    {l:'Avg Active Volunteers/Mo',v:(mAll25.avg_active_volunteers||0).toFixed(0),c:yoy(mAll24.avg_active_volunteers,mAll25.avg_active_volunteers),cl:'blue',sub:mAll25.avg_active_volunteers?'':'New in 2025'},
    {l:'Partner Churches (Year-End)',v:fmtN(mAll25.dec_partner_churches),c:yoy(mAll24.dec_partner_churches,mAll25.dec_partner_churches),cl:'warm'},
    {l:'Total Service Hours',v:fmtN(mAll25.total_service_hours),c:yoy(mAll24.total_service_hours,mAll25.total_service_hours),cl:'green'},
  ],'#sm-vol-kpis',{onClick:'clickVolKpi'});
  $('#sm-vol-area-title').textContent='Total Volunteers (Year-End) By Area';
  renderAreaBars('dec_volunteers','Total Volunteers (Year-End)','#sm-vol-area',false,'d');

  // ── RELATIONSHIPS ──
  window._relMetrics=[
    {l:'Total Active Relationships',k:'avg_relationships',dec:true,bc:'d'},
    {l:'Hosting Relationships',k:'avg_hosting',dec:true,bc:'c'},
    {l:'Family Friendships',k:'avg_friendships',dec:true,bc:'b'},
    {l:'Family Coaching',k:'avg_coaching',dec:true,bc:'e'},
  ];
  renderKpis([
    {l:'Total Active Relationships',v:(mAll25.avg_relationships||0).toFixed(0),c:yoy(mAll24.avg_relationships,mAll25.avg_relationships),cl:'green',sub:'Avg Monthly'},
    {l:'Hosting Relationships',v:(mAll25.avg_hosting||0).toFixed(0),c:yoy(mAll24.avg_hosting,mAll25.avg_hosting),cl:'blue',sub:'Avg Monthly'},
    {l:'Family Friendships',v:(mAll25.avg_friendships||0).toFixed(0),c:yoy(mAll24.avg_friendships,mAll25.avg_friendships),cl:'warm',sub:'Avg Monthly'},
    {l:'Family Coaching',v:(mAll25.avg_coaching||0).toFixed(0),c:yoy(mAll24.avg_coaching,mAll25.avg_coaching),cl:'rose',sub:'Avg Monthly'},
  ],'#sm-rel-kpis',{onClick:'clickRelKpi'});
  $('#sm-rel-area-title').textContent='Total Active Relationships By Area';
  renderAreaBars('avg_relationships','Total Active Relationships','#sm-rel-area',true,'d');

  // ── INTAKE ──
  const openRateS=to25s>0?(tm25s/to25s):0;
  const openRate24S=(mAll24.total_opened||0)>0?((mAll24.total_matched||0)/(mAll24.total_opened||1)):0;
  const goodFitPctS=ti25s>0?(to25s/ti25s):0;
  const goodFit24S=(mAll24.total_intake||0)>0?((mAll24.total_opened||0)/(mAll24.total_intake||1)):0;
  window._intakeMetrics=[
    {l:'Total Intakes',k:'total_intake',dec:false,bc:'d'},
    {l:'Good Fit Rate',k:'total_opened',dec:false,bc:'d'},
    {l:'Open Rate',k:'total_matched',dec:false,bc:'d'},
    {l:'Pending Cases',k:null,dec:false},
  ];
  renderKpis([
    {l:'Total Intakes',v:fmtN(ti25s),c:yoy(mAll24.total_intake,ti25s),cl:'green'},
    {l:'Good Fit Rate',v:fmtPct(goodFitPctS),c:yoy(goodFit24S,goodFitPctS),cl:goodFitPctS>=0.4?'green':'warm',sub:'Of All Intakes'},
    {l:'Open Rate (Of Good Fit Referrals)',v:fmtPct(openRateS),c:yoy(openRate24S,openRateS),cl:openRateS>=0.7?'green':'warm',sub:'Matched / Good Fits'},
  ],'#sm-intake-kpis',{onClick:'clickIntakeKpi'});



  // Intake by area (default: total intakes)
  $('#sm-intake-area-title').textContent='Total Intakes By Area';
  renderAreaBars('total_intake','Total Intakes','#sm-intake-area',false,'d');

  // ── OUTCOMES ──
  window._outcomesMetrics=[
    {l:'Graduations',k:'total_graduations',dec:false,bc:'d'},
    {l:'Hosted Days+Nights',k:'total_hosted',dec:false,bc:'c'},
    {l:'Hosted Nights',k:'total_hosted_nights',dec:false,bc:'b'},
    {l:'Hosted Days',k:'total_hosted_days',dec:false,bc:'d'},
  ];
  renderKpis([
    {l:'Graduations',v:fmtN(mAll25.total_graduations),c:yoy(mAll24.total_graduations,mAll25.total_graduations),cl:'green'},
    {l:'Hosted Days+Nights',v:fmtN(mAll25.total_hosted),c:yoy(mAll24.total_hosted,mAll25.total_hosted),cl:'blue'},
    {l:'Hosted Nights',v:fmtN(mAll25.total_hosted_nights),c:yoy(mAll24.total_hosted_nights,mAll25.total_hosted_nights),cl:'warm'},
    {l:'Hosted Days',v:fmtN(mAll25.total_hosted_days),c:yoy(mAll24.total_hosted_days,mAll25.total_hosted_days),cl:'green'},
  ],'#sm-outcomes-kpis',{onClick:'clickOutcomesKpi'});
  $('#sm-outcomes-area-title').textContent='Graduations By Area';
  renderAreaBars('total_graduations','Graduations','#sm-outcomes-area',false,'d');


  // ===== AREA SUMMARY =====
  {
    const ma24=mv24[a]||{};const ma25=mv25[a]||{};
    const aP=D.pnl_by_area[a];const aEq=D.equity[a]||0;const aNi=aP[18]['2025']||0;
    const aLf=D.local_fundraising['2025'][a]||0;const aLf24=D.local_fundraising['2024'][a]||0;
    const aOpex=aP[16]['2025']||0;const aTr=D.target_reserve[a]||0;
    const aStaff=D.staffing_2026[a]||0;const staffMo=aStaff>0?aEq/(aStaff/12):0;
    const aEff25=D.efficiency['2025'][a]||{};const aEff24=D.efficiency['2024'][a]||{};
    let sh='';
    sh+='<div class="chart-box full" style="margin-bottom:1.2rem"><h3>Financial Snapshot</h3>';
    sh+='<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.8rem;margin-bottom:1rem">';
    [{l:'Net Fundraising',v:fmtK(aLf),c:yoy(aLf24,aLf),cl:'green'},{l:'Operating Expenses',v:fmtK(aOpex),c:yoy(aP[16]['2024']||0,aOpex),cl:'warm'},{l:'Net Income',v:fmtK(aNi),cl:aNi>=0?'green':'rose'},{l:'Cash Reserves',v:fmtK(aEq),cl:'blue',sub:staffMo.toFixed(1)+' mo staffing'}].forEach(k=>{
      sh+='<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:0.8rem;border-left:3px solid var(--'+k.cl+')"><div style="font-size:0.68rem;font-weight:500;color:var(--text-dim);margin-bottom:0.2rem">'+k.l+'</div><div style="font-size:1.3rem;font-weight:700">'+k.v+'</div>'+(k.c!=null?'<div style="font-size:0.7rem;color:var(--'+k.cl+')">'+(k.c>0?'+':'')+(k.c*100).toFixed(0)+'% vs 2024</div>':'')+(k.sub?'<div style="font-size:0.68rem;color:var(--text-dim)">'+k.sub+'</div>':'')+'</div>';
    });
    sh+='</div></div>';
    sh+='<div class="chart-box full" style="margin-bottom:1.2rem"><h3>Mission Impact</h3>';
    sh+='<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.8rem;margin-bottom:1rem">';
    [{l:'Unique Families',v:fmtN(ma25.unique_families),c:yoy(ma24.unique_families,ma25.unique_families),cl:'green'},{l:'Graduations',v:fmtN(ma25.total_graduations),c:yoy(ma24.total_graduations,ma25.total_graduations),cl:'warm'},{l:'Volunteers',v:fmtN(ma25.dec_volunteers),c:yoy(ma24.dec_volunteers,ma25.dec_volunteers),cl:'blue'},{l:'Hosted Days+Nights',v:fmtN(ma25.total_hosted),c:yoy(ma24.total_hosted,ma25.total_hosted),cl:'rose'},{l:'Intakes',v:fmtN(ma25.total_intake),c:yoy(ma24.total_intake,ma25.total_intake),cl:''},{l:'Partner Churches',v:fmtN(ma25.dec_partner_churches),c:yoy(ma24.dec_partner_churches,ma25.dec_partner_churches),cl:'green'}].forEach(k=>{
      sh+='<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:0.8rem;border-left:3px solid var(--'+(k.cl||'accent')+')"><div style="font-size:0.68rem;font-weight:500;color:var(--text-dim);margin-bottom:0.2rem">'+k.l+'</div><div style="font-size:1.3rem;font-weight:700">'+k.v+'</div>'+(k.c!=null?'<div style="font-size:0.7rem;color:'+(k.c>0?'var(--green)':'var(--rose)')+'">'+(k.c>0?'+':'')+(k.c*100).toFixed(0)+'% vs 2024</div>':'')+'</div>';
    });
    sh+='</div></div>';
    sh+='<div class="chart-box full"><h3>Cost per Outcome</h3>';
    sh+='<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.8rem">';
    [{l:'Monthly Cost per Family',k:'_custom_fam',f:fmt},{l:'Monthly Cost per Individual',k:'_custom_ind',f:fmt},{l:'_skip',k:'_skip',f:fmt},{l:'Fundraising ROI',k:'fundraising_roi',f:function(v){return v!=null?v.toFixed(1)+'x':'\u2014'}}].forEach(m=>{
      const v25=aEff25[m.k],v24=aEff24[m.k];
      const cc=(v24!=null&&v25!=null&&v24!==0)?(v25-v24)/Math.abs(v24):null;
      sh+='<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:0.8rem;border-left:3px solid var(--accent)"><div style="font-size:0.68rem;font-weight:500;color:var(--text-dim);margin-bottom:0.2rem">'+m.l+'</div><div style="font-size:1.3rem;font-weight:700">'+m.f(v25)+'</div>'+(cc!=null?'<div style="font-size:0.7rem;color:var(--text-dim)">'+(cc>0?'+':'')+(cc*100).toFixed(0)+'% vs 2024</div>':'')+'</div>';
    });
    sh+='</div></div>';
    $('#lm-summary').innerHTML=sh;
  }

  // ===== OUTLOOK (in overview) =====
  const revGrowth=inc['2025']>0?(b26Rev-inc['2025'])/inc['2025']:null;
  const expGrowth=act25Exp>0?(b26TotalExp-act25Exp)/act25Exp:null;

  // Operating Expense Growth bars (program vs admin split)
  const exp23=pnl[16]['2023']||0;
  const exp24=pnl[16]['2024']||0;
  const exp25=opex['2025']||0;
  const exp26=(b26.total_expenses||0);
  const expMax=Math.max(exp23,exp24,exp25,exp26);
  const prog23=prog['2023']||0,prog24=prog['2024']||0,prog25=prog['2025']||0,prog26=b26.program_costs||0;
  const adm23=admin['2023']||0,adm24=admin['2024']||0,adm25=admin['2025']||0,adm26=b26.admin_costs||0;
  h='';
  [[2023,exp23,null,prog23,adm23],[2024,exp24,exp23,prog24,adm24],[2025,exp25,exp24,prog25,adm25],['2026 (Budget)',exp26,exp25,prog26,adm26]].forEach(([yr,v,prev,pv,av],i)=>{
    const chg=prev&&prev>0?((v-prev)/prev*100).toFixed(1)+'%':'';
    const chgColor=prev&&v>prev?'var(--rose)':prev&&v<prev?'var(--green)':'var(--text-dim)';
    const progPct=v>0?(pv/v*100):0;
    const admPct=v>0?(av/v*100):0;
    const barW=expMax?v/expMax*100:0;
    const colors=['a','b','d','e'];
    h+=`<div class="bar-group"><div class="bar-label"><span>${yr}${chg?' <span style="font-size:0.7rem;color:'+chgColor+';font-weight:700">'+((v-prev)>=0?'+':'')+chg+'</span>':''}</span><span>${fmtK(v)}</span></div>`;
    h+=`<div class="bar-track" style="height:24px"><div style="display:flex;height:100%;width:${barW}%;border-radius:4px;overflow:hidden">`;
    h+=`<div style="width:${progPct}%;background:var(--rose);display:flex;align-items:center;justify-content:center"><span style="color:#fff;font-size:0.6rem;font-weight:700">${progPct>=10?Math.round(progPct)+'%':''}</span></div>`;
    h+=`<div style="width:${admPct}%;background:rgba(190,60,60,0.5);display:flex;align-items:center;justify-content:center"><span style="color:#fff;font-size:0.6rem;font-weight:700">${admPct>=8?Math.round(admPct)+'%':''}</span></div>`;
    h+=`</div></div></div>`;
  });
  h+=`<div class="legend" style="margin-top:0.5rem"><div class="legend-item"><div class="legend-dot" style="background:var(--rose)"></div>Program</div><div class="legend-item"><div class="legend-dot" style="background:rgba(190,60,60,0.5)"></div>Admin</div></div>`;
  if($('#out-actual-budget'))$('#out-actual-budget').innerHTML=h;

  // Reserve coverage with 4.5-month staffing target
  if(a==='All Areas'){
    h='';
    const maxVal=Math.max(...D.field_areas.map(ar=>D.equity[ar]||0));
    [...D.field_areas].sort((x,y)=>(D.equity[y]||0)-(D.equity[x]||0)).forEach((ar,i)=>{
      const aeq=D.equity[ar]||0;
      const atr=D.target_reserve[ar]||0;
      const eqPct=maxVal?aeq/maxVal*100:0;
      const tgtPct=maxVal?Math.min(atr,aeq)/maxVal*100:0;
      const surplusPct=aeq>atr?(maxVal?(aeq-atr)/maxVal*100:0):0;
      const gapPct=aeq<atr?(maxVal?(atr-aeq)/maxVal*100:0):0;
      h+=`<div class="bar-group"><div class="bar-label"><span>${areaLabel(ar)}</span><span>${fmtK(aeq)} <span style="font-size:0.68rem;color:${(()=>{const s=D.staffing_2026[ar]||0;const m=s>0?aeq/(s/12):0;return m>=4.5?'var(--green)':m>=3?'var(--warm)':'var(--rose)'})()};font-weight:700">${(()=>{const s=D.staffing_2026[ar]||0;return s>0?(aeq/(s/12)).toFixed(1)+' mo staffing':'N/A'})()}</span></span></div><div class="bar-track"><div style="display:flex;height:100%;border-radius:4px;overflow:hidden"><div style="width:${tgtPct}%;background:var(--warm)"></div>${surplusPct>0?`<div style="width:${surplusPct}%;background:var(--accent)"></div>`:''}${gapPct>0?`<div style="width:${gapPct}%;background:#e0e0e0"></div>`:''}</div></div></div>`;
    });
    h+=`<div class="legend" style="margin-top:0.6rem"><div class="legend-item"><div class="legend-dot" style="background:var(--warm)"></div>Minimum Target</div><div class="legend-item"><div class="legend-dot" style="background:var(--accent)"></div>Growth Funds</div><div class="legend-item"><div class="legend-dot" style="background:#e0e0e0"></div>Gap to Target</div></div>`;
    if($('#out-reserve'))$('#out-reserve').innerHTML=h;
  } else {
    const atr=D.target_reserve[a]||0;
    const pctT=atr>0?eq/atr:0;
    const aStaff=D.staffing_2026[a]||0;
    const aStaffMo=aStaff>0?eq/(aStaff/12):0;
    const aGrowth=eq-atr;
    h=`<div style="text-align:center;padding:1.5rem 0">`;
    h+=`<div style="display:flex;gap:3rem;justify-content:center;margin-bottom:1.2rem">`;
    h+=`<div><div style="font-size:2.5rem;font-weight:700;color:${reserveMonths>=4.5?'var(--green)':reserveMonths>=3?'var(--warm)':'var(--rose)'}">${fmtK(eq)}</div><div style="font-size:0.8rem;font-weight:700;color:var(--text-muted)">Cash Balance (as of 12/31/25)</div></div>`;
    h+=`<div><div style="font-size:2.5rem;font-weight:700;color:var(--text)">${reserveMonths.toFixed(1)} Mo</div><div style="font-size:0.8rem;font-weight:700;color:var(--text-muted)">At Budgeted Burn Rate</div></div>`;
    h+=`</div>`;
    // Two-color bar: staffing reserve vs growth funds
    const barMax=Math.max(eq,atr);
    const tgtW=barMax>0?Math.min(Math.min(eq,atr)/barMax*100,100):0;
    const growthW=aGrowth>0?(aGrowth/barMax*100):0;
    const gapW=aGrowth<0?((atr-eq)/barMax*100):0;
    h+=`<div style="margin:0 2rem">`;
    h+=`<div class="bar-track" style="height:48px;border-radius:6px;overflow:hidden"><div style="display:flex;height:100%">`;
    h+=`<div style="width:${tgtW}%;background:var(--warm);display:flex;flex-direction:column;align-items:center;justify-content:center"><span style="color:rgba(255,255,255,0.85);font-weight:600;font-size:0.65rem;text-shadow:0 1px 2px rgba(0,0,0,0.2)">Minimum Target</span><span style="color:#fff;font-weight:600;font-size:0.88rem;text-shadow:0 1px 2px rgba(0,0,0,0.2)">${fmtK(Math.min(eq,atr))}</span></div>`;
    if(growthW>0){h+=`<div style="width:${growthW}%;background:var(--accent);display:flex;flex-direction:column;align-items:center;justify-content:center"><span style="color:rgba(255,255,255,0.85);font-weight:600;font-size:0.65rem;text-shadow:0 1px 2px rgba(0,0,0,0.2)">Growth Funds</span><span style="color:#fff;font-weight:600;font-size:0.88rem;text-shadow:0 1px 2px rgba(0,0,0,0.2)">${fmtK(aGrowth)}</span></div>`;}
    if(gapW>0){h+=`<div style="width:${gapW}%;background:#e0e0e0;display:flex;flex-direction:column;align-items:center;justify-content:center"><span style="color:var(--text-dim);font-weight:600;font-size:0.65rem">Gap to Target</span><span style="color:var(--rose);font-weight:600;font-size:0.88rem">${fmtK(Math.abs(aGrowth))}</span></div>`;}
    h+=`</div></div>`;
    h+=`<div style="margin-top:0.4rem;font-size:0.72rem;color:var(--text-dim)">Minimum Target = 4.5 Months of Staffing Costs</div>`;
    h+=`</div>`;
    h+=`</div>`;
    if($('#out-reserve'))$('#out-reserve').innerHTML=h;
  }

  // Fundraising YoY
  if(a==='All Areas'){
    // Show all areas as grouped bars
    const fa=D.field_areas;
    h='';
    const maxLf=Math.max(...fa.map(ar=>Math.max(lf['2023'][ar]||0,lf['2024'][ar]||0,lf['2025'][ar]||0,lf['2026'][ar]||0)));
    [...fa].sort((x,y)=>(lf['2025'][y]||0)-(lf['2025'][x]||0)).forEach(ar=>{
      const v23=lf['2023'][ar]||0,v24=lf['2024'][ar]||0,v25=lf['2025'][ar]||0,v26=lf['2026'][ar]||0;
      const chg=v24>0?((v25-v24)/Math.abs(v24)*100).toFixed(0)+'%':'—';
      const chgColor=v25>v24?'var(--green)':v25<v24?'var(--rose)':'var(--text-dim)';
      h+=`<div style="margin-bottom:1rem"><div style="font-size:0.78rem;font-weight:700;color:var(--text-muted);margin-bottom:0.3rem">${ar} <span style="font-size:0.72rem;color:${chgColor};font-weight:700;margin-left:0.4rem">${v24>0?((v25-v24)>=0?'+':'')+chg:''} YoY</span></div>`;
      [['2023',v23,'a'],['2024',v24,'b'],['2025',v25,'d'],['2026 (Budget)',v26,'e']].forEach(([yr,v,c])=>{
        h+=`<div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.15rem"><span style="font-size:0.68rem;color:var(--text-dim);width:72px;white-space:nowrap">${yr}</span><div class="bar-track" style="flex:1;height:14px"><div class="bar-fill ${c}" style="width:${maxLf>0?Math.max(v,0)/maxLf*100:0}%"></div></div><span style="font-size:0.72rem;min-width:55px;text-align:right;color:${v<0?'var(--rose)':''}">${fmtK(v)}</span></div>`;
      });
      h+=`</div>`;
    });
    h+=`<div class="legend" style="margin-top:0.4rem"><div class="legend-item"><div class="legend-dot" style="background:var(--accent)"></div>2023</div><div class="legend-item"><div class="legend-dot" style="background:var(--warm)"></div>2024</div><div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div>2025</div><div class="legend-item"><div class="legend-dot" style="background:var(--rose)"></div>2026 (Budget)</div></div>`;
    if($('#ov-local-fundraising'))$('#ov-local-fundraising').innerHTML=h;
  } else {
    // Single area: 3-year comparison
    const v23=lf['2023'][a]||0,v24=lf['2024'][a]||0,v25=lf['2025'][a]||0,v26=lf['2026'][a]||0;
    const maxV=Math.max(Math.abs(v23),Math.abs(v24),Math.abs(v25),Math.abs(v26));
    const chg24=v23?yoy(v23,v24):null;
    const chg25=v24?yoy(v24,v25):null;
    const chg26=v25?yoy(v25,v26):null;
    h='';
    [[2023,v23,null,'a'],[2024,v24,chg24,'b'],[2025,v25,chg25,'d'],['2026 (Budget)',v26,chg26,'e']].forEach(([yr,v,chg,c])=>{
      const chgStr=chg!=null?' <span style="font-size:0.7rem;color:'+(chg>0?'var(--green)':'var(--rose)')+';font-weight:700">'+(chg>=0?'+':'')+(chg*100).toFixed(1)+'%</span>':'';
      h+=`<div class="bar-group"><div class="bar-label"><span>${yr}${chgStr}</span><span style="color:${v<0?'var(--rose)':''}">${fmtK(v)}</span></div><div class="bar-track"><div class="bar-fill ${c}" style="width:${maxV>0?Math.max(v,0)/maxV*100:0}%"></div></div></div>`;
    });
    // Breakdown
    const pnlA=D.pnl_by_area[a];
    const inc25=pnlA[8]['2025']||0;
    const doa25=pnlA[4]['2025']||0;
    const cogs25=pnlA[10]['2025']||0;
    h+=`<div style="margin-top:0.8rem;font-size:0.78rem;color:var(--text-muted);display:flex;gap:1.5rem;flex-wrap:wrap"><span>2025 Gross Revenue: <strong>${fmtK(inc25)}</strong></span>${doa25?`<span>DOA Grant: <strong style="color:var(--rose)">−${fmtK(doa25)}</strong></span>`:''}<span>2025 Fundraising Costs: <strong style="color:var(--rose)">−${fmtK(cogs25)}</strong></span><span>= 2025 Net Fundraising: <strong>${fmtK(v25)}</strong></span></div>`;
    if($('#ov-local-fundraising'))$('#ov-local-fundraising').innerHTML=h;
  }

  renderImpactCost();
}

const icGetCost=(ar,yr)=>{
  const opex=D.pnl_by_area[ar]?.[16]?.[yr]||0;
  const fam=((D.mission_v2||{})[yr]||{})[ar]?.avg_families||0;
  const ind=((D.mission_v2||{})[yr]||{})[ar]?.avg_individuals||0;
  const hrs=((D.mission_v2||{})[yr]||{})[ar]?.total_service_hours||0;
  const volValue=hrs*34.07;
  return{fam:fam>0?opex/12/fam:null,ind:ind>0?opex/12/ind:null,vol:hrs>0?opex/hrs:null,netVol:hrs>0?volValue-opex:null};
};
window._icMetrics=[];
function renderImpactCost(){
  const fas=D.field_areas;const e25=D.efficiency['2025'];const e24=D.efficiency['2024'];const aa25=e25['All Areas']||{};const aa24=e24['All Areas']||{};
  // Uses global icGetCost function
  const icAll25=icGetCost('All Areas','2025');
  const icAll24=icGetCost('All Areas','2024');

  const metrics=[
    {l:'Monthly Cost per Family',k:'fam',cl:'green',lower:1},
    {l:'Monthly Cost per Individual',k:'ind',cl:'blue',lower:1},
    {l:'Cost per Volunteer Hour',k:'vol',cl:'warm',lower:1},
    {l:'Net Value of Volunteer Hours',k:'netVol',cl:'accent',lower:0,diverge:1},
  ];
  window._icMetrics=metrics.map(m=>({l:m.l,k:m.k,lower:m.lower,diverge:m.diverge||0,mfmt:m.k==='netVol'?fmtK:fmt,mbc:m.cl==='green'?'d':m.cl==='blue'?'c':m.cl==='warm'?'b':'a'}));
  const kpis=metrics.map(m=>{
    const v25=icAll25[m.k],v24=icAll24[m.k];
    const cc=(v24!=null&&v25!=null&&v24!==0)?(v25-v24)/Math.abs(v24):null;
    return{l:m.l,v:v25!=null?(m.k==='netVol'?fmtK(v25):fmt(Math.round(v25))):'\u2014',c:cc,cl:m.cl,inv:0};
  });
  const renderKpisIC=(kpis,el,opts)=>{let h='';kpis.forEach((k,i)=>{const dir=k.inv?(k.c>0.01?'down':k.c<-0.01?'up':'flat'):(k.c>0.01?'up':k.c<-0.01?'down':'flat');const click=opts&&opts.onClick?' onclick="'+opts.onClick+'('+i+')"':'';
  h+='<div class="kpi-card '+(k.cl||'')+(opts&&opts.onClick&&i===0?' kpi-selected':'')+'"'+click+'><div class="kpi-label">'+k.l+'</div><div class="kpi-value">'+k.v+'</div><div class="kpi-change '+dir+'">'+(k.c!=null?((dir==='up'?'\u25b2':dir==='down'?'\u25bc':'\u2014')+' '+(k.c>0?'+':'')+(k.c*100).toFixed(1)+'% vs 2024'):'')+'</div></div>'});$(el).innerHTML=h};
  renderKpisIC(kpis,'#ic-kpis',{onClick:'clickIcKpi'});
  renderIcBars(window._lastIcIdx||0);
}
function renderIcBars(idx){
  const m=window._icMetrics[idx];if(!m)return;
  const fas=D.field_areas.filter(ar=>ar!=='WI Statewide'&&ar!=='Chippewa Valley');
  let h='';

  // Special case: diverging bar for net value metrics
  if(m.diverge){
    // Statewide allocation for loaded values
    const getAllocD=(yr)=>{
      const swOpex=D.pnl_by_area['WI Statewide']?.[16]?.[yr]||0;
      const localTotal=fas.reduce((s,ar)=>s+(D.pnl_by_area[ar]?.[16]?.[yr]||0),0);
      const alloc={};
      fas.forEach(ar=>{
        const localOpex=D.pnl_by_area[ar]?.[16]?.[yr]||0;
        const share=localTotal>0?localOpex/localTotal:0;
        alloc[ar]=swOpex*share;
      });
      return alloc;
    };
    const allocD25=getAllocD('2025'),allocD24=getAllocD('2024');
    const getLoadedNetVol=(ar,yr)=>{
      const opex=(D.pnl_by_area[ar]?.[16]?.[yr]||0)+(yr==='2025'?allocD25[ar]||0:allocD24[ar]||0);
      const hrs=((D.mission_v2||{})[yr]||{})[ar]?.total_service_hours||0;
      return hrs>0?(hrs*34.07)-opex:null;
    };

    const vals=fas.map(ar=>{
      const c25=icGetCost(ar,'2025');
      const c24=icGetCost(ar,'2024');
      const f25=getLoadedNetVol(ar,'2025');
      const f24=getLoadedNetVol(ar,'2024');
      return{ar,v25:c25[m.k],v24:c24[m.k],f25,f24};
    }).filter(x=>x.v25!=null);
    vals.sort((a,b)=>b.v25-a.v25);
    const maxAbs=Math.max(...vals.map(x=>Math.max(Math.abs(x.v25),Math.abs(x.v24||0),Math.abs(x.f25||0),Math.abs(x.f24||0))));

    const divBar=(v,h_px,op)=>{
      const barPct=maxAbs>0?Math.abs(v)/maxAbs*50:0;
      const barCol=v>=0?'var(--green)':'var(--rose)';
      const opStyle=op<1?'opacity:'+op+';':'';
      if(v<0){
        return '<div style="display:flex;height:'+h_px+'px;'+opStyle+'"><div style="width:'+(50-barPct)+'%"></div><div style="width:'+barPct+'%;background:'+barCol+';border-radius:4px 0 0 4px"></div><div style="width:1px;background:var(--border)"></div><div style="width:50%;background:transparent"></div></div>';
      } else {
        return '<div style="display:flex;height:'+h_px+'px;'+opStyle+'"><div style="width:50%;background:transparent"></div><div style="width:1px;background:var(--border)"></div><div style="width:'+barPct+'%;background:'+barCol+';border-radius:0 4px 4px 0"></div><div style="flex:1"></div></div>';
      }
    };

    vals.forEach(({ar,v25,v24,f25,f24})=>{
      const vc5=v25>=0?'var(--green)':'var(--rose)';
      const vcf5=f25!=null?(f25>=0?'var(--green)':'var(--rose)'):'';
      const vc4=v24!=null?(v24>=0?'var(--green)':'var(--rose)'):'';
      const vcf4=f24!=null?(f24>=0?'var(--green)':'var(--rose)'):'';
      h+='<div style="margin-bottom:1.8rem">';
      h+='<div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">'+areaLabel(ar)+'</span></div>';
      // 2025 local
      h+='<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div style="flex:1;background:var(--surface2);border-radius:4px;overflow:hidden">'+divBar(v25,22,1)+'</div><span style="font-size:0.85rem;font-weight:700;min-width:160px;text-align:left;padding-left:0.5rem;color:'+vc5+'">'+fmtK(v25)+' <span style="font-size:0.68rem;color:'+(vcf5)+'">('+(f25!=null?fmtK(f25):'—')+' loaded)</span></span></div>';
      // 2025 loaded (thinner bar beneath)
      if(f25!=null)h+='<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.3rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px"></span><div style="flex:1;background:var(--surface2);border-radius:4px;overflow:hidden">'+divBar(f25,10,0.4)+'</div><span style="min-width:160px"></span></div>';
      // 2024 local
      if(v24!=null)h+='<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div style="flex:1;background:var(--surface2);border-radius:4px;overflow:hidden">'+divBar(v24,8,0.5)+'</div><span style="font-size:0.68rem;min-width:160px;text-align:left;padding-left:0.5rem;color:'+vc4+'">'+fmtK(v24)+' <span style="font-size:0.62rem;color:'+vcf4+'">('+fmtK(f24)+' loaded)</span></span></div>';
      h+='</div>';
    });

    h+='<div class="legend" style="margin-top:0.6rem"><div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div>Local Costs Only</div><div class="legend-item"><div class="legend-dot" style="background:var(--green);opacity:0.4"></div>Fully Loaded</div></div>';
    h+='<div style="font-size:0.72rem;color:var(--text-muted);margin-top:0.5rem">Based on $34.07/hr national value of volunteer time. Green = volunteer value exceeds operating costs.</div>';
    $('#ic-area-bars').innerHTML=h;
    buildViewSelector('#ic-area-title',m.l+' ');
    return;
  }

  // Calculate statewide allocation proportional to each area's share of local opex
  const getAlloc=(yr)=>{
    const swOpex=D.pnl_by_area['WI Statewide']?.[16]?.[yr]||0;
    const localTotal=fas.reduce((s,ar)=>s+(D.pnl_by_area[ar]?.[16]?.[yr]||0),0);
    const alloc={};
    fas.forEach(ar=>{
      const localOpex=D.pnl_by_area[ar]?.[16]?.[yr]||0;
      const share=localTotal>0?localOpex/localTotal:0;
      alloc[ar]=swOpex*share;
    });
    return alloc;
  };
  const alloc25=getAlloc('2025'),alloc24=getAlloc('2024');

  const getCostLoaded=(ar,yr)=>{
    const opex=(D.pnl_by_area[ar]?.[16]?.[yr]||0)+(yr==='2025'?alloc25[ar]||0:alloc24[ar]||0);
    const fam=((D.mission_v2||{})[yr]||{})[ar]?.avg_families||0;
    const ind=((D.mission_v2||{})[yr]||{})[ar]?.avg_individuals||0;
    const hrs=((D.mission_v2||{})[yr]||{})[ar]?.total_service_hours||0;
    return{fam:fam>0?opex/12/fam:null,ind:ind>0?opex/12/ind:null,vol:hrs>0?opex/hrs:null};
  };

  const vals=fas.map(ar=>{
    const c25=icGetCost(ar,'2025');
    const c24=icGetCost(ar,'2024');
    const f25=getCostLoaded(ar,'2025');
    const f24=getCostLoaded(ar,'2024');
    return{ar,v25:c25[m.k],v24:c24[m.k],f25:f25[m.k],f24:f24[m.k]};
  }).filter(x=>x.f25!=null);

  vals.sort((a,b)=>a.v25-b.v25);
  const maxV=Math.max(...vals.map(x=>x.f25||0));

  const bcToColor={'d':'var(--green)','c':'var(--blue)','b':'var(--warm)'};
  const barCol=bcToColor[m.mbc]||'var(--green)';

  vals.forEach(({ar,v25,v24,f25,f24})=>{
    const cc=yoy(v24,v25);
    const good=cc<0;
    const chgCol=cc!=null?(good?'var(--green)':'var(--rose)'):'var(--text-dim)';
    const pct2=cc!=null?(cc>=0?'+':'')+(cc*100).toFixed(0)+'%':'';
    const localW=maxV>0?v25/maxV*100:0;
    const allocW=maxV>0?(f25-v25)/maxV*100:0;
    const localW24=v24!=null&&maxV>0?v24/maxV*100:0;
    const allocW24=f24!=null&&v24!=null&&maxV>0?(f24-v24)/maxV*100:0;

    h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
    // 2025: segmented bar
    h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div style="display:flex;height:100%;border-radius:4px;overflow:hidden"><div style="width:${localW}%;background:${barCol}"></div><div style="width:${allocW}%;background:${barCol};opacity:0.3"></div></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:140px;text-align:left;padding-left:0.5rem">${fmt(Math.round(v25))} <span style="font-size:0.68rem;color:var(--text-dim)">(${fmt(Math.round(f25))} loaded)</span>${pct2?' <span style="color:'+chgCol+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
    // 2024
    if(v24!=null)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div style="display:flex;height:100%;border-radius:4px;overflow:hidden"><div style="width:${localW24}%;background:${barCol};opacity:0.5"></div><div style="width:${allocW24}%;background:${barCol};opacity:0.15"></div></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:140px;text-align:left;padding-left:0.5rem">${fmt(Math.round(v24))} <span style="font-size:0.62rem">(${fmt(Math.round(f24))} loaded)</span></span></div>`;
    h+=`</div>`;
  });

  h+=`<div class="legend" style="margin-top:0.6rem"><div class="legend-item"><div class="legend-dot" style="background:${barCol}"></div>Local Costs</div><div class="legend-item"><div class="legend-dot" style="background:${barCol};opacity:0.3"></div>+ Statewide Allocation</div></div>`;

  $('#ic-area-bars').innerHTML=h;
  buildViewSelector('#ic-area-title',m.l+' ');
}

function execIdxToGroup(idx){
  const starts=window._execGroupStarts||[];
  for(let i=starts.length-1;i>=0;i--){if(idx>=starts[i])return i;}
  return 0;
}
function renderExecKpiCards(selIdx){
  const groups=window._execGroups;if(!groups)return;
  const gi=execIdxToGroup(selIdx);
  const g=groups[gi];
  let kh='';
  g.kpis.forEach((kpi,ki)=>{
    const idx=g.startIdx+ki;
    const isSel=idx===selIdx;
    const arrow=kpi.up?'\u25b2':'\u25bc';
    const pct=kpi.chg!=null?(kpi.chg>0?'+':'')+(kpi.chg*100).toFixed(1)+'%':'';
    kh+=`<div class="kpi-card ${kpi.cl}${isSel?' kpi-selected':''}" onclick="clickExecKpi(${idx})" style="border-left:3px solid ${g.color}">`;
    kh+=`<div class="kpi-label" style="font-size:0.62rem">${kpi.label}</div>`;
    kh+=`<div class="kpi-value">${kpi.value}</div>`;
    if(pct)kh+=`<div class="kpi-change ${kpi.up?'up':'down'}">${arrow} ${pct} vs 2024</div>`;
    kh+=`</div>`;
  });
  $('#exec-kpis').innerHTML=kh;
}
function clickExecKpi(idx){
  window._lastExecIdx=idx;
  const group=execIdxToGroup(idx);
  const groups=window._execGroups||[];
  document.querySelectorAll('#exec-sidebar .exec-row').forEach((r,i)=>{
    if(i===group){
      r.classList.add('exec-row-active');
      r.style.borderLeftColor=groups[i]?groups[i].color:'transparent';
      r.style.background='var(--surface2)';
    } else {
      r.classList.remove('exec-row-active');
      r.style.borderLeftColor='transparent';
      r.style.background='transparent';
    }
  });
  renderExecKpiCards(idx);
  renderExecChart(idx);
}
function clickExecGroup(gi){
  const starts=window._execGroupStarts||[];
  clickExecKpi(starts[gi]||0);
}

function renderExecChart(idx){
  const m=window._execMetrics[idx];if(!m)return;
  const fas=D.field_areas.filter(ar=>ar!=='WI Statewide');
  let h='';

  if(m.type==='mission'){
    // Use renderAreaBars pattern
    const getMVal=(ar,yr)=>((D.mission_v2||{})[yr]||{})[ar]?.[m.k]||0;
    const maxV=Math.max(...fas.map(ar=>Math.max(getMVal(ar,'2025'),getMVal(ar,'2024'))));
    [...fas].sort((x,y)=>getMVal(y,'2025')-getMVal(x,'2025')).forEach(ar=>{
      const v5=getMVal(ar,'2025'),v4=getMVal(ar,'2024');
      const c2=yoy(v4,v5);
      const col2=c2!=null?(c2>0?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const pct2=c2!=null?(c2>=0?'+':'')+(c2*100).toFixed(0)+'%':'';
      const fmt5=m.dec?v5.toFixed(1):fmtN(Math.round(v5));
      const fmt4=m.dec?v4.toFixed(1):fmtN(Math.round(v4));
      const w5=maxV>0?v5/maxV*100:0,w4=maxV>0?v4/maxV*100:0;
      h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
      h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div class="bar-fill ${m.bc}" style="width:${w5}%"></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:100px;text-align:left;padding-left:0.5rem">${fmt5}${pct2?' <span style="color:'+col2+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
      if(v4)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${m.bc}" style="width:${w4}%;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${fmt4}</span></div>`;
      h+=`</div>`;
    });
  } else if(m.type==='cost'){
    const vals=fas.map(ar=>{
      const c25=icGetCost(ar,'2025'),c24=icGetCost(ar,'2024');
      return{ar,v25:c25[m.k],v24:c24[m.k]};
    }).filter(x=>x.v25!=null);
    vals.sort((a,b)=>a.v25-b.v25);
    const maxV=Math.max(...vals.map(x=>x.v25));
    vals.forEach(({ar,v25,v24})=>{
      const cc=yoy(v24,v25);
      const chgCol=cc!=null?(cc<0?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const pct2=cc!=null?(cc>=0?'+':'')+(cc*100).toFixed(0)+'%':'';
      const w25=maxV>0?v25/maxV*100:0,w24=v24!=null&&maxV>0?v24/maxV*100:0;
      h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
      h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div class="bar-fill ${m.bc}" style="width:${w25}%"></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:110px;text-align:left;padding-left:0.5rem">${fmt(Math.round(v25))}${pct2?' <span style="color:'+chgCol+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
      if(v24!=null)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${m.bc}" style="width:${w24}%;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:110px;text-align:left;padding-left:0.5rem">${fmt(Math.round(v24))}</span></div>`;
      h+=`</div>`;
    });
  } else if(m.type==='fin'){
    // Reuse financial bar renderer
    renderFinAreaBars(m.k,m.l,'#exec-chart',m.bc);
    $('#exec-chart-title').textContent=m.l+' By Area';
    return;
  }

  $('#exec-chart').innerHTML=h;
  $('#exec-chart-title').textContent=m.l+' By Area';
}

function clickIcKpi(idx){
  window._lastIcIdx=idx;
  const cards=document.querySelectorAll('#ic-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  renderIcBars(idx);
}

function clickDfKpi(idx){
  const cards=document.querySelectorAll('#df-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._dfMetrics[idx];
  $('#df-area-title').textContent=m.l+' By Area';
  renderFinAreaBars(m.k,m.l,'#df-area-bars');
}

function clickOvKpi(idx){
  const cards=document.querySelectorAll('#ov-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._ovMetrics[idx];
  window._lastOvIdx=idx;
  buildViewSelector('#ov-area-title',m.l+' ');
  renderFinAreaBars(m.k,m.l,'#ov-area-bars',m.bc);
}

function clickIntakeKpi(idx){
  const cards=document.querySelectorAll('#sm-intake-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._intakeMetrics[idx];
  $('#sm-intake-area-title').textContent=m.l+' By Area';
  if(m.k){renderAreaBars(m.k,m.l,'#sm-intake-area',m.dec,m.bc);}
  else{$('#sm-intake-area').innerHTML='<p style="color:var(--text-dim);font-size:0.82rem;padding:1rem 0">Calculated metric \u2014 no per-area breakdown available.</p>';}
}


function clickOutcomesKpi(idx){
  const cards=document.querySelectorAll('#sm-outcomes-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._outcomesMetrics[idx];
  $('#sm-outcomes-area-title').textContent=m.l+' By Area';
  if(m.k){renderAreaBars(m.k,m.l,'#sm-outcomes-area',m.dec,m.bc);}
}

function clickPeopleKpi(idx){
  const cards=document.querySelectorAll('#sm-people-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._peopleMetrics[idx];
  $('#sm-people-area-title').textContent=m.l+' By Area';
  if(m.k){renderAreaBars(m.k,m.l,'#sm-people-area',m.dec,m.bc);}
  else{$('#sm-people-area').innerHTML='<p style="color:var(--text-dim);font-size:0.82rem;padding:1rem 0">Estimated metric \u2014 no per-area breakdown available.</p>';}
}

function clickRelKpi(idx){
  const cards=document.querySelectorAll('#sm-rel-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._relMetrics[idx];
  $('#sm-rel-area-title').textContent=m.l+' By Area';
  if(m.k){renderAreaBars(m.k,m.l,'#sm-rel-area',m.dec,m.bc);}
}

function clickVolKpi(idx){
  const cards=document.querySelectorAll('#sm-vol-kpis .kpi-card');
  cards.forEach(c=>c.classList.remove('kpi-selected'));
  cards[idx].classList.add('kpi-selected');
  const m=window._volMetrics[idx];
  $('#sm-vol-area-title').textContent=m.l+' By Area';
  if(m.k){renderAreaBars(m.k,m.l,'#sm-vol-area',m.dec,m.bc);}
}


function clickRevKpi(idx){
  window._lastRevIdx=idx;
  document.querySelectorAll('#rev-kpis .kpi-card').forEach(c=>c.classList.remove('kpi-selected'));
  document.querySelectorAll('#rev-kpis .kpi-card')[idx].classList.add('kpi-selected');
  renderRevContent(idx);
}

function renderRevContent(idx){
  const a=area;const dP=D.pnl_by_area;const fas=D.field_areas;let h='';const m=window._revMetrics[idx];

  const get3yr=(ar,fn)=>[fn(ar,'2023'),fn(ar,'2024'),fn(ar,'2025')];

  if(m.k==='waterfall'){
    // Waterfall stays as-is (not by-area)
    const dp=dP[a];
    const maxW=Math.max(dp[8]['2023']||0,dp[8]['2024']||0,dp[8]['2025']||0);
    [['2025',1,22],['2024',0.5,8],['2023',0.3,8]].forEach(([yr,op,ht])=>{
      const g=dp[8][yr]||0,cc=dp[9][yr]||0,n=g-cc;
      const gW=maxW>0?g/maxW*100:0,nPct=g>0?n/g*100:0,cPct=g>0?cc/g*100:0;
      h+=`<div style="margin-bottom:0.6rem"><div style="display:flex;justify-content:space-between;font-size:0.75rem;margin-bottom:0.15rem"><span style="font-weight:700">${yr}</span><span style="color:var(--text-dim)">${fmtK(g)} gross \u2192 ${fmtK(cc)} costs \u2192 <strong style="color:var(--green)">${fmtK(n)} net</strong></span></div><div class="bar-track" style="height:${ht}px"><div style="display:flex;height:100%;width:${gW}%;border-radius:4px;overflow:hidden;opacity:${op}"><div style="width:${nPct}%;background:var(--green)"></div><div style="width:${cPct}%;background:var(--warm)"></div></div></div></div>`;
    });
    h+=`<div class="legend" style="margin-top:0.4rem"><div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div>Net</div><div class="legend-item"><div class="legend-dot" style="background:var(--warm)"></div>Costs</div></div>`;
    buildViewSelector('#rev-area-title','Gross \u2192 Costs \u2192 Net Fundraising ');
  } else if(m.k==='roi'){
    const getROI=(ar,yr)=>{const p=dP[ar];const g=p[8][yr]||0;const cc=p[9][yr]||0;return cc>0?g/cc:0};
    const vals=fas.map(ar=>({ar,v25:getROI(ar,'2025'),v24:getROI(ar,'2024'),v23:getROI(ar,'2023')})).filter(x=>x.v25>0);
    vals.sort((a,b)=>b.v25-a.v25);
    const maxV=Math.max(...vals.map(x=>Math.max(x.v25,x.v24,x.v23)));
    vals.forEach(({ar,v25,v24,v23})=>{
      const c2=yoy(v24,v25),c1=yoy(v23,v24);
      const col2=c2!=null?(c2>0?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const col1=c1!=null?(c1>0?'var(--green)':'var(--rose)'):'var(--text-dim)';
      const pct2=c2!=null?(c2>=0?'+':'')+(c2*100).toFixed(0)+'%':'';
      const pct1=c1!=null?(c1>=0?'+':'')+(c1*100).toFixed(0)+'%':'';
      h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
      h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div class="bar-fill ${m.bc||'c'}" style="width:${maxV>0?v25/maxV*100:0}%"></div></div><span style="font-size:0.85rem;font-weight:700;min-width:100px;text-align:left;padding-left:0.5rem">${v25.toFixed(0)}x${pct2?' <span style="color:'+col2+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
      if(v24)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${m.bc||'c'}" style="width:${maxV>0?v24/maxV*100:0}%;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${v24.toFixed(0)}x${pct1?' <span style="color:'+col1+';font-weight:600">'+pct1+'</span>':''}</span></div>`;
      if(v23)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2023</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${m.bc||'c'}" style="width:${maxV>0?v23/maxV*100:0}%;opacity:0.3"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${v23.toFixed(0)}x</span></div>`;
      h+=`</div>`;
    });
    buildViewSelector('#rev-area-title','Fundraising ROI ');
  } else if(m.k==='sharing'){
    const getRS=(ar,yr)=>D.rev_sharing[yr][ar]||0;
    const rsVals=fas.map(ar=>({ar,v25:getRS(ar,'2025'),v24:getRS(ar,'2024'),v23:getRS(ar,'2023')})).filter(x=>x.v25!==0||x.v24!==0);
    if(rsVals.length){
      rsVals.sort((a,b)=>b.v25-a.v25);
      const maxRS=Math.max(...rsVals.map(x=>Math.max(Math.abs(x.v25),Math.abs(x.v24),Math.abs(x.v23))));
      rsVals.forEach(({ar,v25,v24,v23})=>{
        const col25=v25>=0?'var(--green)':'var(--rose)';
        const col24=v24>=0?'var(--green)':'var(--rose)';
        const col23=v23>=0?'var(--green)':'var(--rose)';
        h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
        h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div style="width:${maxRS>0?Math.abs(v25)/maxRS*100:0}%;height:100%;background:${col25};border-radius:4px"></div></div><span style="font-size:0.85rem;font-weight:700;min-width:100px;text-align:left;padding-left:0.5rem;color:${col25}">${v25?fmtK(v25):'\u2014'}</span></div>`;
        if(v24)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div style="width:${maxRS>0?Math.abs(v24)/maxRS*100:0}%;height:100%;background:${col24};border-radius:4px;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${fmtK(v24)}</span></div>`;
        if(v23)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2023</span><div class="bar-track" style="flex:1;height:8px"><div style="width:${maxRS>0?Math.abs(v23)/maxRS*100:0}%;height:100%;background:${col23};border-radius:4px;opacity:0.3"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:100px;text-align:left;padding-left:0.5rem">${fmtK(v23)}</span></div>`;
        h+=`</div>`;
      });
    } else {h+='<p style="color:var(--text-dim);padding:1rem 0">No revenue sharing activity.</p>';}
    buildViewSelector('#rev-area-title','Revenue Sharing ');
  } else if(m.k==='sources'){
    const srcs25=(D.rev_sources['2025'][a]||[]).slice().sort((a,b)=>b.value-a.value);
    const rs25v=D.rev_sharing['2025'][a]||0;
    const grossRev=D.pnl_by_area[a][8]['2025']||0;
    const totalWithRS=grossRev+(rs25v>0?rs25v:0);
    const items=srcs25.map(s=>({l:s.label,v:s.value,pct:totalWithRS>0?(s.value/totalWithRS*100):0}));
    if(rs25v>0)items.push({l:'Revenue Sharing (Received)',v:rs25v,pct:totalWithRS>0?(rs25v/totalWithRS*100):0,isRS:true});
    items.sort((a,b)=>b.v-a.v);
    const maxPct=Math.max(...items.map(x=>x.pct));
    items.forEach(it=>{
      const w=maxPct>0?it.pct/maxPct*100:0;
      const col=it.isRS?'var(--accent)':'var(--green)';
      h+=`<div style="margin-bottom:0.8rem"><div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:0.2rem"><span style="font-size:0.82rem;font-weight:700;${it.isRS?'color:var(--accent)':''}">${it.l}</span><span style="font-size:0.82rem;font-weight:600">${it.pct.toFixed(0)}% <span style="font-size:0.72rem;color:var(--text-dim);font-weight:600">${fmtK(it.v)}</span></span></div>`;
      h+=`<div class="bar-track" style="height:14px"><div style="width:${w}%;height:100%;background:${col};border-radius:4px;opacity:${it.isRS?0.7:0.8}"></div></div></div>`;
    });
    buildViewSelector('#rev-area-title','Revenue Mix ');
  }
  $('#rev-detail-content').innerHTML=h;
}

function clickExpKpi(idx){
  window._lastExpIdx=idx;
  document.querySelectorAll('#exp-kpis .kpi-card').forEach(c=>c.classList.remove('kpi-selected'));
  document.querySelectorAll('#exp-kpis .kpi-card')[idx].classList.add('kpi-selected');
  renderExpContent(idx);
}

function renderExpContent(idx){
  const a=area;const fas=D.field_areas;let h='';const m=window._expMetrics[idx];
  const getExpYr=(ar,k,yr)=>{const p=D.pnl_by_area[ar];if(k==='program')return p[14][yr]||0;if(k==='admin')return p[15][yr]||0;if(k==='fundraising')return p[9][yr]||0;if(k==='total')return(p[16][yr]||0)+(p[9][yr]||0);return 0};
  if(m.k==='admin'){h+=`<div style="margin-bottom:1rem;font-size:0.85rem;font-weight:600;color:var(--warm)">Statewide Support absorbs the majority of admin costs \u2014 covering finance, HR, and operations that serve all areas.</div>`}
  const vals=fas.map(ar=>({ar,v25:getExpYr(ar,m.k,'2025'),v24:getExpYr(ar,m.k,'2024'),v23:getExpYr(ar,m.k,'2023')}));
  vals.sort((a,b)=>b.v25-a.v25);
  const maxV=Math.max(...vals.map(x=>Math.max(x.v25,x.v24,x.v23)));
  vals.forEach(({ar,v25,v24,v23})=>{
    const c2=yoy(v24,v25),c1=yoy(v23,v24);
    const isExp=true;
    const col2=c2!=null?(c2<0?'var(--green)':'var(--rose)'):'var(--text-dim)';
    const col1=c1!=null?(c1<0?'var(--green)':'var(--rose)'):'var(--text-dim)';
    const pct2=c2!=null?(c2>=0?'+':'')+(c2*100).toFixed(0)+'%':'';
    const pct1=c1!=null?(c1>=0?'+':'')+(c1*100).toFixed(0)+'%':'';
    const aTotal=getExpYr(ar,  'total','2025');
    const pctOfTotal=aTotal>0?(v25/aTotal*100).toFixed(0)+'%':'';
    const w25=maxV>0?v25/maxV*100:0;
    const w24=maxV>0?v24/maxV*100:0;
    const w23=maxV>0?v23/maxV*100:0;
    h+=`<div style="margin-bottom:1.8rem"><div style="margin-bottom:0.3rem"><span style="font-size:0.95rem;font-weight:600;color:var(--text)">${areaLabel(ar)}</span></div>`;
    h+=`<div style="display:flex;align-items:center;gap:0.4rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px;font-weight:700">2025</span><div class="bar-track" style="flex:1;height:22px"><div class="bar-fill ${m.bc||'b'}" style="width:${w25}%"></div></div><span style="font-size:0.85rem;font-weight:700;color:var(--text);min-width:120px;text-align:left;padding-left:0.5rem">${fmtK(v25)}${pctOfTotal?' <span style="font-size:0.65rem;color:var(--text-dim)">'+pctOfTotal+'</span>':''}${pct2?' <span style="color:'+col2+';font-weight:600;font-size:0.72rem">'+pct2+'</span>':''}</span></div>`;
    if(v24)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.5rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2024</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${m.bc||'b'}" style="width:${w24}%;opacity:0.5"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:120px;text-align:left;padding-left:0.5rem">${fmtK(v24)}${pct1?' <span style="color:'+col1+';font-weight:600">'+pct1+'</span>':''}</span></div>`;
    if(v23)h+=`<div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem"><span style="font-size:0.7rem;color:var(--text-muted);width:28px">2023</span><div class="bar-track" style="flex:1;height:8px"><div class="bar-fill ${m.bc||'b'}" style="width:${w23}%;opacity:0.3"></div></div><span style="font-size:0.68rem;color:var(--text-dim);min-width:120px;text-align:left;padding-left:0.5rem">${fmtK(v23)}</span></div>`;
    h+=`</div>`;
  });
  $('#exp-detail-content').innerHTML=h;buildViewSelector('#exp-area-title',m.l+' ');
}

function switchDF(btn){
  document.querySelectorAll('#pnl .sm-tab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.df-sub').forEach(s=>s.classList.remove('active'));
  $('#df-'+btn.dataset.df).classList.add('active');
}

function switchSM(btn){
  document.querySelectorAll('.sm-tab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.sm-sub').forEach(s=>s.classList.remove('active'));
  $('#sm-'+btn.dataset.sm).classList.add('active');
}


function shareLmLink(){
  const url=new URL(window.location.href);
  url.searchParams.set('area',area);url.searchParams.set('tab','local-mission');
  navigator.clipboard.writeText(url.toString()).then(()=>{
    const btn=$('#lm-share-btn');btn.textContent='Link Copied!';btn.style.background='var(--green)';
    setTimeout(()=>{btn.textContent='Copy Share Link';btn.style.background='var(--accent)'},2000);
  });
}
try{const params=new URLSearchParams(window.location.search);
if(params.get('area')){area=params.get('area');}
if(params.get('tab')){setTimeout(()=>{const btn=document.querySelector('.nav-btn[data-s="'+params.get('tab')+'"]');if(btn)btn.click()},100);}
}catch(e){}

render();

// ===== GL DRILL-DOWN (Side Peek) =====
(function(){
  if(!D.gl_available_years||!D.gl_available_years.length) return;

  const csrfToken=document.querySelector('meta[name="csrf-token"]');
  const headers={'Accept':'application/json'};
  if(csrfToken) headers['X-CSRF-TOKEN']=csrfToken.content;

  // Build peek panel DOM
  const overlay=document.createElement('div');
  overlay.className='gl-peek-overlay';
  document.body.appendChild(overlay);

  const peek=document.createElement('div');
  peek.className='gl-peek';
  peek.innerHTML='<div class="gl-peek-header"><h3 id="gl-peek-title">GL Detail</h3><button class="gl-peek-close">&times;</button></div><div class="gl-peek-body" id="gl-peek-body"></div>';
  document.body.appendChild(peek);

  const peekTitle=peek.querySelector('#gl-peek-title');
  const peekBody=peek.querySelector('#gl-peek-body');
  const closeBtn=peek.querySelector('.gl-peek-close');

  function openPeek(title){
    peekTitle.textContent=title;
    peekBody.innerHTML='<div class="gl-loading">Loading...</div>';
    peek.classList.add('active');
    overlay.classList.add('active');
  }
  function closePeek(){
    peek.classList.remove('active');
    overlay.classList.remove('active');
    document.querySelectorAll('.gl-drillable.gl-open').forEach(r=>r.classList.remove('gl-open'));
  }
  closeBtn.addEventListener('click',closePeek);
  overlay.addEventListener('click',closePeek);
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&peek.classList.contains('active'))closePeek()});

  function getCurrentArea(){
    const sel=document.querySelector('.area-sel.active,.area-btn.active');
    return sel?sel.textContent.trim():'All Areas';
  }
  function getAreaId(areaName){return D.area_ids[areaName]||null}

  // Click on P&L row → open peek with sub-accounts
  document.addEventListener('click',function(e){
    const row=e.target.closest('.gl-drillable');
    if(row&&row.closest('#cashflow-content')){
      e.stopPropagation();
      const liIdx=parseInt(row.dataset.liIdx);
      const liId=D.pnl_line_item_ids[liIdx];
      if(!liId) return;

      // Highlight clicked row
      document.querySelectorAll('.gl-drillable.gl-open').forEach(r=>r.classList.remove('gl-open'));
      row.classList.add('gl-open');

      const label=row.querySelector('td').textContent.replace(/^\s*[▸▹►]\s*/,'').trim();
      openPeek(label);
      fetchLineItemDetail(liId,label);
      return;
    }

    // Click sub-account row inside peek → toggle transactions
    const acctRow=e.target.closest('.gl-acct-row');
    if(acctRow&&peek.contains(acctRow)){
      e.stopPropagation();
      const acctId=parseInt(acctRow.dataset.acctId);
      const existing=acctRow.nextElementSibling;
      if(existing&&existing.classList.contains('gl-txn-wrap')){
        existing.remove();
        acctRow.classList.remove('gl-acct-open');
        return;
      }
      // Close other open txn panels
      peekBody.querySelectorAll('.gl-txn-wrap').forEach(w=>w.remove());
      peekBody.querySelectorAll('.gl-acct-row.gl-acct-open').forEach(r=>r.classList.remove('gl-acct-open'));
      acctRow.classList.add('gl-acct-open');
      fetchAccountTxns(acctRow,acctId);
    }
  });

  function fetchLineItemDetail(lineItemId,label){
    const areaName=getCurrentArea();
    const areaId=getAreaId(areaName);
    const params=new URLSearchParams({line_item_id:lineItemId,fiscal_year:2025});
    if(areaId) params.set('area_id',areaId);

    peekTitle.textContent=label+(areaName!=='All Areas'?' — '+areaName:'');

    fetch('/api/gl/line-item-detail?'+params,{headers})
      .then(r=>r.json())
      .then(data=>{
        if(!data.accounts||!data.accounts.length){
          peekBody.innerHTML='<div style="color:var(--text-dim);text-align:center;padding:2rem">No GL accounts mapped to this line item</div>';
          return;
        }
        peekBody.innerHTML=buildDetailTable(data);
      })
      .catch(()=>{
        peekBody.innerHTML='<div style="color:var(--rose);text-align:center;padding:2rem">Failed to load GL detail</div>';
      });
  }

  function buildDetailTable(data){
    const years=data.years||[2025];
    let total=0;
    let h='<table><thead><tr><th>GL Account</th>';
    years.forEach(y=>h+='<th style="text-align:right">'+y+'</th>');
    h+='<th style="text-align:right">#Txns</th></tr></thead><tbody>';

    data.accounts.forEach(a=>{
      h+='<tr class="gl-acct-row" data-acct-id="'+a.id+'">';
      h+='<td style="white-space:nowrap"><span style="color:var(--accent);font-size:0.65rem;margin-right:4px">&#9656;</span>'+a.account_number+' '+a.account_name+'</td>';
      years.forEach(y=>{
        const v=a.years[y]?a.years[y].total:0;
        if(y===years[years.length-1]) total+=v;
        h+='<td style="text-align:right;font-variant-numeric:tabular-nums">'+fmt(v)+'</td>';
      });
      const lastYear=years[years.length-1];
      const cnt=a.years[lastYear]?a.years[lastYear].count:0;
      h+='<td style="text-align:right;color:var(--text-dim)">'+cnt+'</td>';
      h+='</tr>';
    });

    h+='</tbody></table>';
    h+='<div style="margin-top:0.8rem;padding-top:0.6rem;border-top:2px solid var(--border);display:flex;justify-content:space-between;font-size:0.82rem;font-weight:700">';
    h+='<span>Total</span><span style="font-variant-numeric:tabular-nums">'+fmt(total)+'</span></div>';
    h+='<div style="margin-top:0.8rem;font-size:0.68rem;color:var(--text-dim)">Click an account to view individual transactions</div>';
    return h;
  }

  function fetchAccountTxns(afterRow,acctId){
    const areaName=getCurrentArea();
    const areaId=getAreaId(areaName);
    const params=new URLSearchParams({gl_account_id:acctId,fiscal_year:2025});
    if(areaId) params.set('area_id',areaId);

    const wrap=document.createElement('tr');
    wrap.className='gl-txn-wrap';
    const td=document.createElement('td');
    td.colSpan=afterRow.children.length;
    td.innerHTML='<div class="gl-loading" style="padding:0.6rem">Loading transactions...</div>';
    wrap.appendChild(td);
    afterRow.after(wrap);

    fetch('/api/gl/account-transactions?'+params,{headers})
      .then(r=>r.json())
      .then(data=>{
        if(!data.transactions||!data.transactions.length){
          td.innerHTML='<div style="padding:0.6rem;color:var(--text-dim);font-size:0.75rem;text-align:center">No transactions found</div>';
          return;
        }
        td.innerHTML=buildTxnTable(data.transactions);
      })
      .catch(()=>{
        td.innerHTML='<div style="color:var(--rose);font-size:0.75rem;padding:0.6rem">Failed to load transactions</div>';
      });
  }

  function buildTxnTable(txns){
    let total=0;
    let h='<div class="gl-txn-scroll"><table><thead><tr><th>Date</th><th>Type</th><th>Name</th><th>Memo</th><th>Area</th><th style="text-align:right">Amount</th></tr></thead><tbody>';
    txns.forEach(t=>{
      total+=t.amount;
      const color=t.amount<0?'color:var(--rose)':'';
      h+='<tr><td style="white-space:nowrap">'+t.date+'</td><td>'+(t.type||'')+'</td><td>'+(t.name||'')+'</td><td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'+(t.memo||'')+'</td><td>'+(t.area||'<span style="color:var(--text-dim)">—</span>')+'</td><td style="text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap;'+color+'">'+fmt(t.amount)+'</td></tr>';
    });
    h+='</tbody></table></div>';
    h+='<div style="padding:0.4rem 0.5rem;font-size:0.72rem;font-weight:700;display:flex;justify-content:space-between;border-top:1px solid var(--border)">';
    h+='<span>'+txns.length+' transactions</span><span style="font-variant-numeric:tabular-nums">'+fmt(total)+'</span></div>';
    return h;
  }
})();

// ── Theme Toggle ──
(function(){
  var btn=document.getElementById('themeToggle');
  if(!btn)return;
  btn.addEventListener('click',function(){
    var html=document.documentElement;
    var isDark=html.getAttribute('data-theme')==='dark';
    if(isDark){
      html.removeAttribute('data-theme');
      localStorage.setItem('sfcwi-theme','light');
    }else{
      html.setAttribute('data-theme','dark');
      localStorage.setItem('sfcwi-theme','dark');
    }
  });
})();
</script>
</body>
</html>
