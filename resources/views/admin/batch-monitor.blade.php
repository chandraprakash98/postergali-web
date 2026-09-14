<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Status - Postergali Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f4efe6;
            --sidebar: #9b342b;
            --accent: #e58b6a;
            --panel: #fffdf8;
            --text-dark: #2f2a26;
            --muted: #79706b;
            --border: #e9e1d5;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', sans-serif;
            background: var(--bg);
            color: var(--muted);
            font-size: 14px;
        }

        .container { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: var(--sidebar);
            color: #fff;
            padding: 28px 22px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .logo { font-size: 20px; font-weight: 800; margin-bottom: 30px; }
        .logo .menu-icon { background: #fff; color: var(--sidebar); padding: 8px; border-radius: 8px; display: inline-block; }
        .menu-items { display: flex; flex-direction: column; gap: 14px; flex: 1; }
        .sidebar-footer { margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.12); }
        .menu-item {
            color: rgba(255,255,255,0.95);
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 10px;
            display: flex;
            gap: 12px;
            align-items: center;
            font-weight: 600;
            transition: background 0.15s ease;
        }
        .menu-item:hover { background: rgba(255,255,255,0.08); }
        .menu-item.active { background: rgba(255,255,255,0.16); }
        .logout-btn {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            width: 100%;
        }

        /* Main */
        .main-content { flex: 1; margin-left: 240px; }
        .topbar {
            padding: 28px 40px 18px;
            display: flex;
            align-items: center;
        }
        .page-heading h2 { font-size: 22px; color: var(--text-dark); margin: 0; font-weight: 800; }
        .page-heading p  { color: #8b8179; margin: 4px 0 0; font-size: 14px; }
        .content { padding: 0 40px 60px; }

        /* Batch Card */
        .batch-card {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 28px 32px;
            box-shadow: 0 4px 18px rgba(43,30,24,0.04);
            display: flex;
            flex-direction: column;
            gap: 0;
            max-width: 560px;
        }

        .batch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 0;
        }
        .batch-row + .batch-row {
            border-top: 1px solid var(--border);
        }

        .batch-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #8b8179;
        }

        .batch-value {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .batch-name-chip {
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            background: #fbf5ee;
            border: 1px solid #ebdcd0;
            border-radius: 8px;
            padding: 5px 12px;
            color: var(--text-dark);
        }

        /* Running / Stopped */
        .run-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 24px;
        }
        .run-badge.running {
            background: #e6f6ee;
            color: #1e7b54;
            border: 1px solid #bde6d1;
        }
        .run-badge.stopped {
            background: #fce8e6;
            color: #c93b2b;
            border: 1px solid #f6c4c0;
        }
        .pulse-dot {
            width: 8px; height: 8px;
            background: #2f8f6b;
            border-radius: 50%;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(47,143,107,0.7); }
            70%  { box-shadow: 0 0 0 7px rgba(47,143,107,0); }
            100% { box-shadow: 0 0 0 0 rgba(47,143,107,0); }
        }

        /* Status chip */
        .status-chip {
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .status-chip.active  { background: #e6f6ee; color: #1e7b54; border: 1px solid #bde6d1; }
        .status-chip.stopped { background: #fce8e6; color: #c93b2b; border: 1px solid #f6c4c0; }

        /* Notif count */
        .notif-count {
            font-size: 22px;
            font-weight: 900;
            color: #1e7b54;
        }

        /* Footer row */
        .card-footer {
            border-top: 1px solid var(--border);
            padding-top: 18px;
            margin-top: 4px;
        }
        .refresh-btn {
            background: #fff;
            border: 1px solid var(--border);
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .refresh-btn:hover { background: #f4efe6; }
    </style>
</head>
<body>
<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <span class="menu-icon">🎨</span>
            <span>POSTER<br>GALI</span>
        </div>

        <div class="menu-items">
            <a href="{{ route('admin.allAds') }}"        class="menu-item">📋 All Ads</a>
            <a href="{{ route('admin.pendingAds') }}"    class="menu-item">⏳ Pending Ads</a>
            <a href="{{ route('admin.liveAds') }}"       class="menu-item">✓ Live Ads</a>
            <a href="{{ route('admin.expiredAds') }}"    class="menu-item">✕ Expired Ads</a>
            <a href="{{ route('admin.pricingInfo') }}"   class="menu-item">💰 Pricing Info</a>
            <a href="{{ route('admin.referrals') }}"     class="menu-item">🤝 Referrals</a>
            <a href="{{ route('admin.batch.monitor') }}" class="menu-item active">🔔 Batch Monitor</a>
        </div>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content">
        <div class="topbar">
            <div class="page-heading">
                <h2>🔔 Batch Status</h2>
                <p>Automated notification batch overview</p>
            </div>
        </div>

        <div class="content">
            <div class="batch-card">

                {{-- 1. Batch Name --}}
                <div class="batch-row">
                    <span class="batch-label">Batch Name</span>
                    <span class="batch-name-chip">{{ $batchStatus['batch_name'] ?? 'postergali-alpha' }}</span>
                </div>

                {{-- 2. Running / Stopped --}}
                <div class="batch-row">
                    <span class="batch-label">Engine</span>
                    @if($batchStatus['enabled'] ?? true)
                        <span class="run-badge running">
                            <span class="pulse-dot"></span> Running
                        </span>
                    @else
                        <span class="run-badge stopped">⏹ Stopped</span>
                    @endif
                </div>

                {{-- 3. Scheduled Time --}}
                <div class="batch-row">
                    <span class="batch-label">Scheduled Time</span>
                    <span class="batch-value">{{ $batchStatus['schedule_human'] ?? 'Everyday at 6:00 AM IST' }}</span>
                </div>

                {{-- 4. Status --}}
                <div class="batch-row">
                    <span class="batch-label">Status</span>
                    @php $isActive = $batchStatus['enabled'] ?? true; @endphp
                    <span class="status-chip {{ $isActive ? 'active' : 'stopped' }}">
                        {{ $batchStatus['status'] ?? ($isActive ? 'Active' : 'Disabled') }}
                    </span>
                </div>

                {{-- 5. Total Notifications --}}
                <div class="batch-row">
                    <span class="batch-label">Total Notifications Sent</span>
                    <span class="notif-count">📲 {{ $batchStatus['total_sent'] ?? 0 }}</span>
                </div>

                <div class="card-footer">
                    <button class="refresh-btn" onclick="location.reload();">↻ Refresh</button>
                </div>

            </div>
        </div>
    </div>

</div>
</body>
</html>
