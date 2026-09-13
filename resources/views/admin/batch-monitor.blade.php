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
            --info-bg: #eef6fc;
            --info-text: #1d6fa5;
            --info-border: #cce4f7;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--muted);
            font-size: 13px;
        }

        .container { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 220px; background: var(--sidebar); color: #fff; padding: 22px 16px; position: fixed; height: 100vh; overflow-y: auto; display: flex; flex-direction: column; }
        .logo { font-size: 18px; font-weight: 800; margin-bottom: 22px; }
        .logo .menu-icon { background: #fff; color: var(--sidebar); padding: 6px; border-radius: 6px; display: inline-block; font-size: 14px; }
        .menu-items { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .sidebar-footer { margin-top: auto; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.12); }
        .menu-item { color: rgba(255,255,255,0.92); text-decoration: none; padding: 8px 12px; border-radius: 8px; display: flex; gap: 10px; align-items: center; font-weight: 600; font-size: 12.5px; }
        .menu-item:hover { background: rgba(255,255,255,0.06); }
        .menu-item.active { background: rgba(255,255,255,0.14); }
        .logout-btn { background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 14px; cursor: pointer; font-weight: 700; font-size: 12px; }

        /* ── Main ── */
        .main-content { flex: 1; margin-left: 220px; }
        .topbar { padding: 18px 32px 12px; display: flex; justify-content: space-between; align-items: center; }
        .page-heading h2 { font-size: 19px; color: #302b27; margin: 0; font-weight: 800; }
        .page-heading p { color: #8b8179; margin: 2px 0 0; font-size: 12px; }
        .content { padding: 0 32px 40px; }

        /* ── Compact Schedule Info Strip ── */
        .schedule-info {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            color: #79706b;
            font-weight: 600;
        }
        .schedule-info strong { color: #2f2a26; }

        /* ── Compact Batch Grid ── */
        .batch-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ── Super Compact Batch Card ── */
        .batch-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 2px 8px rgba(43,30,24,0.03);
            display: flex;
            flex-direction: column;
        }

        .batch-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f2ece3;
        }

        .batch-title {
            font-size: 14px;
            font-weight: 800;
            color: #2f2a26;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .batch-desc {
            font-size: 11px;
            color: #9b9188;
            margin-top: 1px;
            font-weight: 500;
        }

        /* ── Header Right: Timer Left to Active Button ── */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        /* ── Clean, Small Timer Div ── */
        .timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            color: var(--info-text);
            font-weight: 600;
        }
        .timer-pill .timer-label {
            color: #6298be;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .timer-pill .timer-time {
            color: #114c72;
            font-weight: 800;
            font-size: 11.5px;
        }
        .timer-pill .timer-countdown {
            background: #fff;
            color: var(--info-text);
            border: 1px solid #bcdcf5;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .status-badge.active {
            background: #eaf7ef;
            color: #1a6147;
            border: 1px solid #b7dfcd;
        }
        .status-badge.disabled {
            background: #f7eaea;
            color: #a32a2a;
            border: 1px solid #e5b4b4;
        }
        .pulse-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 2px rgba(47,143,107,0.3); }
            50% { box-shadow: 0 0 0 4px rgba(47,143,107,0.08); }
        }

        /* ── Compact Task Tags ── */
        .task-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 8px;
        }
        .task-tag {
            font-size: 10px;
            font-weight: 600;
            color: #635b54;
            background: #f8f4ec;
            border: 1px solid #ede5d8;
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* ── Compact Stat Rows ── */
        .stat-rows {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: auto;
            border-top: 1px dashed #ede5d8;
            padding-top: 6px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
        }

        .stat-row-label {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #9b9188;
            font-size: 9.5px;
        }
        .stat-row-value {
            font-weight: 700;
            color: #2f2a26;
            font-size: 11.5px;
            text-align: right;
        }
        .stat-row-value.green { color: var(--success); }
        .stat-row-value.muted { color: #a09890; font-weight: 500; }

        @media (max-width: 860px) {
            .batch-grid { grid-template-columns: 1fr; }
            .main-content { margin-left: 0; }
            .sidebar { display: none; }
            .content { padding: 0 16px 40px; }
            .topbar { padding: 14px 16px; }
            .header-actions { flex-direction: column; align-items: flex-end; gap: 4px; }
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
                <h2>🔔 Batch Monitor (B1 & B2)</h2>
                <p>Automated notification batches running via Laravel Scheduler</p>
            </div>
        </div>

        <div class="content">

            {{-- Compact Summary Bar --}}
            <div class="schedule-info">
                <div>
                    ⚡ <strong>B1:</strong> Every 2 min (Expired, Expiring, Milestones)
                    &nbsp;·&nbsp;
                    🌙 <strong>B2:</strong> Evening 7:00 PM IST (Milestones)
                </div>
                <div>
                    🌐 <strong>Timezone:</strong> {{ $batchStatus['timezone'] ?? 'Asia/Kolkata' }}
                </div>
            </div>

            {{-- Compact Batch Grid --}}
            <div class="batch-grid">
                @foreach($batchStatus['batches'] as $batch)
                <div class="batch-card" id="card-{{ strtolower($batch['name']) }}">

                    <!-- Card Header: Title on Left, Timer + Active Button on Right -->
                    <div class="batch-card-header">
                        <div>
                            <div class="batch-title">
                                {{ $batch['label'] }}
                                <code style="font-size:10px; color:#888; font-weight:normal; background:#f0eae0; padding:1px 5px; border-radius:4px;">{{ $batch['schedule'] }}</code>
                            </div>
                            <div class="batch-desc">{{ $batch['description'] }}</div>
                        </div>

                        <!-- Right: Small Clean Timer Div directly to the left of Active Button -->
                        <div class="header-actions">
                            <div class="timer-pill">
                                <span>⏰</span>
                                <span class="timer-label">Next:</span>
                                <span class="timer-time">{{ $batch['upcomingTimeOnly'] ?? $batch['upcomingFormatted'] }}</span>
                                <span class="timer-countdown" id="countdown-{{ strtolower($batch['name']) }}">⏳ {{ $batch['upcomingHuman'] }}</span>
                            </div>

                            <span class="status-badge {{ $batch['enabled'] ? 'active' : 'disabled' }}">
                                @if($batch['enabled'])
                                    <span class="pulse-dot"></span>
                                @endif
                                {{ $batch['status'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Tasks List (Compact Tags) -->
                    <div class="task-tags">
                        @foreach($batch['tasks'] as $task)
                            <span class="task-tag">{{ $task }}</span>
                        @endforeach
                    </div>

                    <!-- Compact Stat Rows -->
                    <div class="stat-rows">
                        <div class="stat-row">
                            <span class="stat-row-label">Schedule</span>
                            <span class="stat-row-value">⏱️ {{ $batch['scheduleHuman'] }}</span>
                        </div>

                        <div class="stat-row">
                            <span class="stat-row-label">Last Run</span>
                            <span class="stat-row-value {{ $batch['lastRunStatus'] ? '' : 'muted' }}">
                                {{ $batch['lastRunFormatted'] }}
                                @if($batch['lastRunHuman'])
                                    <span style="font-size:10px; color:#9b9188; font-weight:normal;">({{ $batch['lastRunHuman'] }})</span>
                                @endif
                            </span>
                        </div>

                        <div class="stat-row">
                            <span class="stat-row-label">Notifications Sent</span>
                            <span class="stat-row-value {{ $batch['totalSent'] > 0 ? 'green' : 'muted' }}">
                                📲 {{ $batch['totalSent'] }} sent
                                <span style="font-size:10px; color:#888; font-weight:normal;">({{ $batch['totalRuns'] }} runs)</span>
                            </span>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

<script>
    // Live countdown update for upcoming runs
    document.addEventListener('DOMContentLoaded', function () {
        @foreach($batchStatus['batches'] as $batch)
            @if(!empty($batch['upcomingIso']))
                (function() {
                    const targetTime = new Date("{{ $batch['upcomingIso'] }}").getTime();
                    const el = document.getElementById("countdown-{{ strtolower($batch['name']) }}");
                    if (!el) return;

                    function updateCountdown() {
                        const now = new Date().getTime();
                        const diff = targetTime - now;

                        if (diff <= 0) {
                            el.innerHTML = "⚡ Due now";
                            return;
                        }

                        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const secs = Math.floor((diff % (1000 * 60)) / 1000);
                        const hours = Math.floor(diff / (1000 * 60 * 60));

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
        @endforeach
    });
</script>
</body>
</html>
