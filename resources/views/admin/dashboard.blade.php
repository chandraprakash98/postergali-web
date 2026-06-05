<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Postergali</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background: white;
            border-right: 1px solid #e0e0e0;
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
        }

        .menu-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .menu-item {
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #666;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }

        .menu-item:hover {
            background-color: #f5f5f5;
            color: #333;
        }

        .menu-item.active {
            background-color: #f0f0ff;
            color: #667eea;
        }

        .menu-icon {
            font-size: 18px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 220px;
        }

        .topbar {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .topbar h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logout-btn {
            background-color: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background-color: #764ba2;
        }

        .content {
            padding: 40px;
        }

        .page-title {
            margin-bottom: 8px;
            font-size: 28px;
            font-weight: 600;
        }

        .page-subtitle {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-info h3 {
            color: #999;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-icon.pending {
            background-color: #fff3cd;
        }

        .stat-icon.live {
            background-color: #d4edda;
        }

        .stat-icon.expired {
            background-color: #f8d7da;
        }

        /* Table Section */
        .table-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 30px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .filter-icon {
            cursor: pointer;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f9f9f9;
            padding: 16px 30px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            border-bottom: 1px solid #f0f0f0;
        }

        td {
            padding: 16px 30px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending,
        .status-pending-verification {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-live,
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected,
        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }

        .view-btn {
            background-color: #1a1a2e;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }

        .view-btn:hover {
            background-color: #0f0f1e;
        }

        /* Pricing Table */
        .pricing-table {
            margin-top: 20px;
        }

        .pricing-table th {
            background-color: #f9f9f9;
        }

        .empty-state {
            padding: 60px 30px;
            text-align: center;
            color: #999;
        }

        .empty-state p {
            font-size: 16px;
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 20px;
            }

            .content {
                padding: 20px;
            }

            th, td {
                padding: 12px 16px;
                font-size: 13px;
            }
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
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <h1>Dashboard</h1>
                <div class="user-section">
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>

            <div class="content">
                @if(($active ?? 'all') !== 'pricing')
                    <h2 class="page-title">Dashboard</h2>
                    <p class="page-subtitle">Overview of all ad listings</p>

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
                            <span class="filter-icon">⚙️</span>
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
                                        <tr>
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
                                                <button class="view-btn">👁️ View</button>
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
                    </div>

                @else
                    <!-- Pricing Info Section -->
                    <h2 class="page-title">Pricing Info</h2>
                    <p class="page-subtitle">Manage subscription plans</p>

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
</body>
</html>
