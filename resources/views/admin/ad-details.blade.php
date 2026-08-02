<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ad Details - Postergali</title>
    <style>
        * { box-sizing: border-box; margin:0; padding:0 }

        :root{
            --bg: #f4efe6;
            --sidebar: #9b342b;
            --accent: #e58b6a;
            --panel: #fffdf8;
            --muted: #79706b;
            --success: #2f8f6b;
            --danger: #d9534f;
        }

        body{ font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', sans-serif; background:var(--bg); color:var(--muted); }

        .container{ display:flex; min-height:100vh; }

        .sidebar{ width:240px; background:var(--sidebar); color:#fff; padding:28px 22px; position:fixed; height:100vh; overflow-y:auto; }
        .logo{ font-size:20px; font-weight:800; margin-bottom:30px; }
        .logo .menu-icon{ background:#fff; color:var(--sidebar); padding:8px; border-radius:8px; display:inline-block; }
        .menu-items{ display:flex; flex-direction:column; gap:14px; }
        .menu-item{ color: rgba(255,255,255,0.95); text-decoration:none; padding:12px 14px; border-radius:10px; display:flex; gap:12px; align-items:center; font-weight:600; }
        .menu-item:hover{ background: rgba(255,255,255,0.06); }
        .menu-item.active{ background: rgba(255,255,255,0.12); }

        .main-panel{ flex:1; margin-left:240px; }
        .page-header{ padding:24px 40px; display:flex; justify-content:space-between; align-items:center; gap:16px; }
        .page-title{ font-size:28px; font-weight:800; color:#2f2a26 }
        .page-subtitle{ color:#8b8179; }
        .back-link{ color:#4f463f; text-decoration:none; font-weight:700 }

        .status-pill{ padding:10px 18px; border-radius:999px; font-size:13px; font-weight:700; background:#fff7e6; color:#b77400 }

        .content{ padding:0 40px 40px }
        .grid{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px; margin-bottom:24px }

        .card{ background:var(--panel); border-radius:20px; padding:24px; box-shadow: 0 12px 30px rgba(43,30,24,0.05); }
        .card-header{ display:flex; align-items:center; gap:12px; margin-bottom:18px }
        .card-icon{ width:40px; height:40px; border-radius:12px; display:grid; place-items:center; background:#f3f1ee; color:var(--muted); font-size:18px }
        .card-title{ font-size:14px; font-weight:800; color:#2f2a26 }
        .card-body{ color:#4f463f; font-size:13px; line-height:1.8 }
        .card-body strong{ display:block; color:#2f2a26; margin-bottom:4px }

        .media-list{ display:grid; gap:12px }
        .media-item{ display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:#f9f7f3; border-radius:12px; border:1px solid rgba(47,34,30,0.04); color:#2f2a26 }
        .media-link{ color:#2f2a26; text-decoration:none; font-weight:700 }
        .media-action{ width:32px; height:32px; border-radius:8px; display:grid; place-items:center; background:#fdecea; color:var(--danger) }

        .status-card{ grid-column:span 2 }
        .form-row{ display:grid; gap:16px; grid-template-columns:180px 1fr; margin-bottom:18px; align-items:center }
        .radio-group{ display:flex; gap:16px; flex-wrap:wrap }
        .radio-field{ display:flex; align-items:center; gap:8px; background:#fffdf8; padding:12px 16px; border-radius:999px; border:1px solid rgba(47,34,30,0.04); cursor:pointer }
        .radio-field input{ accent-color:var(--accent) }

        textarea{ width:100%; min-height:130px; border-radius:16px; border:1px solid rgba(47,34,30,0.04); padding:16px; font-family:inherit; font-size:14px; resize:vertical; color:#2f2a26 }

        .form-footer{ display:flex; justify-content:flex-end; margin-top:12px }
        .button-primary{ background:var(--accent); color:#fff; border:none; padding:12px 24px; border-radius:12px; font-size:14px; font-weight:700 }
        .button-primary:hover{ filter:brightness(0.98) }

        .success-banner{ background:#daf5dd; color:#166534; padding:18px 24px; border-radius:16px; margin-bottom:20px; border:1px solid #a7f3d0 }

        @media (max-width:1024px){ .grid{ grid-template-columns:1fr } .sidebar{ position:relative; height:auto } .main-panel{ margin-left:0 } }
        @media (max-width:640px){ .page-header{ flex-direction:column; align-items:flex-start } .form-row{ grid-template-columns:1fr } }

        .loading-overlay{ display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center }
        .loading-overlay.active{ display:flex }
        .loading-content{ display:flex; flex-direction:column; align-items:center; gap:20px }
        .loading-spinner{ width:60px; height:60px; border:5px solid rgba(255,255,255,0.3); border-top:5px solid #ffffff; border-radius:50%; animation:spin 0.8s linear infinite }
        .loading-text{ color:#ffffff; font-size:18px; font-weight:500; text-align:center }
        @keyframes spin{ 0%{ transform:rotate(0deg) } 100%{ transform:rotate(360deg) } }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <span class="menu-icon">🎨</span>
                <span>POSTER<br>GALI</span>
            </div>
            <div class="menu-items">
                <a href="{{ route('admin.allAds') }}" class="menu-item {{ ($active ?? '') === 'all' ? 'active' : '' }}">
                    <span class="menu-icon">📋</span>
                    All Ads
                </a>
                <a href="{{ route('admin.pendingAds') }}" class="menu-item {{ ($active ?? '') === 'pending' ? 'active' : '' }}">
                    <span class="menu-icon">⏳</span>
                    Pending Ads
                </a>
                <a href="{{ route('admin.liveAds') }}" class="menu-item {{ ($active ?? '') === 'live' ? 'active' : '' }}">
                    <span class="menu-icon">✓</span>
                    Live Ads
                </a>
                <a href="{{ route('admin.expiredAds') }}" class="menu-item {{ ($active ?? '') === 'expired' ? 'active' : '' }}">
                    <span class="menu-icon">✕</span>
                    Expired Ads
                </a>
                <a href="{{ route('admin.pricingInfo') }}" class="menu-item {{ ($active ?? '') === 'pricing' ? 'active' : '' }}">
                    <span class="menu-icon">💰</span>
                    Pricing Info
                </a>
            </div>
        </div>

        <div class="main-panel">
            <div class="page-header">
                <div class="page-title-wrapper">
                    <a href="{{ route('admin.dashboard') }}" class="back-link">← Back to Dashboard</a>
                    <h1 class="page-title">Ad Details</h1>
                    <p class="page-subtitle">Complete information for {{ strtoupper($type === 'job' ? 'JOB' : 'OFF') }}{{ str_pad($ad->id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>

                <span class="status-pill">
                    @if($ad->status === 'pending')
                        Pending Verification
                    @elseif($ad->status === 'approved')
                        Approved
                    @elseif($ad->status === 'rejected')
                        Rejected
                    @else
                        {{ ucfirst($ad->status) }}
                    @endif
                </span>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="success-banner">{{ session('success') }}</div>
                @endif

                <div class="grid">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">🏢</div>
                            <div>
                                <div class="card-title">Business Information</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <strong>Business Name</strong>
                            <span>{{ $ad->business_name }}</span>
                            <strong>AD ID</strong>
                            <span>{{ $type === 'job' ? 'JOB-' : 'OFF-' }}{{ str_pad($ad->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">📍</div>
                            <div>
                                <div class="card-title">Location</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <strong>City</strong>
                            <span>{{ $ad->city }}</span>
                            <strong>Lat Long</strong>
                            <span>{{ $ad->latitude }}, {{ $ad->longitude }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">📱</div>
                            <div>
                                <div class="card-title">Device Details</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <strong>Device ID</strong>
                            <span>{{ $ad->device_id }}</span>
                            <strong>Phone</strong>
                            <span>{{ $type === 'job' ? $ad->phone_number : $ad->mobile_number }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">💳</div>
                            <div>
                                <div class="card-title">Plan Info</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <strong>Selected Plan</strong>
                            <span>{{ $plan?->plan_title ?? ($ad->plan_id ? 'Plan #' . $ad->plan_id : 'N/A') }}</span>
                            <strong>Ad Duration</strong>
                            <span>{{ $plan?->duration ?? ($ad->plan_id ? 'Plan ID ' . $ad->plan_id : 'N/A') }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">🕒</div>
                            <div>
                                <div class="card-title">Poster Duration</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <strong>Date Posted</strong>
                            <span>{{ $ad->created_at?->format('d F Y, h:i A') ?? 'N/A' }}</span>
                            <strong>Ad Expires By</strong>
                            <span>{{ $ad->expires_at?->format('d F Y, h:i A') ?? 'N/A' }}</span>
                        </div>
                    </div>

                    @if($type === 'job')
                        <div class="card">
                            <div class="card-header">
                                <div class="card-icon">🧾</div>
                                <div>
                                    <div class="card-title">Poster Details</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <strong>Sub Category</strong>
                                <select name="subcategory" form="details-form" onchange="document.getElementById('details-action').value='subcategory'; document.getElementById('details-form').submit()" style="width:100%; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; margin-bottom:16px; font-size:14px;">
                                    <option value="">Select sub category...</option>
                                    @foreach(['Shop/Office/School Staff','Delivery & Logistics','Food, Healthcare & Hospitality','Services, Labor, & Daily Wages'] as $option)
                                        <option value="{{ $option }}" {{ $ad->subcategory === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>

                                <strong>Job Type</strong>
                                <span>{{ $ad->job_type ?? 'N/A' }}</span>
                                <strong>Job Role</strong>
                                <span>{{ $ad->job_role }}</span>
                                <strong>Salary</strong>
                                <span>₹{{ number_format($ad->salary ?? 0) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-header">
                                <div class="card-icon">🧾</div>
                                <div>
                                    <div class="card-title">Poster Details</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <strong>Sub Category</strong>
                                <select name="subcategory" form="details-form" onchange="document.getElementById('details-action').value='subcategory'; document.getElementById('details-form').submit()" style="width:100%; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; margin-bottom:16px; font-size:14px;">
                                    <option value="">Select sub category...</option>
                                    @foreach(['Local Shop Promotion','Jobs in Local Business','Local Service','Home Based Business','Academic/Hobby/Sports Classes','Street Vendors'] as $option)
                                        <option value="{{ $option }}" {{ $ad->subcategory === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <strong>Offer Type</strong>
                                <span>{{ $ad->offer_type ?? 'N/A' }}</span>
                                <strong>Ad Description</strong>
                                <span>{{ $ad->offer_details }}</span>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="card-icon">🖼️</div>
                                <div>
                                    <div class="card-title">Media</div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(is_array($ad->media) && count($ad->media) > 0)
                                    <div class="media-list">
                                        @foreach($ad->media as $mediaItem)
                                            @php
                                                if (is_string($mediaItem)) {
                                                    $mediaUrl = $mediaItem;
                                                    $mediaName = basename($mediaItem);
                                                } elseif (is_array($mediaItem)) {
                                                    $mediaUrl = $mediaItem['url'] ?? $mediaItem['path'] ?? $mediaItem['name'] ?? null;
                                                    $mediaName = basename($mediaUrl ?? ($mediaItem['name'] ?? json_encode($mediaItem)));
                                                } else {
                                                    $mediaUrl = null;
                                                    $mediaName = 'Media attachment';
                                                }
                                            @endphp
                                            <div class="media-item">
                                                @if($mediaUrl)
                                                    <a href="{{ $mediaUrl }}" download class="media-link">{{ $mediaName }}</a>
                                                @else
                                                    <span>{{ $mediaName }}</span>
                                                @endif
                                                <span class="media-action">⬇️</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span>No media attached</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="card status-card">
                        <form id="details-form" action="{{ route('admin.ad.status', ['type' => $type, 'id' => $ad->id]) }}" method="POST">
                            <input type="hidden" name="action" id="details-action" value="">
                            @csrf
                            <div class="card-header">
                                <div class="card-icon">📝</div>
                                <div>
                                    <div class="card-title">Poster Status</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div>Approval</div>
                                    <div class="radio-group">
                                        <label class="radio-field">
                                            <input type="radio" name="status" value="approved" {{ $ad->status === 'approved' ? 'checked' : '' }}>
                                            Approve
                                        </label>
                                        <label class="radio-field">
                                            <input type="radio" name="status" value="rejected" {{ $ad->status === 'rejected' ? 'checked' : '' }}>
                                            Reject
                                        </label>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div>Comment</div>
                                    <div>
                                        <textarea name="comment" placeholder="Message text goes here">{{ $type === 'job' ? $ad->status_comment : $ad->status_note }}</textarea>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="button-primary" onclick="showLoading(event)">Confirm</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing your request...</div>
        </div>
    </div>

    <script>
        function showLoading(event) {
            // Show the loading overlay
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('active');
            
            // Set the action value and submit the form
            document.getElementById('details-action').value = 'status';
            document.getElementById('details-form').submit();
        }
    </script>
</body>
</html>