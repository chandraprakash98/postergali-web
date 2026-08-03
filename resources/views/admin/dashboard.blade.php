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
        .sidebar-footer{ margin-top:auto; padding-top:20px; border-top:1px solid rgba(255,255,255,0.12); }
        .menu-item{ color: rgba(255,255,255,0.95); text-decoration:none; padding:12px 14px; border-radius:10px; display:flex; gap:12px; align-items:center; font-weight:600; }
        .menu-item:hover{ background: rgba(255,255,255,0.06); }
        .menu-item.active{ background: rgba(255,255,255,0.12); }

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

        @media (max-width: 900px){ .stats-grid{ grid-template-columns:1fr; } .main-content{ margin-left:0; } .sidebar{ display:none; } }
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
                    @if(($active ?? 'all') === 'referrals')
                        <h2>Referrals</h2>
                        <p>Referral records from the referral table</p>
                    @elseif(($active ?? 'all') === 'pricing')
                        <h2>Pricing Info</h2>
                        <p>Manage subscription plans</p>
                    @else
                        <h2>Dashboard</h2>
                        <p>Overview of all ad listings</p>
                    @endif
                </div>
                <div class="type-toggle">
                    <div class="pill">
                        <button id="toggle-jobs" class="active">Jobs</button>
                        <button id="toggle-offers">Offers</button>
                    </div>
                </div>
            </div>

            <div class="content">
                @if(($active ?? 'all') === 'referrals')
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
                            <span class="filter-icon">⚙️</span>
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
                                                <button class="view-btn">👁️ View</button>
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

    // default to Jobs to match provided image feel
    if(btnJobs && btnOffers){
        setActive(btnJobs);
        filter('jobs');

        btnJobs.addEventListener('click', function(){ setActive(btnJobs); filter('jobs'); });
        btnOffers.addEventListener('click', function(){ setActive(btnOffers); filter('offers'); });
    }
});
</script>
</body>
</html>
