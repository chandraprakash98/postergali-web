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
            --text-dark: #2f2a26;
            --muted: #79706b;
            --success: #2f8f6b;
            --danger: #d9534f;
            --warning: #b77400;
            --border: #e9e1d5;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', sans-serif;
            background: var(--bg);
            color: var(--muted);
            font-size: 14px;
        }

        .container { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
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

        /* ── Main Content ── */
        .main-content { flex: 1; margin-left: 240px; }
        .topbar {
            padding: 28px 40px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-heading h2 { font-size: 22px; color: var(--text-dark); margin: 0; font-weight: 800; }
        .page-heading p { color: #8b8179; margin: 4px 0 0; font-size: 14px; }
        .content { padding: 0 40px 60px; }

        /* ── Top Info Strip ── */
        .info-strip {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #79706b;
            font-weight: 600;
        }
        .info-strip strong { color: var(--text-dark); }

        /* ── Alpha Batch Card ── */
        .alpha-card {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 24px 28px;
            box-shadow: 0 4px 18px rgba(43,30,24,0.04);
            margin-bottom: 28px;
        }

        .alpha-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .alpha-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alpha-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-dark);
            font-family: monospace;
            background: #fbf5ee;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #ebdcd0;
        }

        .alpha-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .alpha-badge.active {
            background: #e6f6ee;
            color: #1e7b54;
            border: 1px solid #bde6d1;
        }
        .alpha-badge.disabled {
            background: #fce8e6;
            color: #c93b2b;
            border: 1px solid #f6c4c0;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #2f8f6b;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(47,143,107,0.7);
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(47,143,107,0.7); }
            70% { box-shadow: 0 0 0 7px rgba(47,143,107,0); }
            100% { box-shadow: 0 0 0 0 rgba(47,143,107,0); }
        }

        .timer-badge {
            background: #fdf3ed;
            border: 1px solid #f5cfb8;
            border-radius: 24px;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
        }
        .timer-time {
            color: #b45309;
        }
        .timer-countdown {
            color: #b91c1c;
            background: rgba(185,28,28,0.08);
            padding: 2px 8px;
            border-radius: 12px;
        }

        /* ── Metrics Grid ── */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .metric-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
        }
        .metric-box-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #8b8179;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .metric-box-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
        }
        .metric-box-sub {
            font-size: 12px;
            color: #8b8179;
            margin-top: 4px;
        }

        /* ── Functions Overview Box ── */
        .functions-box {
            background: #faf7f2;
            border: 1px solid #ebdcd0;
            border-radius: 12px;
            padding: 16px 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .func-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .func-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .func-desc {
            font-size: 12px;
            color: #79706b;
            line-height: 1.4;
        }

        /* ── History Table ── */
        .table-section {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 4px 14px rgba(43,30,24,0.03);
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .table-header h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-dark);
        }
        .refresh-btn {
            background: #fff;
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .refresh-btn:hover {
            background: #f4efe6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th {
            text-align: left;
            padding: 10px 12px;
            background: #faf7f2;
            color: #79706b;
            font-weight: 700;
            border-bottom: 1.5px solid var(--border);
        }
        td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
        }
        tr:hover td {
            background: #fbf9f5;
        }

        .badge-status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }
        .badge-status.success {
            background: #e6f6ee;
            color: #1e7b54;
        }
        .badge-status.error {
            background: #fce8e6;
            color: #c93b2b;
        }

        @media (max-width: 992px) {
            .metrics-grid { grid-template-columns: 1fr 1fr; }
            .functions-box { grid-template-columns: 1fr; }
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="page-heading">
                <h2>🔔 Batch Status</h2>
                <p>Automated notification batch (postergali-alpha) running via Laravel Scheduler</p>
            </div>
        </div>

        <div class="content">

            <!-- Info Strip -->
            <div class="info-strip">
                <div>
                    ⚡ <strong>Batch Engine:</strong> postergali-alpha
                    &nbsp;·&nbsp;
                    ⏰ <strong>Schedule:</strong> {{ $batchStatus['schedule_human'] ?? 'Everyday at 6:00 AM IST' }}
                </div>
                <div>
                    🌐 <strong>Timezone:</strong> {{ $batchStatus['timezone'] ?? 'Asia/Kolkata' }}
                </div>
            </div>

            <!-- Single Alpha Batch Overview Card -->
            <div class="alpha-card">
                <div class="alpha-header">
                    <div class="alpha-title-group">
                        <span class="alpha-title">{{ $batchStatus['batch_name'] ?? 'postergali-alpha' }}</span>
                        <span class="alpha-badge {{ ($batchStatus['enabled'] ?? true) ? 'active' : 'disabled' }}">
                            @if($batchStatus['enabled'] ?? true)
                                <span class="pulse-dot"></span>
                            @endif
                            {{ $batchStatus['status'] ?? 'Active' }}
                        </span>
                    </div>

                    <div class="timer-badge">
                        <span>⏰ Next Run:</span>
                        <span class="timer-time">{{ $batchStatus['upcoming_time_only'] ?? $batchStatus['upcoming_formatted'] }}</span>
                        <span class="timer-countdown" id="alpha-countdown">⏳ {{ $batchStatus['upcoming_human'] }}</span>
                    </div>
                </div>

                <!-- 4 Metrics Grid -->
                <div class="metrics-grid">
                    <div class="metric-box">
                        <div class="metric-box-title">Schedule Time</div>
                        <div class="metric-box-value">{{ $batchStatus['schedule_human'] }}</div>
                        <div class="metric-box-sub">Customizable (cron: {{ $batchStatus['schedule'] }})</div>
                    </div>

                    <div class="metric-box">
                        <div class="metric-box-title">Last Executed</div>
                        <div class="metric-box-value" style="font-size: 15px;">{{ $batchStatus['last_run_formatted'] }}</div>
                        <div class="metric-box-sub">
                            @if($batchStatus['last_run_human'])
                                {{ $batchStatus['last_run_human'] }} ({{ $batchStatus['last_run_duration'] }}ms)
                            @else
                                No previous run
                            @endif
                        </div>
                    </div>

                    <div class="metric-box">
                        <div class="metric-box-title">Total Notifications Sent</div>
                        <div class="metric-box-value" style="color: #1e7b54;">📲 {{ $batchStatus['total_sent'] }}</div>
                        <div class="metric-box-sub">Across {{ $batchStatus['total_runs'] }} successful batch runs</div>
                    </div>

                    <div class="metric-box">
                        <div class="metric-box-title">Last Run Dispatched</div>
                        <div class="metric-box-value">{{ $batchStatus['last_run_sent'] }} sent</div>
                        <div class="metric-box-sub">
                            Day-1: {{ $batchStatus['last_run_day_before'] }} &bull; Expired: {{ $batchStatus['last_run_on_expiry'] }} &bull; Skipped: {{ $batchStatus['last_run_skipped'] }}
                        </div>
                    </div>
                </div>

                <!-- Functions Details -->
                <div class="functions-box">
                    <div class="func-item">
                        <div class="func-title">⏰ Function 1 (f1): 1 Day Before Expiry Reminder</div>
                        <div class="func-desc">
                            Finds approved posters expiring in the next 24 hours and dispatches reminder push notification ("Poster Expires Tomorrow") to renew or create a new poster.
                        </div>
                    </div>
                    <div class="func-item">
                        <div class="func-title">⚠️ Function 2 (f2): Expiring Today & Expired Alert</div>
                        <div class="func-desc">
                            Finds approved posters that reached expiry today, sends expiration notification ("Poster Expired"), and transitions poster status to expired.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Runs History Table -->
            <div class="table-section">
                <div class="table-header">
                    <h3>Recent Execution History (postergali-alpha)</h3>
                    <button class="refresh-btn" onclick="location.reload();">↻ Refresh</button>
                </div>

                @if(!empty($batchStatus['recent_runs']) && count($batchStatus['recent_runs']) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Execution Time (IST)</th>
                                <th>Duration</th>
                                <th>Day 1 Before (Jobs/Offers)</th>
                                <th>Expiring Today (Jobs/Offers)</th>
                                <th>Sent</th>
                                <th>Skipped</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batchStatus['recent_runs'] as $run)
                                <tr>
                                    <td>
                                        <strong>{{ $run['ran_at_formatted'] }}</strong>
                                        <div style="font-size: 11px; color: #8b8179;">{{ $run['ran_at_human'] }}</div>
                                    </td>
                                    <td>{{ $run['duration_ms'] }}ms</td>
                                    <td>{{ $run['day_before_jobs'] }} / {{ $run['day_before_offers'] }}</td>
                                    <td>{{ $run['on_expiry_jobs'] }} / {{ $run['on_expiry_offers'] }}</td>
                                    <td><strong style="color: #1e7b54;">{{ $run['notifications_sent'] }}</strong></td>
                                    <td style="color: #8b8179;">{{ $run['skipped_no_token'] }}</td>
                                    <td>
                                        <span class="badge-status {{ strtolower($run['status']) === 'success' ? 'success' : 'error' }}">
                                            {{ $run['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: #8b8179; font-style: italic; padding: 12px 0;">
                        No execution records found yet for postergali-alpha. The batch will record logs upon its next scheduled run or manual execution via <code>php artisan postergali-alpha</code>.
                    </p>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(!empty($batchStatus['upcoming_iso']))
            (function() {
                const targetTime = new Date("{{ $batchStatus['upcoming_iso'] }}").getTime();
                const el = document.getElementById("alpha-countdown");
                if (!el) return;

                function updateCountdown() {
                    const now = new Date().getTime();
                    const diff = targetTime - now;

                    if (diff <= 0) {
                        el.innerHTML = "⚡ Due now";
                        return;
                    }

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const secs = Math.floor((diff % (1000 * 60)) / 1000);

                    if (hours > 0) {
                        el.innerHTML = "⏳ in " + hours + "h " + mins + "m";
                    } else {
                        el.innerHTML = "⏳ in " + (mins > 0 ? mins + "m " : "") + secs + "s";
                    }
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            })();
        @endif
    });
</script>
</body>
</html>
