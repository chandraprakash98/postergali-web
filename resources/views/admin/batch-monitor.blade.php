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
        .topbar { padding: 28px 40px; display: flex; justify-content: space-between; align-items: center; gap: 24px; }
        .page-heading h2 { font-size: 22px; color: #302b27; margin: 0; font-weight: 800; }
        .page-heading p { color: #8b8179; margin: 4px 0 0; }
        .content { padding: 0 40px 80px; }

        /* ── Status banner ── */
        .status-banner {
            display: flex; align-items: center; gap: 16px;
            padding: 18px 24px; border-radius: 14px;
            margin-bottom: 28px; font-weight: 600;
        }
        .status-banner.enabled  { background: #eaf7ef; border: 1.5px solid #b7dfcd; color: #1a6147; }
        .status-banner.disabled { background: #fdecea; border: 1.5px solid #f5c6c4; color: #8b1a1a; }
        .status-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .status-dot.on  { background: var(--success); box-shadow: 0 0 0 3px rgba(47,143,107,0.25); animation: pulse 2s infinite; }
        .status-dot.off { background: var(--danger); }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(47,143,107,0.25); }
            50%       { box-shadow: 0 0 0 6px rgba(47,143,107,0.10); }
        }
        .status-banner .meta { margin-left: auto; display: flex; gap: 24px; font-size: 13px; font-weight: 500; opacity: 0.85; }
        .status-banner .meta span { display: flex; gap: 6px; align-items: center; }

        /* ── Stats grid ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
        .stat-card { background: var(--panel); padding: 20px 22px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 6px 18px rgba(43,30,24,0.04); }
        .stat-info h3 { color: #8b8179; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .stat-number { font-size: 28px; color: #2f2a26; font-weight: 800; }
        .stat-sub { font-size: 12px; color: #a09890; margin-top: 2px; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .stat-icon.runs        { background: #ede9fe; color: #7c3aed; }
        .stat-icon.notifs      { background: #eaf7ef; color: var(--success); }
        .stat-icon.skipped     { background: #fff7e6; color: var(--warning); }
        .stat-icon.last        { background: #e0f2fe; color: var(--info); }

        /* ── Table ── */
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .section-header h3 { font-size: 16px; color: #2f2a26; font-weight: 800; }
        .section-header .badge { background: var(--accent); color: #fff; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }

        table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        thead th { background: transparent; color: #9a8f86; text-align: left; padding: 8px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        tbody tr { background: var(--panel); border-radius: 10px; box-shadow: 0 4px 10px rgba(43,30,24,0.03); }
        tbody tr td:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        tbody tr td:last-child  { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        td { padding: 12px 16px; vertical-align: middle; color: #4f463f; font-size: 14px; }

        .pill { padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px; display: inline-block; }
        .pill-success  { background: #eaf7ef; color: var(--success); }
        .pill-disabled { background: #f3f4f6; color: #6b7280; }
        .pill-dry      { background: #fff7e6; color: var(--warning); }

        .num { font-weight: 700; color: #302b27; }
        .num-muted { font-weight: 700; color: #aaa; }

        .empty-state { padding: 60px 20px; text-align: center; color: #8b8179; }
        .empty-state .icon { font-size: 40px; display: block; margin-bottom: 12px; }

        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .main-content { margin-left: 0; }
            .sidebar { display: none; }
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
                <h2>🔔 Batch Monitor</h2>
                <p>Real-time status of the notification batch job</p>
            </div>
        </div>

        <div class="content">

            {{-- ── Status Banner ── --}}
            <div class="status-banner {{ $enabled ? 'enabled' : 'disabled' }}">
                <div class="status-dot {{ $enabled ? 'on' : 'off' }}"></div>
                <div>
                    <div style="font-size:15px;">
                        Batch is <strong>{{ $enabled ? 'ENABLED' : 'DISABLED' }}</strong>
                    </div>
                    <div style="font-size:12px; margin-top:3px; opacity:0.8;">
                        Schedule: <code>{{ $schedule }}</code> &nbsp;·&nbsp; Window: {{ $windowHours }}h ahead
                    </div>
                </div>
                <div class="meta">
                    @if($nextRun)
                    <span>⏭️ Next run: <strong>{{ $nextRun->format('H:i') }}</strong>
                        (in {{ now()->diffInMinutes($nextRun, false) }} min)</span>
                    @endif
                    @if($lastRun)
                    <span>🕐 Last run: <strong>{{ $lastRun->ran_at->diffForHumans() }}</strong></span>
                    @endif
                </div>
            </div>

            {{-- ── Stats Grid ── --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Total Runs</h3>
                        <div class="stat-number">{{ number_format($totalRuns) }}</div>
                        <div class="stat-sub">since tracking began</div>
                    </div>
                    <div class="stat-icon runs">🔁</div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Notifications Sent</h3>
                        <div class="stat-number">{{ number_format($totalNotifications) }}</div>
                        <div class="stat-sub">all-time FCM pushes</div>
                    </div>
                    <div class="stat-icon notifs">📲</div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Skipped (No Token)</h3>
                        <div class="stat-number">{{ number_format($totalSkipped) }}</div>
                        <div class="stat-sub">no FCM token found</div>
                    </div>
                    <div class="stat-icon skipped">⚠️</div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Last Run</h3>
                        <div class="stat-number" style="font-size:16px; margin-top:4px;">
                            {{ $lastRun ? $lastRun->ran_at->format('d M, H:i') : '—' }}
                        </div>
                        @if($lastRun)
                        <div class="stat-sub">{{ $lastRun->duration_ms }}ms duration</div>
                        @endif
                    </div>
                    <div class="stat-icon last">🕐</div>
                </div>
            </div>

            {{-- ── Run History Table ── --}}
            <div class="section-header">
                <h3>Run History</h3>
                <span class="badge">Last 50 runs</span>
            </div>

            @if($recentRuns->isEmpty())
                <div class="empty-state">
                    <span class="icon">📭</span>
                    <p>No batch runs recorded yet.</p>
                    <p style="margin-top:6px; font-size:13px;">
                        The batch logs itself every time it runs via the scheduler.
                    </p>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ran At</th>
                            <th>Status</th>
                            <th>Day-Before Jobs</th>
                            <th>Day-Before Offers</th>
                            <th>Expired Jobs</th>
                            <th>Expired Offers</th>
                            <th>Sent</th>
                            <th>Skipped</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRuns as $index => $run)
                        <tr>
                            <td class="num-muted">#{{ $totalRuns - $index }}</td>
                            <td>
                                <div style="font-weight:600; color:#302b27;">{{ $run->ran_at->format('d M Y') }}</div>
                                <div style="font-size:12px; color:#9a8f86;">{{ $run->ran_at->format('H:i:s') }}</div>
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
                            <td class="{{ $run->day_before_jobs > 0 ? 'num' : 'num-muted' }}">
                                {{ $run->day_before_jobs }}
                            </td>
                            <td class="{{ $run->day_before_offers > 0 ? 'num' : 'num-muted' }}">
                                {{ $run->day_before_offers }}
                            </td>
                            <td class="{{ $run->on_expiry_jobs > 0 ? 'num' : 'num-muted' }}">
                                {{ $run->on_expiry_jobs }}
                            </td>
                            <td class="{{ $run->on_expiry_offers > 0 ? 'num' : 'num-muted' }}">
                                {{ $run->on_expiry_offers }}
                            </td>
                            <td>
                                @if($run->notifications_sent > 0)
                                    <span style="font-weight:700; color:var(--success);">
                                        📲 {{ $run->notifications_sent }}
                                    </span>
                                @else
                                    <span class="num-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($run->skipped_no_token > 0)
                                    <span style="font-weight:700; color:var(--warning);">
                                        ⚠️ {{ $run->skipped_no_token }}
                                    </span>
                                @else
                                    <span class="num-muted">0</span>
                                @endif
                            </td>
                            <td style="color:#9a8f86; font-size:13px;">{{ $run->duration_ms }}ms</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</div>
</body>
</html>
