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
            --info: #2563eb;
            --border: #e9e1d5;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', sans-serif;
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
        .topbar { padding: 28px 40px 18px; display: flex; justify-content: space-between; align-items: center; gap: 24px; }
        .page-heading h2 { font-size: 22px; color: #302b27; margin: 0; font-weight: 800; }
        .page-heading p { color: #8b8179; margin: 4px 0 0; font-size: 14px; }
        .content { padding: 0 40px 80px; }

        /* ── Status Bar (Enhanced) ── */
        .status-bar-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(43,30,24,0.04);
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            justify-content: space-between;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }
        .status-pill.active {
            background: #eaf7ef;
            color: #1a6147;
            border: 1px solid #b7dfcd;
        }
        .status-pill.disabled {
            background: #fdecea;
            color: #8b1a1a;
            border: 1px solid #f5c6c4;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 3px rgba(47,143,107,0.25);
            animation: pulse-ring 2s infinite;
        }
        .pulse-dot.off {
            background: var(--danger);
            box-shadow: none;
            animation: none;
        }
        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 3px rgba(47,143,107,0.3); transform: scale(1); }
            50% { box-shadow: 0 0 0 6px rgba(47,143,107,0.1); transform: scale(1.1); }
        }

        .status-info-group {
            display: flex;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
        }

        .info-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #8b8179;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .info-val-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-time {
            font-size: 16px;
            font-weight: 800;
            color: #2f2a26;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .info-sub {
            font-size: 12px;
            color: #8b8179;
            font-weight: 500;
        }

        .countdown-pill {
            background: #2f2a26;
            color: #fff;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .countdown-pill.running {
            background: #e58b6a;
            animation: pulse-ring 1s infinite;
        }

        .clock-badge {
            background: #ede9fe;
            color: #6b21a8;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .refresh-btn {
            background: #fff;
            border: 1px solid var(--border);
            color: #4f463f;
            padding: 7px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .refresh-btn:hover {
            background: var(--bg);
            border-color: #d8cebe;
        }

        /* ── Simple Stats Grid ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--panel);
            border: 1px solid var(--border);
            padding: 18px 20px;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(43,30,24,0.03);
        }
        .stat-info h3 { color: #8b8179; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; font-weight: 700; }
        .stat-number { font-size: 26px; color: #2f2a26; font-weight: 800; }
        .stat-sub { font-size: 12px; color: #a09890; margin-top: 2px; }
        .stat-icon { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .stat-icon.runs    { background: #ede9fe; color: #7c3aed; }
        .stat-icon.notifs  { background: #eaf7ef; color: var(--success); }
        .stat-icon.skipped { background: #fff7e6; color: var(--warning); }
        .stat-icon.speed   { background: #e0f2fe; color: var(--info); }

        /* ── Simple Clean Table ── */
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .section-header h3 { font-size: 16px; color: #2f2a26; font-weight: 800; }
        .section-header .badge { background: var(--accent); color: #fff; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }

        .table-wrap {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(43,30,24,0.03);
        }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #faf6ef;
            color: #8b8179;
            text-align: left;
            padding: 12px 18px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid var(--border);
        }
        tbody tr {
            border-bottom: 1px solid #f1eae0;
            transition: background 0.15s ease;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #faf7f2; }
        td { padding: 14px 18px; vertical-align: middle; color: #4f463f; font-size: 13px; }

        .pill { padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px; display: inline-block; }
        .pill-success  { background: #eaf7ef; color: var(--success); }
        .pill-disabled { background: #f3f4f6; color: #6b7280; }
        .pill-dry      { background: #fff7e6; color: var(--warning); }

        .tag-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 4px;
            margin-bottom: 2px;
        }
        .tag-expiring { background: #fff7e6; color: #b77400; border: 1px solid #fde7be; }
        .tag-expired  { background: #fdecea; color: #8b1a1a; border: 1px solid #f9c7c4; }
        .tag-none     { color: #aaa; font-size: 12px; font-weight: 600; }

        .badge-sent { font-weight: 700; color: var(--success); }
        .badge-skip { font-weight: 700; color: var(--warning); font-size: 12px; margin-left: 6px; }
        .num-muted { font-weight: 700; color: #aaa; }

        .empty-state { padding: 60px 20px; text-align: center; color: #8b8179; }
        .empty-state .icon { font-size: 40px; display: block; margin-bottom: 12px; }

        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .status-info-group { gap: 16px; }
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
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
            <a href="{{ route('admin.allAds') }}"      class="menu-item">📋 All Ads</a>
            <a href="{{ route('admin.pendingAds') }}"  class="menu-item">⏳ Pending Ads</a>
            <a href="{{ route('admin.liveAds') }}"     class="menu-item">✓ Live Ads</a>
            <a href="{{ route('admin.expiredAds') }}"  class="menu-item">✕ Expired Ads</a>
            <a href="{{ route('admin.pricingInfo') }}" class="menu-item">💰 Pricing Info</a>
            <a href="{{ route('admin.referrals') }}"   class="menu-item">🤝 Referrals</a>
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
                <h2>🔔 Batch Notification Monitor</h2>
                <p>Real-time scheduler monitoring with Indian Standard Time (IST)</p>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <span class="clock-badge">
                    ⏰ IST: <strong class="live-ist-clock">{{ $currentIstTime }}</strong>
                </span>
                <button onclick="window.location.reload();" class="refresh-btn">
                    🔄 Refresh
                </button>
            </div>
        </div>

        <div class="content">

            {{-- ── Status Bar (Simplified & Intuitive) ── --}}
            <div class="status-bar-card">
                <div>
                    <span class="status-pill {{ $enabled ? 'active' : 'disabled' }}">
                        <span class="pulse-dot {{ $enabled ? '' : 'off' }}"></span>
                        <span>{{ $enabled ? 'BATCH ACTIVE' : 'BATCH DISABLED' }}</span>
                    </span>
                    <div style="font-size:12px; color:#8b8179; margin-top:6px; font-weight:600;">
                        Schedule: <code>{{ $scheduleHuman ?? $schedule }} ({{ $schedule }})</code> · Window: {{ $windowHours }}h
                    </div>
                </div>

                <div class="status-info-group">
                    {{-- Last Run --}}
                    <div class="info-block">
                        <span class="info-label">🕒 Last Batch Run (IST)</span>
                        <div class="info-val-row">
                            <span class="info-time live-last-run">{{ $lastRunTimeOnly }}</span>
                            <span class="info-sub live-last-human">({{ $lastRunHuman }} · {{ $lastRunSent }} sent)</span>
                        </div>
                    </div>

                    <div style="width:1px; height:34px; background:var(--border);"></div>

                    {{-- Next Batch --}}
                    <div class="info-block">
                        <span class="info-label">⏳ Next Batch Coming (IST)</span>
                        <div class="info-val-row">
                            <span class="info-time live-next-run">{{ $nextRunFormatted }}</span>
                            <span class="countdown-pill live-batch-countdown">--:--</span>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ── Simplified Run History Table ── --}}
            <div class="section-header">
                <h3>Batch Run History</h3>
                <span class="badge">Recent 50 Runs</span>
            </div>

            @if($recentRuns->isEmpty())
                <div class="empty-state">
                    <span class="icon">📭</span>
                    <p style="font-weight:700; font-size:16px; color:#2f2a26;">No batch runs recorded yet.</p>
                    <p style="margin-top:6px; font-size:13px;">
                        The batch logger will record every execution automatically every 2 minutes.
                    </p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th style="width:190px;">Time (IST)</th>
                                <th style="width:130px;">Status</th>
                                <th>Posters Found</th>
                                <th style="width:120px;">Sent</th>
                                <th style="width:100px;">Skipped</th>
                                <th style="width:100px;">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRuns as $index => $run)
                            @php
                                $ranAtIst = $run->ran_at ? $run->ran_at->timezone('Asia/Kolkata') : null;
                                $expiringCount = (int) $run->day_before_jobs + (int) $run->day_before_offers;
                                $expiredCount  = (int) $run->on_expiry_jobs + (int) $run->on_expiry_offers;
                            @endphp
                            <tr>
                                <td class="num-muted">#{{ $totalRuns - $index }}</td>
                                <td>
                                    <div style="font-weight:700; color:#2f2a26; font-size:13px;">
                                        {{ $ranAtIst ? $ranAtIst->format('d M Y') : '—' }}
                                    </div>
                                    <div style="font-size:12px; color:#8b8179; font-family:ui-monospace,monospace;">
                                        {{ $ranAtIst ? $ranAtIst->format('h:i:s A') : '—' }} IST
                                    </div>
                                </td>
                                <td>
                                    @if($run->status === 'success' && !$run->dry_run)
                                        <span class="pill pill-success">✓ Success</span>
                                    @elseif($run->dry_run)
                                        <span class="pill pill-dry">🧪 Dry Run</span>
                                    @else
                                        <span class="pill pill-disabled">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($expiringCount === 0 && $expiredCount === 0)
                                        <span class="tag-none">0 Posters</span>
                                    @else
                                        @if($run->day_before_jobs > 0)
                                            <span class="tag-pill tag-expiring">⏳ {{ $run->day_before_jobs }} Job Expiring</span>
                                        @endif
                                        @if($run->day_before_offers > 0)
                                            <span class="tag-pill tag-expiring">⏳ {{ $run->day_before_offers }} Offer Expiring</span>
                                        @endif
                                        @if($run->on_expiry_jobs > 0)
                                            <span class="tag-pill tag-expired">✕ {{ $run->on_expiry_jobs }} Job Expired</span>
                                        @endif
                                        @if($run->on_expiry_offers > 0)
                                            <span class="tag-pill tag-expired">✕ {{ $run->on_expiry_offers }} Offer Expired</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($run->notifications_sent > 0)
                                        <span class="badge-sent">📲 {{ $run->notifications_sent }} Sent</span>
                                    @else
                                        <span class="num-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($run->skipped_no_token > 0)
                                        <span class="badge-skip">⚠️ {{ $run->skipped_no_token }}</span>
                                    @else
                                        <span class="num-muted">0</span>
                                    @endif
                                </td>
                                <td style="color:#8b8179; font-family:ui-monospace,monospace; font-size:12px;">
                                    {{ $run->duration_ms }}ms
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
(function() {
    let targetTimestamp = {{ $nextRunTimestampMs ?? 0 }};
    let serverTimestamp = {{ $serverNowTimestampMs ?? 0 }};
    let clientTimestamp = Date.now();
    let clockOffset = serverTimestamp ? (serverTimestamp - clientTimestamp) : 0;
    let stepMinutes = {{ $stepMinutes ?? 2 }};
    let isFetching = false;

    function formatTimeIST(date) {
        return date.toLocaleTimeString('en-IN', {
            timeZone: 'Asia/Kolkata',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        }) + ' IST';
    }

    function updateTimer() {
        const now = Date.now() + clockOffset;
        let diffMs = targetTimestamp - now;

        // Update live IST clock elements
        const istDate = new Date(now);
        document.querySelectorAll('.live-ist-clock').forEach(el => {
            el.textContent = formatTimeIST(istDate);
        });

        const countdownEls = document.querySelectorAll('.live-batch-countdown');
        if (diffMs <= 0) {
            countdownEls.forEach(el => {
                el.innerHTML = '⚡ Running batch...';
                el.classList.add('running');
            });

            if (!isFetching) {
                isFetching = true;
                // Fetch fresh status from backend after 4 seconds
                setTimeout(() => {
                    fetch('{{ route("admin.batch.status") }}')
                        .then(r => r.json())
                        .then(data => {
                            targetTimestamp = data.nextRunTimestampMs;
                            serverTimestamp = data.serverNowTimestampMs;
                            clientTimestamp = Date.now();
                            clockOffset = serverTimestamp - clientTimestamp;
                            isFetching = false;

                            document.querySelectorAll('.live-last-run').forEach(el => {
                                el.textContent = data.lastRunTimeOnly || data.lastRunFormatted;
                            });
                            document.querySelectorAll('.live-last-human').forEach(el => {
                                el.textContent = '(' + data.lastRunHuman + ' · ' + data.lastRunSent + ' sent)';
                            });
                            document.querySelectorAll('.live-next-run').forEach(el => {
                                el.textContent = data.nextRunFormatted;
                            });

                            countdownEls.forEach(el => el.classList.remove('running'));
                        })
                        .catch(() => {
                            targetTimestamp = now + (stepMinutes * 60 * 1000);
                            isFetching = false;
                        });
                }, 4000);
            }
            return;
        }

        const totalSec = Math.floor(diffMs / 1000);
        const hours = Math.floor(totalSec / 3600);
        const mins = Math.floor((totalSec % 3600) / 60);
        const secs = totalSec % 60;
        let formatted = '';
        if (hours > 0) {
            formatted += (hours < 10 ? '0' : '') + hours + 'h ';
        }
        formatted += (mins < 10 ? '0' : '') + mins + 'm ' + (secs < 10 ? '0' : '') + secs + 's';

        countdownEls.forEach(el => {
            el.classList.remove('running');
            el.textContent = formatted;
        });
    }

    updateTimer();
    setInterval(updateTimer, 1000);
})();
</script>
</body>
</html>
