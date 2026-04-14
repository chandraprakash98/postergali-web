<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ad Details</title>

<!-- FONT -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- ICONS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

/* GLOBAL TYPOGRAPHY */
body{
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background:#f3f6fb;
    color:#111827;
    font-size:14px;
    line-height:1.5;
    letter-spacing:-0.01em;
}

h1,h2,h3{
    letter-spacing:-0.02em;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    background:#fff;
    border-right:1px solid #e5e7eb;
    padding:25px 18px;
    position:fixed;
}

.logo{
    font-size:22px;
    font-weight:700;
    margin-bottom:35px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 14px;
    border-radius:10px;
    text-decoration:none;
    color:#374151;
    font-size:14px;
    margin-bottom:10px;
    font-weight:500;
}

.menu a.active{
    background:#0f172a;
    color:#fff;
}

.menu a:hover{
    background:#f1f5f9;
}

/* MAIN */
.main{
    margin-left:260px;
    padding:30px 35px;
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.back{
    font-size:14px;
    color:#6b7280;
    text-decoration:none;
}

/* BUTTONS */
.actions{
    display:flex;
    gap:10px;
}

.btn{
    padding:8px 16px;
    border-radius:20px;
    border:none;
    font-size:13px;
    cursor:pointer;
    font-weight:500;
}

.btn-approve{
    background:#f97316;
    color:#fff;
}

.btn-reject{
    background:#e5e7eb;
}

/* HEADER */
.header h1{
    font-size:28px;
    font-weight:600;
}

.header p{
    font-size:14px;
    font-weight:400;
    color:#6b7280;
    margin-bottom:20px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

/* CARD */
.card{
    background:#fff;
    border-radius:14px;
    border:1px solid #e5e7eb;
    padding:20px;
}

.card-title{
    font-size:14px;
    font-weight:600;
    margin-bottom:15px;
    display:flex;
    align-items:center;
    gap:8px;
}

/* TEXT */
.label{
    font-size:12px;
    font-weight:500;
    color:#6b7280;
}

.value{
    font-size:14px;
    font-weight:500;
    margin-bottom:10px;
}

/* STATUS */
.status{
    display:inline-block;
    padding:5px 10px;
    border-radius:6px;
    font-size:12px;
    font-weight:500;
}

.pending{background:#fef3c7;color:#92400e;}
.approved{background:#dcfce7;color:#166534;}
.rejected{background:#fee2e2;color:#991b1b;}

/* IMAGE */
.image-box{
    width:220px;
    height:170px;
    background:#e5e7eb;
    border-radius:16px;
    overflow:hidden;
}

.image-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* FULL WIDTH CARD */
.full{
    grid-column:1 / -1;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">PosterGali</div>

    <div class="menu">
        <a href="/admin/ads"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="#"><i class="fa-solid fa-clock"></i> Pending Ads</a>
        <a href="#"><i class="fa-solid fa-check-circle"></i> Approved Ads</a>
        <a href="#"><i class="fa-solid fa-times-circle"></i> Expired Ads</a>
        <a href="#"><i class="fa-solid fa-dollar-sign"></i> Pricing Info</a>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOP -->
    <div class="topbar">
        <a href="{{ url()->previous() }}" class="back">← Back to Dashboard</a>

        <div class="actions">
            @if($ad->status != 'rejected')
            <form method="POST" action="/admin/ads/{{ $ad->ad_id }}/reject">
                @csrf
                <button class="btn btn-reject">Reject</button>
            </form>
            @endif

            @if($ad->status != 'approved')
            <form method="POST" action="/admin/ads/{{ $ad->ad_id }}/approve">
                @csrf
                <button class="btn btn-approve">Approve</button>
            </form>
            @endif
        </div>
    </div>

    <!-- HEADER -->
    <div class="header">
        <h1>Ad Details</h1>
        <p>Complete information for {{ $ad->ad_id }}</p>
    </div>

    <!-- GRID -->
    <div class="grid">

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-building"></i> Business Information</div>
            <div class="label">Business Name</div>
            <div class="value">{{ $ad->business_name }}</div>

            <div class="label">Ad ID</div>
            <div class="value">{{ $ad->ad_id }}</div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-tag"></i> Category & Pricing</div>
            <div class="label">Category</div>
            <div class="value">{{ $ad->master_category }}</div>

            <div class="label">Sub Category</div>
            <div class="value">{{ $ad->ad_subcategory }}</div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-calendar"></i> Posting Details</div>
            <div class="label">Date Posted</div>
            <div class="value">{{ $ad->created_at->format('F d, Y') }}</div>

            <div class="label">Status</div>
            <div class="value">
                <span class="status {{ $ad->status }}">{{ ucfirst($ad->status) }}</span>
            </div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-clock"></i> Ad Expiry</div>
            <div class="label">Date</div>
            <div class="value">
                {{ $ad->expired_at ? \Carbon\Carbon::parse($ad->expired_at)->format('d F Y, h:i A') : 'Not set' }}
            </div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-location-dot"></i> Location</div>
            <div class="label">Lat Long</div>
            <div class="value">{{ $ad->location }}</div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-phone"></i> Phone Number</div>
            <div class="label">Phone</div>
            <div class="value">{{ $ad->mobile }}</div>
        </div>

        <div class="card full">
            <div>
                <div class="card-title"><i class="fa-solid fa-circle-info"></i> Additional Information</div>

                <div class="label">Sub Category</div>
                <div class="value">{{ $ad->ad_subcategory }}</div>

                <div class="label">Description</div>
                <div class="value">{{ $ad->ad_description }}</div>
            </div>

            <div class="image-box">
                <img src="{{ asset('storage/'.$ad->ad_media) }}">
            </div>
        </div>

    </div>

</div>

</body>
</html>