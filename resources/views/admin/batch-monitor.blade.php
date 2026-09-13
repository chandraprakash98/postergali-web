<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Monitor - Postergali Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f4efe6;
            --sidebar: #9b342b;
            --accent: #e58b6a;
            --panel: #fffdf8;
            --muted: #79706b;
            --success: #2f8f6b;
            --danger: #d9534f;
            --warning: #b77400;
            --border: #e9e1d5;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--muted);
        }

        .container { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 240px; background: var(--sidebar); color: #fff; padding: 28px 22px; position: fixed; height: 100vh; overflow-y: auto; display: flex; flex-direction: column; }
        .logo { font-size: 20px; font-weight: 800; margin-bottom: 30px; }
        .logo .menu-icon { background: #fff; color: var(--sidebar); padding: 8px; border-radius: 8px; display: inline-block; }
        .menu-items { display: flex; flex-direction: column; gap: 14px; flex: 1; }
        .sidebar-footer { margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.12); }
        .menu-item { color: rgba(255,255,255,0.95); text-decoration: none; padding: 12px 14px; border-radius: 10px; display: flex; gap: 12px; align-items: center; font-weight: 600; }
        .menu-item:hover { background: rgba(255,255,255,0.06); }
        .menu-item.active { background: rgba(255,255,255,0.12); }
        .logout-btn { background: var(--accent); color: #fff; border: none; padding: 8px 14px; border-radius: 20px; cursor: pointer; font-weight: 700; }

        /* ── Main ── */
        .main-content { flex: 1; margin-left: 240px; }
        .topbar { padding: 28px 40px 18px; display: flex; justify-content: space-between; align-items: center; }
        .page-heading h2 { font-size: 22px; color: #302b27; margin: 0; font-weight: 800; }
        .page-heading p { color: #8b8179; margin: 4px 0 0; font-size: 14px; }
        .content { padding: 0 40px 80px; }

        /* ── Schedule Info ── */
        .schedule-info {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #79706b;
            font-weight: 600;
        }
        .schedule-info strong { color: #2f2a26; }

        /* ── Batch Cards ── */
        .batch-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .batch-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px 26px;
            box-shadow: 0 4px 16px rgba(43,30,24,0.04);
        }

        .batch-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .batch-label {
            font-size: 17px;
            font-weight: 800;
            color: #2f2a26;
        }
        .batch-desc {
            font-size: 12px;
            color: #9b9188;
            margin-top: 4px;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .status-badge.active {
            background: #eaf7ef;
            color: #1a6147;
            border: 1px solid #b7dfcd;
        }
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 2px rgba(47,143,107,0.3); }
            50% { box-shadow: 0 0 0 5px rgba(47,143,107,0.08); }
        }

        /* ── Stat Rows ── */
        .stat-rows { display: flex; flex-direction: column; gap: 12px; }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1eae0;
        }
        .stat-row:last-child { border-bottom: none; }

        .stat-row-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #9b9188;
        }
        .stat-row-value {
            font-size: 14px;
            font-weight: 800;
            color: #2f2a26;
        }
        .stat-row-value.green { color: var(--success); }
        .stat-row-value.orange { color: var(--warning); }
        .stat-row-value.muted { color: #a09890; font-weight: 600; }

        @media (max-width: 768px) {
            .batch-grid { grid-template-columns: 1fr; }
            .main-content { margin-left: 0; }
            .sidebar { display: none; }
            .content { padding: 0 16px 60px; }
            .topbar { padding: 20px 16px; }
        }
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
                <h2>🔔 Batch Monitor</h2>
                <p>Scheduled FCM notification batches running daily at 6:00 AM IST</p>
            </div>
        </div>

        <div class="content">

            {{-- Schedule info strip --}}
            <div class="schedule-info">
                🕐 <strong>Schedule:</strong> {{ $batchStatus['schedule'] }}
                &nbsp;·&nbsp;
                🚀 <strong>2 active batches</strong> running automatically via Laravel Scheduler
            </div>

            {{-- Batch Cards --}}
            <div class="batch-grid">
                @foreach($batchStatus['batches'] as $batch)
                <div class="batch-card">
                    <div class="batch-card-header">
                        <div>
                            <div class="batch-label">{{ $batch['label'] }}</div>
                            <div class="batch-desc">{{ $batch['description'] }}</div>
                        </div>
                        <span class="status-badge active">
                            <span class="pulse-dot"></span>
                            Active
                        </span>
                    </div>

                    <div class="stat-rows">
                        <div class="stat-row">
                            <span class="stat-row-label">Total Runs</span>
                            <span class="stat-row-value">{{ $batch['totalRuns'] }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-row-label">Total Notifications Sent</span>
                            <span class="stat-row-value {{ $batch['totalSent'] > 0 ? 'green' : 'muted' }}">
                                📲 {{ $batch['totalSent'] }}
                            </span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-row-label">Last Run</span>
                            <span class="stat-row-value {{ $batch['lastRunStatus'] ? '' : 'muted' }}">
                                {{ $batch['lastRunFormatted'] }}
                                @if($batch['lastRunHuman'])
                                    <span style="font-size:11px; color:#9b9188; font-weight:600;">({{ $batch['lastRunHuman'] }})</span>
                                @endif
                            </span>
                        </div>
                        @if($batch['lastRunStatus'])
                        <div class="stat-row">
                            <span class="stat-row-label">Last Run — Sent</span>
                            <span class="stat-row-value {{ $batch['lastRunSent'] > 0 ? 'green' : 'muted' }}">
                                {{ $batch['lastRunSent'] }} sent
                                @if($batch['lastRunSkipped'] > 0)
                                    <span class="orange" style="font-size:12px; margin-left:6px;">⚠️ {{ $batch['lastRunSkipped'] }} skipped</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
</body>
</html>
