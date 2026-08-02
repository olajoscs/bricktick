<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Bricktick') }}</title>
    <style>
        :root {
            --text: #1b1b18;
            --muted: #706f6c;
            --border: #e3e3e0;
            --bg: #fdfdfc;
            --accent: #1b1b18;
            --danger: #b91c1c;
            --success: #166534;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, 'Segoe UI', Roboto, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: var(--text);
            background: var(--bg);
        }
        .admin-shell { max-width: 960px; margin: 0 auto; padding: 24px 20px 48px; }
        .admin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 16px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }
        .admin-header h1 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .admin-header nav a { color: var(--muted); text-decoration: none; margin-right: 16px; }
        .admin-header nav a:hover { color: var(--text); }
        .flash {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
        }
        .flash--success { background: #f0fdf4; border-color: #bbf7d0; color: var(--success); }
        .flash--error { background: #fef2f2; border-color: #fecaca; color: var(--danger); }
        label { display: block; font-weight: 500; margin-bottom: 6px; }
        input[type="text"], input[type="email"], input[type="password"], textarea {
            width: 100%;
            font: inherit;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
        }
        textarea { min-height: 120px; resize: vertical; }
        .field { margin-bottom: 16px; }
        .btn {
            display: inline-block;
            font: inherit;
            padding: 10px 16px;
            border-radius: 6px;
            border: 1px solid var(--accent);
            background: var(--accent);
            color: #fff;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { opacity: 0.9; }
        .btn--secondary {
            background: #fff;
            color: var(--text);
            border-color: var(--border);
        }
        .btn--danger {
            background: var(--danger);
            border-color: var(--danger);
        }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }
        th { font-size: 14px; color: var(--muted); font-weight: 500; }
        .meta { font-size: 14px; color: var(--muted); }
        .panel {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            background: #fff;
        }
        .panel h2 { margin: 0 0 16px; font-size: 1.1rem; }
        .collapsible {
            margin-bottom: 20px;
        }
        .collapsible summary {
            list-style: none;
            cursor: pointer;
        }
        .collapsible summary::-webkit-details-marker { display: none; }
        .collapsible__body {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
    </style>
</head>
<body>
    <div class="admin-shell">
        @hasSection('header')
            @yield('header')
        @else
            <header class="admin-header">
                <h1>Bricktick Admin</h1>
                <nav>
                    <a href="{{ route('admin.projects.index') }}">Projects</a>
                    <form action="{{ route('admin.logout') }}" method="post" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn--secondary" style="padding:6px 12px;font-size:14px">Log out</button>
                    </form>
                </nav>
            </header>
        @endif

        @if (session('success'))
            <div class="flash flash--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash--error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
