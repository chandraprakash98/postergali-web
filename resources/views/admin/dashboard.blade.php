<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Postergali</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root{
            --bg: #f4efe6;
            --sidebar: #9b342b;
            --accent: #e58b6a;
            --panel: #fffdf8;
            --muted: #79706b;
            --success: #2f8f6b;
            --danger: #d9534f;
        }

        body{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', sans-serif;
            background: var(--bg);
            color: var(--muted);
        }

        .container{ display:flex; min-height:100vh; }

        .sidebar{ width:240px; background:var(--sidebar); color:#fff; padding:28px 22px; position:fixed; height:100vh; overflow-y:auto; display:flex; flex-direction:column; }
        .logo{ font-size:20px; font-weight:800; margin-bottom:30px; }
        .logo .menu-icon{ background:#fff; color:var(--sidebar); padding:8px; border-radius:8px; display:inline-block; }
        .menu-items{ display:flex; flex-direction:column; gap:14px; flex:1; }
        .sidebar-footer{ margin-top:auto; padding-top:20px; border-top:1px solid rgba(255,255,255,0.12); display:flex; flex-direction:column; gap:14px; }
        .menu-item{ color: rgba(255,255,255,0.95); text-decoration:none; padding:12px 14px; border-radius:10px; display:flex; gap:12px; align-items:center; font-weight:600; }
        .menu-item:hover{ background: rgba(255,255,255,0.06); }
        .menu-item.active{ background: rgba(255,255,255,0.12); }
        /* Batch status mini-widget */
        .batch-widget{ background: rgba(0,0,0,0.18); border-radius:10px; padding:10px 12px; font-size:12px; color:rgba(255,255,255,0.85); display:flex; flex-direction:column; gap:5px; }
        .batch-widget-row{ display:flex; align-items:center; justify-content:space-between; gap:6px; }
        .batch-widget-label{ color:rgba(255,255,255,0.5); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; }
        .batch-widget-val{ font-weight:700; color:#fff; font-size:12px; }
        .batch-run-dot{ width:7px; height:7px; border-radius:50%; display:inline-block; flex-shrink:0; }
        .batch-run-dot.running{ background:#4ade80; box-shadow:0 0 0 0 rgba(74,222,128,0.7); animation:bpulse 1.8s infinite; }
        .batch-run-dot.stopped{ background:#f87171; }
        @keyframes bpulse{ 0%{box-shadow:0 0 0 0 rgba(74,222,128,0.7);} 70%{box-shadow:0 0 0 6px rgba(74,222,128,0);} 100%{box-shadow:0 0 0 0 rgba(74,222,128,0);} }

        .main-content{ flex:1; margin-left:240px; }
        .topbar{ padding:28px 40px; display:flex; justify-content:space-between; align-items:center; gap:24px; }
        .page-heading{ display:flex; flex-direction:column; gap:4px; max-width:55%; }
        .page-heading h2{ font-size:22px; color:#302b27; margin:0; font-weight:800; }
        .page-heading p{ color:#8b8179; margin:0; }
        .user-section{ display:flex; gap:16px; align-items:center; color:var(--muted); }
        .logout-btn{ background: var(--accent); color:#fff; border:none; padding:8px 14px; border-radius:20px; cursor:pointer; font-weight:700; }

        .content{ padding:30px 40px 80px; }
        .page-title{ font-size:22px; color:#302b27; margin-bottom:6px; }
        .page-subtitle{ color:#8b8179; margin-bottom:20px; }
        .view-btn{ background: var(--success); color:#fff; padding:8px 10px; min-width:80px; display:inline-flex; align-items:center; justify-content:center; border-radius:20px; text-decoration:none; font-weight:700; white-space:nowrap; }

        .stats-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:28px; }
        .stat-card{ background:var(--panel); padding:20px; border-radius:14px; display:flex; justify-content:space-between; align-items:center; box-shadow: 0 6px 18px rgba(43,30,24,0.04); }
        .stat-info h3{ color:#8b8179; font-size:12px; text-transform:uppercase; letter-spacing:1px; }
        .stat-number{ font-size:26px; color:#2f2a26; font-weight:800; }
        .stat-icon{ width:54px; height:54px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; }
        .stat-icon.pending{ background:#fff7e6; color:#b77400; }
        .stat-icon.live{ background:#eaf7ef; color:var(--success); }
        .stat-icon.expired{ background:#fdecea; color:var(--danger); }

        .table-section{ background: transparent; }
        .table-header{ padding:12px 18px 18px 18px; display:flex; justify-content:space-between; align-items:center; }
        .table-header h3{ font-size:16px; color:#2f2a26; font-weight:800; }

        .type-toggle{ display:flex; align-items:center; gap:8px; }
        .type-toggle .pill{ background: var(--panel); border-radius:999px; padding:6px; display:flex; gap:6px; align-items:center; }
        .type-toggle button{ background:transparent; border:none; padding:8px 14px; border-radius:999px; cursor:pointer; font-weight:700; color:var(--muted); }
        .type-toggle button.active{ background: var(--accent); color:#fff; }

        table{ width:100%; border-collapse:separate; border-spacing:0 10px; }
        thead th{ background: transparent; color:#9a8f86; text-align:left; padding:10px 18px; font-size:12px; font-weight:700; text-transform:uppercase; }
        tbody tr{ background: var(--panel); border-radius:10px; box-shadow: 0 6px 12px rgba(43,30,24,0.03); }
        tbody tr td:first-child{ border-top-left-radius:10px; border-bottom-left-radius:10px; }
        tbody tr td:last-child{ border-top-right-radius:10px; border-bottom-right-radius:10px; }
        td{ padding:14px 18px; vertical-align:middle; color:#4f463f; }

        .status-badge{ padding:6px 12px; border-radius:999px; font-weight:700; font-size:12px; }
        .status-pending{ background:#fff7e6; color:#b77400; }
        .status-live{ background:#eaf7ef; color:var(--success); }
        .status-expired{ background:#fdecea; color:var(--danger); }
        .status-active{ background:#eaf7ef; color:var(--success); }
        .status-success{ background:#eaf7ef; color:var(--success); }

        .view-btn{ background: var(--success); color:#fff; padding:8px 14px; border-radius:20px; text-decoration:none; font-weight:700; }

        .empty-state{ padding:40px 18px; text-align:center; color:#8b8179; display:none; }

        .credit-settings-grid{ display:grid; grid-template-columns:repeat(3, 1fr); gap:18px; margin-bottom:24px; }
        @media (max-width: 900px){ .stats-grid, .credit-settings-grid{ grid-template-columns:1fr; } .main-content{ margin-left:0; } .sidebar{ display:none; } }
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
                <a href="{{ route('admin.allAds') }}" class="menu-item {{ ($active ?? 'all') === 'all' ? 'active' : '' }}">
                    <span class="menu-icon">📋</span>
                    All Ads
                </a>
                <a href="{{ route('admin.pendingAds') }}" class="menu-item {{ ($active ?? null) === 'pending' ? 'active' : '' }}">
                    <span class="menu-icon">⏳</span>
                    Pending Ads
                </a>
                <a href="{{ route('admin.liveAds') }}" class="menu-item {{ ($active ?? null) === 'live' ? 'active' : '' }}">
                    <span class="menu-icon">✓</span>
                    Live Ads
                </a>
                <a href="{{ route('admin.expiredAds') }}" class="menu-item {{ ($active ?? null) === 'expired' ? 'active' : '' }}">
                    <span class="menu-icon">✕</span>
                    Expired Ads
                </a>
                <a href="{{ route('admin.pricingInfo') }}" class="menu-item {{ ($active ?? null) === 'pricing' ? 'active' : '' }}">
                    <span class="menu-icon">💰</span>
                    Pricing Info
                </a>
                <a href="{{ route('admin.referrals') }}" class="menu-item {{ ($active ?? null) === 'referrals' ? 'active' : '' }}">
                    <span class="menu-icon">🤝</span>
                    Referrals
                </a>
                <a href="{{ route('admin.ugcReports') }}" class="menu-item {{ ($active ?? null) === 'reports' ? 'active' : '' }}">
                    <span class="menu-icon">🚩</span>
                    UGC Reports
                </a>
                <a href="{{ route('admin.creditSettings') }}" class="menu-item {{ ($active ?? null) === 'credit-settings' ? 'active' : '' }}">
                    <span class="menu-icon">💳</span>
                    Credit Settings
                </a>
            </div>
            <div class="sidebar-footer">
                {{-- Batch Status Mini Widget --}}
                <div class="batch-widget" id="batch-widget">
                    <div class="batch-widget-row">
                        <span class="batch-widget-label">🔔 Batch</span>
                        <span class="batch-widget-val" id="bw-name" style="font-family:monospace;font-size:11px;">postergali-alpha</span>
                    </div>
                    <div class="batch-widget-row">
                        <span class="batch-widget-label">Engine</span>
                        <span style="display:inline-flex;align-items:center;gap:5px;">
                            <span class="batch-run-dot running" id="bw-dot"></span>
                            <span class="batch-widget-val" id="bw-running">Running</span>
                        </span>
                    </div>
                    <div class="batch-widget-row">
                        <span class="batch-widget-label">Schedule</span>
                        <span class="batch-widget-val" id="bw-schedule" style="font-size:11px;">6:00 AM IST</span>
                    </div>
                    <div class="batch-widget-row">
                        <span class="batch-widget-label">Notifs Sent</span>
                        <span class="batch-widget-val" id="bw-sent">—</span>
                    </div>
                </div>
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
                    @if(($active ?? 'all') === 'reports')
                        <h2>UGC Reports</h2>
                        <p>Review reported posters and their submitted reasons</p>
                    @elseif(($active ?? 'all') === 'referrals')
                        <h2>Referrals</h2>
                        <p>Referral records from the referral table</p>
                    @elseif(($active ?? 'all') === 'pricing')
                        <h2>Pricing Info</h2>
                        <p>Manage subscription plans</p>
                    @elseif(($active ?? 'all') === 'credit-settings')
                        <h2>Credit Settings</h2>
                        <p>Configure automated credit balance allocations</p>
                    @else
                        <h2>Dashboard</h2>
                        <p>Overview of all ad listings</p>
                    @endif
                </div>
                @if(!in_array(($active ?? 'all'), ['referrals', 'pricing', 'reports', 'credit-settings'], true))
                    <div class="type-toggle">
                        <div class="pill">
                            <button id="toggle-jobs" class="active">Jobs</button>
                            <button id="toggle-offers">Offers</button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="content">
                @if (session('success'))
                    <div style="background:#eaf7ef; color:#2f8f6b; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:700; border: 1px solid #c2ebd4; display:flex; align-items:center; gap:8px;">
                        <span>✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div style="background:#fff1f1; border:1px solid #f7c2c2; color:#b91c1c; padding:14px 18px; border-radius:10px; margin-bottom:20px;">
                        <ul style="margin:0; padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(($active ?? 'all') === 'reports')
                    <div class="table-section">
                        <div class="table-header">
                            <h3>Reported Posters</h3>
                        </div>

                        @if($reports->count() > 0)
                            <table>
                                <thead>
                                    <tr>
                                        <th>REPORTED AT</th>
                                        <th>POSTER</th>
                                        <th>REASON</th>
                                        <th>DETAILS</th>
                                        <th>AUTHOR ID</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                {{ strtoupper($report->content_type) }} #{{ $report->content_id }}
                                                @if($report->poster)
                                                    <br><small>{{ $report->poster->business_name }}</small>
                                                @else
                                                    <br><small>Poster no longer exists</small>
                                                @endif
                                            </td>
                                            <td>{{ $report->reason }}</td>
                                            <td>{{ $report->details ?: '—' }}</td>
                                            <td>{{ $report->author_id ?: '—' }}</td>
                                            <td>
                                                @if($report->poster)
                                                    <a href="{{ route('admin.ad.show', ['type' => $report->content_type, 'id' => $report->content_id]) }}" class="view-btn">Review Poster</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="padding: 12px 18px;">{{ $reports->links() }}</div>
                        @else
                            <div class="empty-state" style="display:block;">
                                <p>No reported posters found</p>
                            </div>
                        @endif
                    </div>

                @elseif(($active ?? 'all') === 'referrals')
                    <div class="table-section">
                        <div class="table-header">
                            <h3>All Referrals</h3>
                        </div>

                        @if(isset($referrals) && count($referrals) > 0)
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>REFERRAL NAME</th>
                                        <th>REFERRAL MOBILE</th>
                                        <th>REFERRER NAME</th>
                                        <th>REFERRER MOBILE</th>
                                        <th>STATUS</th>
                                        <th>CREATED AT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $referral)
                                        <tr>
                                            <td>#{{ $referral->id }}</td>
                                            <td>{{ $referral->referral_name }}</td>
                                            <td>{{ $referral->referral_mobile }}</td>
                                            <td>{{ $referral->referrer_name }}</td>
                                            <td>{{ $referral->referrer_mobile }}</td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $referral->status ?? 'unknown')) }}">
                                                    {{ ucfirst($referral->status ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            <td>{{ $referral->created_at ? $referral->created_at->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <p>No referrals found</p>
                            </div>
                        @endif
                    </div>

                @elseif(($active ?? 'all') === 'credit-settings')
                    <div class="table-section">
                        <div class="table-header" style="padding: 0 0 16px 0;">
                            <div>
                                <h3 style="font-size: 20px;">Automated Credit Configuration</h3>
                                <p style="color: #8b8179; font-size: 13px;">Configure credits awarded across customer lifecycle events</p>
                            </div>
                            @if(isset($creditSettings) && $creditSettings->updated_by)
                                <div style="font-size: 12px; color: #8b8179; background: var(--panel); padding: 8px 14px; border-radius: 999px; box-shadow: 0 2px 6px rgba(43,30,24,0.04);">
                                    Last updated by <strong>{{ $creditSettings->updated_by }}</strong> on {{ $creditSettings->updated_at?->format('M d, Y \a\t H:i') }}
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.creditSettings.update') }}">
                            @csrf
                            
                            <!-- 3 Clear Horizontal Boxes -->
                            <div class="credit-settings-grid">
                                
                                <!-- Box 1: Welcome Sign-Up Credits -->
                                <div style="background: var(--panel); border-radius: 14px; padding: 22px; box-shadow: 0 6px 18px rgba(43,30,24,0.04); display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--accent);">
                                    <div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: var(--accent); background: #fdf1ec; padding: 3px 8px; border-radius: 6px;">Direct Sign-Up</span>
                                            <span style="font-size: 20px;">🎁</span>
                                        </div>
                                        <label for="signup_credit" style="display: block; font-size: 15px; font-weight: 800; color: #2f2a26; margin-bottom: 4px;">
                                            Welcome Sign-Up Credits
                                        </label>
                                        <p style="font-size: 12px; color: #8b8179; margin-bottom: 16px; line-height: 1.4;">
                                            Given to new users upon registration when no influencer coupon is used.
                                        </p>
                                    </div>
                                    <div>
                                        <input 
                                            type="number" 
                                            name="signup_credit" 
                                            id="signup_credit" 
                                            min="0" 
                                            step="1" 
                                            value="{{ old('signup_credit', $creditSettings->signup_credit ?? 1000) }}" 
                                            required
                                            style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #d7cfc7; font-size: 18px; font-weight: 800; color: #2f2a26; background: #fff; box-sizing: border-box;"
                                        >
                                        <span style="display: block; font-size: 11px; color: #a49a90; margin-top: 6px;">Default: 1,000 credits</span>
                                    </div>
                                </div>

                                <!-- Box 2: OTP First Login Credits -->
                                <div style="background: var(--panel); border-radius: 14px; padding: 22px; box-shadow: 0 6px 18px rgba(43,30,24,0.04); display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #2f8f6b;">
                                    <div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #2f8f6b; background: #eaf7ef; padding: 3px 8px; border-radius: 6px;">OTP Verification</span>
                                            <span style="font-size: 20px;">📱</span>
                                        </div>
                                        <label for="otp_credit" style="display: block; font-size: 15px; font-weight: 800; color: #2f2a26; margin-bottom: 4px;">
                                            OTP First Login Credits
                                        </label>
                                        <p style="font-size: 12px; color: #8b8179; margin-bottom: 16px; line-height: 1.4;">
                                            Initial wallet balance created when a user verifies their phone via OTP for the first time.
                                        </p>
                                    </div>
                                    <div>
                                        <input 
                                            type="number" 
                                            name="otp_credit" 
                                            id="otp_credit" 
                                            min="0" 
                                            step="1" 
                                            value="{{ old('otp_credit', $creditSettings->otp_credit ?? 1000) }}" 
                                            required
                                            style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #d7cfc7; font-size: 18px; font-weight: 800; color: #2f2a26; background: #fff; box-sizing: border-box;"
                                        >
                                        <span style="display: block; font-size: 11px; color: #a49a90; margin-top: 6px;">Default: 1,000 credits</span>
                                    </div>
                                </div>

                                <!-- Box 3: Referral Reward Credits -->
                                <div style="background: var(--panel); border-radius: 14px; padding: 22px; box-shadow: 0 6px 18px rgba(43,30,24,0.04); display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--sidebar);">
                                    <div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: var(--sidebar); background: #fbeee8; padding: 3px 8px; border-radius: 6px;">On Ad Approval</span>
                                            <span style="font-size: 20px;">🤝</span>
                                        </div>
                                        <label for="referral_credit" style="display: block; font-size: 15px; font-weight: 800; color: #2f2a26; margin-bottom: 4px;">
                                            Referral Reward Credits
                                        </label>
                                        <p style="font-size: 12px; color: #8b8179; margin-bottom: 16px; line-height: 1.4;">
                                            Reward added to the referrer's wallet whenever their referred friend's ad is approved by Admin.
                                        </p>
                                    </div>
                                    <div>
                                        <input 
                                            type="number" 
                                            name="referral_credit" 
                                            id="referral_credit" 
                                            min="0" 
                                            step="1" 
                                            value="{{ old('referral_credit', $creditSettings->referral_credit ?? 100) }}" 
                                            required
                                            style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #d7cfc7; font-size: 18px; font-weight: 800; color: #2f2a26; background: #fff; box-sizing: border-box;"
                                        >
                                        <span style="display: block; font-size: 11px; color: #a49a90; margin-top: 6px;">Default: 100 credits</span>
                                    </div>
                                </div>

                            </div>

                            <!-- Save Button -->
                            <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
                                <button type="submit" class="view-btn" style="padding: 12px 36px; font-size: 15px; cursor: pointer; border: none; background: var(--sidebar); border-radius: 10px; color: #fff; font-weight: 800;">
                                    💾 Save Credit Settings
                                </button>
                            </div>

                            <!-- Clear Informational Guide: When Each Credit is Sent & Influencer Flow -->
                            <div style="background: var(--panel); border-radius: 14px; padding: 22px 24px; box-shadow: 0 4px 14px rgba(43,30,24,0.03); border: 1px solid #ebdcd5;">
                                <h4 style="font-size: 14px; font-weight: 800; color: #302b27; margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
                                    <span>💡</span> When Each Credit is Sent & How Influencer Coupons Differ:
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; font-size: 12px; color: #6e655f; line-height: 1.5;">
                                    <div style="background: #fff; padding: 14px; border-radius: 10px; border: 1px solid #ede7e3;">
                                        <div style="font-weight: 800; color: #2f2a26; margin-bottom: 4px;">1. Standard User (No Coupon):</div>
                                        <span>User registers directly without entering an influencer promo code &rarr; they immediately receive the <strong>Welcome Sign-Up Credits</strong> configured above.</span>
                                    </div>
                                    <div style="background: #fff; padding: 14px; border-radius: 10px; border: 1px solid #ede7e3;">
                                        <div style="font-weight: 800; color: #2f2a26; margin-bottom: 4px;">2. Influencer Coupon User:</div>
                                        <span>User registers using an Influencer Coupon (e.g. <code>DIWALI50</code>) &rarr; gets the specific bonus credits defined in the <strong>Bonus Coupons</strong> table. When their poster is approved, an incentive record is automatically created for that influencer.</span>
                                    </div>
                                    <div style="background: #fff; padding: 14px; border-radius: 10px; border: 1px solid #ede7e3;">
                                        <div style="font-weight: 800; color: #2f2a26; margin-bottom: 4px;">3. Friend Referral Reward:</div>
                                        <span>User A refers User B. When User B's ad (Job or Offer) is approved by Admin in the panel &rarr; User A gets the <strong>Referral Reward Credits</strong> configured above, plus an instant push notification.</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                @elseif(($active ?? 'all') !== 'pricing')
                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>Pending Verification</h3>
                                <div class="stat-number">{{ $stats['pending'] ?? 0 }}</div>
                            </div>
                            <div class="stat-icon pending">⏳</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>Live Ads</h3>
                                <div class="stat-number">{{ $stats['live'] ?? 0 }}</div>
                            </div>
                            <div class="stat-icon live">✓</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>Expired Ads</h3>
                                <div class="stat-number">{{ $stats['expired'] ?? 0 }}</div>
                            </div>
                            <div class="stat-icon expired">✕</div>
                        </div>
                    </div>

                    <!-- Ads Table -->
                    <div class="table-section">
                        <div class="table-header">
                            <h3>
                                @if(($active ?? 'all') === 'pending')
                                    Pending Ads
                                @elseif(($active ?? null) === 'live')
                                    Live Ads
                                @elseif(($active ?? null) === 'expired')
                                    Expired Ads
                                @else
                                    All Ads
                                @endif
                            </h3>
                        </div>

                        @if(isset($allAds) && count($allAds) > 0)
                            <table>
                                <thead>
                                    <tr>
                                        <th>AD ID</th>
                                        <th>BUSINESS NAME</th>
                                        <th>PHONE NUMBER</th>
                                        <th>CITY</th>
                                        <th>AD STATUS</th>
                                        <th>DATE POSTED</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allAds as $ad)
                                        <tr data-type="{{ strtolower($ad['type'] ?? 'ad') }}">
                                            <td>{{ $ad['id'] }}</td>
                                            <td>{{ $ad['business_name'] }}</td>
                                            <td>{{ $ad['phone'] }}</td>
                                            <td>{{ $ad['city'] }}</td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $ad['status'])) }}">
                                                    {{ $ad['status'] }}
                                                </span>
                                            </td>
                                            <td>{{ $ad['date'] }}</td>
                                            <td>
                                                <a href="{{ route('admin.ad.show', ['type' => strtolower($ad['type']), 'id' => $ad['model_id']]) }}" class="view-btn">👁️ View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <p>No ads found</p>
                            </div>
                        @endif
                        <div id="filter-empty" class="empty-state">No items match the selected filter</div>
                    </div>

                @else
                    <!-- Pricing Info Section -->
                    <div class="table-section">
                        <div class="table-header">
                            <h3>All Plans</h3>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <a href="{{ route('admin.plans.create') }}" class="view-btn" style="text-decoration:none;">➕ Add Plan</a>
                                <span class="filter-icon">⚙️</span>
                            </div>
                        </div>

                        @if(isset($plans) && count($plans) > 0)
                            <table class="pricing-table">
                                <thead>
                                    <tr>
                                        <th>PLAN ID</th>
                                        <th>PLAN TITLE</th>
                                        <th>DURATION</th>
                                        <th>PRICE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plans as $plan)
                                        <tr>
                                            <td>PLAN{{ str_pad($plan->id, 3, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $plan->plan_title }}</td>
                                            <td>{{ $plan->duration }}</td>
                                            <td>₹{{ $plan->price }}</td>
                                            <td>
                                                <a href="{{ route('admin.plans.edit', $plan->id) }}" class="view-btn" style="text-decoration:none;">✏️ Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <p>No plans found</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const btnJobs = document.getElementById('toggle-jobs');
    const btnOffers = document.getElementById('toggle-offers');
    const rows = Array.from(document.querySelectorAll('tbody tr'));
    const empty = document.getElementById('filter-empty');

    function setActive(btn){
        if(!btnJobs || !btnOffers) return;
        btnJobs.classList.remove('active');
        btnOffers.classList.remove('active');
        if(btn) btn.classList.add('active');
    }

    function filter(type){
        let visible = 0;
        rows.forEach(r => {
            const t = (r.getAttribute('data-type') || '').toLowerCase();
            if(type === 'jobs'){
                if(t === 'job') { r.style.display = 'table-row'; visible++; } else { r.style.display = 'none'; }
            } else if(type === 'offers'){
                if(t === 'offer') { r.style.display = 'table-row'; visible++; } else { r.style.display = 'none'; }
            } else {
                r.style.display = 'table-row'; visible++;
            }
        });
        if(!empty) return;
        empty.style.display = visible > 0 ? 'none' : 'block';
    }

    // only enable filtering when the toggle exists
    if(btnJobs && btnOffers){
        setActive(btnJobs);
        filter('jobs');

        btnJobs.addEventListener('click', function(){ setActive(btnJobs); filter('jobs'); });
        btnOffers.addEventListener('click', function(){ setActive(btnOffers); filter('offers'); });
    }
});
</script>

<script>
// Batch status sidebar widget
(function() {
    function loadBatchStatus() {
        fetch('{{ route("admin.batch.status") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(d => {
                const name   = document.getElementById('bw-name');
                const dot    = document.getElementById('bw-dot');
                const run    = document.getElementById('bw-running');
                const sched  = document.getElementById('bw-schedule');
                const sent   = document.getElementById('bw-sent');

                if (name)  name.textContent  = d.batch_name  || 'postergali-alpha';
                if (sched) sched.textContent = d.schedule_human || '6:00 AM IST';
                if (sent)  sent.textContent  = d.total_sent  != null ? d.total_sent : '—';

                const running = d.enabled !== false;
                if (dot) { dot.className = 'batch-run-dot ' + (running ? 'running' : 'stopped'); }
                if (run) { run.textContent = running ? 'Running' : 'Stopped'; }
            })
            .catch(() => {}); // silently fail – widget shows defaults
    }
    loadBatchStatus();
})();
</script>
</body>
</html>
