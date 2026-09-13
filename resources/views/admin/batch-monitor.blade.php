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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', sans-serif;
            background: var(--bg);
            color: var(--muted);
            font-size: 14px;
        }

        .container { display: flex; min-height: 100vh; }

        /* ── Sidebar (matches Dashboard exactly) ── */
        .sidebar { width: 240px; background: var(--sidebar); color: #fff; padding: 28px 22px; position: fixed; height: 100vh; overflow-y: auto; display: flex; flex-direction: column; }
        .logo { font-size: 20px; font-weight: 800; margin-bottom: 30px; }
        .logo .menu-icon { background: #fff; color: var(--sidebar); padding: 8px; border-radius: 8px; display: inline-block; }
        .menu-items { display: flex; flex-direction: column; gap: 14px; flex: 1; }
        .sidebar-footer { margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.12); }
        .menu-item { color: rgba(255,255,255,0.95); text-decoration: none; padding: 12px 14px; border-radius: 10px; display: flex; gap: 12px; align-items: center; font-weight: 600; }
        .menu-item:hover { background: rgba(255,255,255,0.06); }
        .menu-item.active { background: rgba(255,255,255,0.14); }
        .logout-btn { background: var(--accent); color: #fff; border: none; padding: 8px 14px; border-radius: 20px; cursor: pointer; font-weight: 700; font-size: 13px; }

        /* ── Main Content & Topbar ── */
        .main-content { flex: 1; margin-left: 240px; }
        .topbar { padding: 28px 40px 18px; display: flex; justify-content: space-between; align-items: center; }
        .page-heading h2 { font-size: 22px; color: #302b27; margin: 0; font-weight: 800; }
        .page-heading p { color: #8b8179; margin: 4px 0 0; font-size: 14px; }
        .content { padding: 0 40px 60px; }

        /* ── Schedule Info Strip ── */
        .schedule-info {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #79706b;
            font-weight: 600;
        }
        .schedule-info strong { color: #2f2a26; }

        /* ── Batch Grid ── */
        .batch-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ── Compact & Clean Batch Card ── */
        .batch-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 22px;
            box-shadow: 0 4px 14px rgba(43,30,24,0.04);
            display: flex;
            flex-direction: column;
        }

        .batch-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f2ece3;
        }

        .batch-title {
            font-size: 16px;
            font-weight: 800;
            color: #2f2a26;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .batch-desc {
            font-size: 12px;
            color: #9b9188;
            margin-top: 2px;
            font-weight: 500;
        }

        /* ── Header Actions (Timer pill directly left to Active button) ── */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--info-text);
            font-weight: 600;
        }
        .timer-pill .timer-label {
            color: #6298be;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .timer-pill .timer-time {
            color: #114c72;
            font-weight: 800;
            font-size: 13px;
        }
        .timer-pill .timer-countdown {
            background: #fff;
            color: var(--info-text);
            border: 1px solid #bcdcf5;
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
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
        .status-badge.disabled {
            background: #f7eaea;
            color: #a32a2a;
            border: 1px solid #e5b4b4;
        }
        .pulse-dot {
            width: 7px; height: 7px; border-radius: 50%;
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
            gap: 6px;
            margin-bottom: 12px;
        }
        .task-tag {
            font-size: 11.5px;
            font-weight: 600;
            color: #635b54;
            background: #f8f4ec;
            border: 1px solid #ede5d8;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* ── Compact Stat Rows ── */
        .stat-rows {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: auto;
            border-top: 1px dashed #ede5d8;
            padding-top: 8px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
        }

        .stat-row-label {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9b9188;
            font-size: 11px;
        }
        .stat-row-value {
            font-weight: 700;
            color: #2f2a26;
            font-size: 13px;
            text-align: right;
        }
        .stat-row-value.green { color: var(--success); }
        .stat-row-value.muted { color: #a09890; font-weight: 500; }

        @media (max-width: 860px) {
            .batch-grid { grid-template-columns: 1fr; }
            .main-content { margin-left: 0; }
            .sidebar { display: none; }
            .content { padding: 0 16px 40px; }
            .topbar { padding: 18px 16px; }
            .header-actions { flex-direction: column; align-items: flex-end; gap: 6px; }
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
                <h2>🔔 Batch Monitor (B1 & B2)</h2>
                <p>Automated notification batches running via Laravel Scheduler</p>
            </div>
        </div>

        <div class="content">

            {{-- Summary Bar --}}
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

            {{-- Batch Cards Grid --}}
            <div class="batch-grid">
                @foreach($batchStatus['batches'] as $batch)
                <div class="batch-card" id="card-{{ strtolower($batch['name']) }}">

                    <!-- Card Header -->
                    <div class="batch-card-header">
                        <div>
                            <div class="batch-title">
                                {{ $batch['label'] }}
                                <code style="font-size:11px; color:#888; font-weight:normal; background:#f0eae0; padding:2px 6px; border-radius:4px;">{{ $batch['schedule'] }}</code>
                            </div>
                            <div class="batch-desc">{{ $batch['description'] }}</div>
                        </div>

                        <!-- Right: Next Run Timer directly left to Active button -->
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

                    <!-- Tasks List Tags -->
                    <div class="task-tags">
                        @foreach($batch['tasks'] as $task)
                            <span class="task-tag">{{ $task }}</span>
                        @endforeach
                    </div>

                    <!-- Stat Rows -->
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
                                    <span style="font-size:11px; color:#9b9188; font-weight:normal;">({{ $batch['lastRunHuman'] }})</span>
                                @endif
                            </span>
                        </div>

                        <div class="stat-row">
                            <span class="stat-row-label">Notifications Sent</span>
                            <span class="stat-row-value {{ $batch['totalSent'] > 0 ? 'green' : 'muted' }}">
                                📲 {{ $batch['totalSent'] }} sent
                                <span style="font-size:11px; color:#888; font-weight:normal;">({{ $batch['totalRuns'] }} runs)</span>
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
