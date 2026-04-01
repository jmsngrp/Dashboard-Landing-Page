<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SFCWI Admin - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #ffffff;
            --surface: #ffffff;
            --surface2: #f7f6f3;
            --surface3: #edece9;
            --border: #e8e8e6;
            --text: #27303B;
            --text-muted: #787774;
            --text-dim: #a3a29e;
            --accent: #4a88b0;
            --accent-dim: rgba(107,158,200,0.08);
            --green: #6b9146;
            --rose: #7b649a;
            --sidebar-w: 240px;
            --topbar-h: 52px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Lato', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── Sidebar ─────────────────────────────────── */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--surface2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            padding: 16px 20px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 8px 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 400;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
        }

        .sidebar-nav a:hover {
            background: var(--surface3);
            color: var(--text);
        }

        .sidebar-nav a.active {
            background: var(--surface3);
            color: var(--text);
            font-weight: 600;
            border-left-color: var(--accent);
        }

        .sidebar-nav .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 14px;
            opacity: 0.7;
        }

        .sidebar-nav a.active .nav-icon,
        .sidebar-nav a:hover .nav-icon {
            opacity: 1;
        }

        .sidebar-section {
            padding: 18px 20px 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* ── Top Bar ─────────────────────────────────── */
        .admin-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 90;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-right a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 400;
            padding: 6px 12px;
            border-radius: 5px;
            transition: all 0.15s ease;
        }

        .topbar-right a:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--text);
        }

        .hamburger svg {
            width: 22px;
            height: 22px;
        }

        /* ── Main Content ────────────────────────────── */
        .admin-main {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── Flash Messages ──────────────────────────── */
        .flash-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: var(--green);
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13.5px;
        }

        .flash-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: var(--rose);
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13.5px;
        }

        .validation-errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: var(--rose);
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .validation-errors ul {
            margin: 6px 0 0 18px;
        }

        /* ── Page Header ─────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        /* ── Buttons ─────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Lato', sans-serif;
            border-radius: 6px;
            border: 1px solid var(--border);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1.4;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: #3c7499;
            border-color: #3c7499;
        }

        .btn-secondary {
            background: transparent;
            color: var(--text);
        }

        .btn-secondary:hover {
            background: var(--surface2);
        }

        .btn-danger {
            background: var(--rose);
            color: #fff;
            border-color: var(--rose);
        }

        .btn-danger:hover {
            background: #6a5486;
            border-color: #6a5486;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* ── Tables ──────────────────────────────────── */
        .admin-table {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            border-spacing: 0;
            overflow: hidden;
        }

        .admin-table thead th {
            background: var(--surface2);
            padding: 10px 14px;
            text-align: left;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        .admin-table tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid var(--surface3);
            font-size: 13.5px;
        }

        .admin-table tbody tr:last-child td {
            border-bottom: none;
        }

        .admin-table tbody tr:hover {
            background: var(--surface2);
        }

        .admin-table .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .admin-table .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        /* ── Forms ───────────────────────────────────── */
        .admin-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            font-size: 14px;
            font-family: 'Lato', sans-serif;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--surface);
            color: var(--text);
            transition: border-color 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-dim);
        }

        select.form-control {
            appearance: auto;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--surface3);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
        }

        /* ── Filter Bar ──────────────────────────────── */
        .filter-bar {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-bar .form-control {
            width: auto;
            min-width: 160px;
        }

        /* ── Overlay for mobile ──────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            z-index: 99;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-topbar {
                left: 0;
            }

            .admin-main {
                margin-left: 0;
                padding: 20px 16px;
            }

            .hamburger {
                display: block;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-brand">SFCWI Admin</div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">&#9632;</span> Dashboard
            </a>

            <div class="sidebar-section">Data Management</div>

            <a href="{{ route('admin.areas.index') }}" class="{{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9670;</span> Areas
            </a>
            <a href="{{ route('admin.pnl.index') }}" class="{{ request()->routeIs('admin.pnl.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9654;</span> P&amp;L Data
            </a>
            <a href="{{ route('admin.mission.index') }}" class="{{ request()->routeIs('admin.mission.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9733;</span> Mission Metrics
            </a>
            <a href="{{ route('admin.efficiency.index') }}" class="{{ request()->routeIs('admin.efficiency.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9881;</span> Efficiency Metrics
            </a>
            <a href="{{ route('admin.revenue-sources.index') }}" class="{{ request()->routeIs('admin.revenue-sources.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9650;</span> Revenue Sources
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9660;</span> Expense Summaries
            </a>
            <a href="{{ route('admin.budgets.index') }}" class="{{ request()->routeIs('admin.budgets.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9635;</span> Budgets
            </a>
            <a href="{{ route('admin.financial-snapshots.index') }}" class="{{ request()->routeIs('admin.financial-snapshots.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9673;</span> Financial Snapshots
            </a>
            <a href="{{ route('admin.revenue-sharing.index') }}" class="{{ request()->routeIs('admin.revenue-sharing.*') ? 'active' : '' }}">
                <span class="nav-icon">&#8644;</span> Revenue Sharing
            </a>
            <a href="{{ route('admin.local-fundraising.index') }}" class="{{ request()->routeIs('admin.local-fundraising.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9829;</span> Local Fundraising
            </a>

            <div class="sidebar-section">Budget System</div>

            <a href="{{ route('admin.budget-buckets.index') }}" class="{{ request()->routeIs('admin.budget-buckets.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9638;</span> Budget Buckets
            </a>
            <a href="{{ route('admin.bucket-amounts.index') }}" class="{{ request()->routeIs('admin.bucket-amounts.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9641;</span> Bucket Amounts
            </a>
            <a href="{{ route('admin.starting-cash.index') }}" class="{{ request()->routeIs('admin.starting-cash.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9670;</span> Starting Cash
            </a>

            <div class="sidebar-section">Dashboard Config</div>

            <a href="{{ route('admin.highlights.index') }}" class="{{ request()->routeIs('admin.highlights.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9733;</span> Highlight Groups
            </a>
            <a href="{{ route('admin.highlight-kpis.index') }}" class="{{ request()->routeIs('admin.highlight-kpis.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9679;</span> Highlight KPIs
            </a>
            <a href="{{ route('admin.design.index') }}" class="{{ request()->routeIs('admin.design.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9674;</span> Design
            </a>

            <div class="sidebar-section">GL Integration</div>

            <a href="{{ route('admin.qbo.index') }}" class="{{ request()->routeIs('admin.qbo.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9729;</span> QuickBooks Online
            </a>
            <a href="{{ route('admin.gl-import.index') }}" class="{{ request()->routeIs('admin.gl-import.*') ? 'active' : '' }}">
                <span class="nav-icon">&#8645;</span> GL Import
            </a>
            <a href="{{ route('admin.gl-accounts.index') }}" class="{{ request()->routeIs('admin.gl-accounts.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9776;</span> GL Account Mapping
            </a>
            <a href="{{ route('admin.area-aliases.index') }}" class="{{ request()->routeIs('admin.area-aliases.*') ? 'active' : '' }}">
                <span class="nav-icon">&#8646;</span> Area Aliases
            </a>

            <div class="sidebar-section">Tools</div>

            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="nav-icon">&#9775;</span> User Permissions
            </a>
            <a href="{{ route('admin.import.index') }}" class="{{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
                <span class="nav-icon">&#8593;</span> CSV Import
            </a>
        </nav>
    </aside>

    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Top Bar -->
    <header class="admin-topbar">
        <div class="topbar-left">
            <button class="hamburger" id="hamburgerBtn" aria-label="Toggle sidebar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="topbar-title">@yield('title', 'Admin')</span>
        </div>
        <div class="topbar-right">
            <a href="{{ route('dashboard') }}">View Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="validation-errors">
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const hamburger = document.getElementById('hamburgerBtn');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    </script>
</body>
</html>
