<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>PosterGali Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    background:#f3f6fb;
    display:flex;
    color:#111827;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    background:#ffffff;
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
    transition:0.2s;
}

.menu a i{
    font-size:15px;
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
    width:100%;
}

/* HEADER */
.header h1{
    font-size:28px;
    font-weight:600;
    margin-bottom:6px;
}

.header p{
    font-size:14px;
    color:#6b7280;
    margin-bottom:25px;
}

/* CARDS */
.cards{
    display:flex;
    gap:20px;
    margin-bottom:30px;
}

.card{
    flex:1;
    background:#ffffff;
    padding:16px;
    border-radius:14px;
    border:1px solid #e5e7eb;
    display:flex;
    justify-content:space-between;
    align-items:center;
    transition:0.2s;
}

.card:hover{
    box-shadow:0 4px 20px rgba(0,0,0,0.05);
}

.card h3{
    font-size:13px;
    color:#6b7280;
    margin-bottom:3px;
}

.value{
    font-size:30px;
    font-weight:700;
}

.icon{
    width:42px;
    height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:16px;
}

.yellow{background:#f59e0b;}
.green{background:#22c55e;}
.red{background:#ef4444;}

/* TABLE */
.table-container{
    background:#fff;
    border-radius:14px;
    border:1px solid #e5e7eb;
    padding:16px;
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.table-header h2{
    font-size:18px;
    font-weight:600;
}

.search-box{
    display:flex;
    gap:10px;
}

.search-box input{
    padding:8px 12px;
    border-radius:8px;
    border:1px solid #e5e7eb;
    font-size:13px;
}

.search-box button{
    background:#0f172a;
    color:#fff;
    border:none;
    padding:8px 16px;
    border-radius:8px;
    font-size:13px;
    cursor:pointer;
}

/* TABLE STYLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    font-size:12px;
    color:#6b7280;
    padding:10px 12px;
    border-bottom:1px solid #e5e7eb;
    font-weight:500;
}

td{
    padding:10px 12px;
    font-size:13px;
    border-bottom:1px solid #f1f5f9;
}

tr:hover{
    background:#f9fafb;
}

/* STATUS */
.status{
    padding:5px 10px;
    border-radius:6px;
    font-size:12px;
    font-weight:500;
}

.approved{
    background:#dcfce7;
    color:#166534;
}

.rejected{
    background:#fee2e2;
    color:#991b1b;
}

.pending{
    background:#fef3c7;
    color:#92400e;
}

/* IMAGE */
.ad-img{
    width:45px;
    height:45px;
    border-radius:8px;
    object-fit:cover;
}

/* BUTTON */
.btn{
    background:#0f172a;
    color:#fff;
    padding:6px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:12px;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.btn i{
    font-size:12px;
}
</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">PosterGali</div>

    <div class="menu">
        <a href="/admin/ads" class="active">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>

        <a href="/admin/ads">
            <i class="fa-solid fa-clock"></i> Pending Ads
        </a>

        <a href="/admin/approved">
            <i class="fa-solid fa-check-circle"></i> Approved Ads
        </a>

        <a href="/admin/expired">
            <i class="fa-solid fa-times-circle"></i> Expired Ads
        </a>

        <a href="#">
            <i class="fa-solid fa-dollar-sign"></i> Pricing Info
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Dashboard</h1>
        <p>Overview of all ad listings</p>
    </div>

    <!-- CARDS -->
    <div class="cards">

        <div class="card">
            <div>
                <h3>Pending Approvals</h3>
                <div class="value">{{ $pending ?? 0 }}</div>
            </div>
            <div class="icon yellow">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="card">
            <div>
                <h3>Live Ads</h3>
                <div class="value">{{ $approved ?? 0 }}</div>
            </div>
            <div class="icon green">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>

        <div class="card">
            <div>
                <h3>Expired Ads</h3>
                <div class="value">{{ $expired ?? 0 }}</div>
            </div>
            <div class="icon red">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="table-container">

        <div class="table-header">
            <h2>All Ads</h2>

            <form method="GET" class="search-box">
                <input type="text" name="city" placeholder="City">
                <input type="text" name="range" placeholder="Range">
                <button>Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>AD ID</th>
                    <th>Business Name</th>
                    <th>Category</th>
                    <th>Date Posted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            @foreach($ads as $ad)

            <tr>

                <td>{{ $ad->ad_id }}</td>

                <td>{{ $ad->business_name }}</td>
                <td>{{ $ad->master_category }}</td>
                <td>{{ $ad->created_at->format('M d, Y') }}</td>

                <td>
                    @if($ad->status=='approved')
                        <span class="status approved">Approved</span>
                    @elseif($ad->status=='rejected')
                        <span class="status rejected">Rejected</span>
                    @else
                        <span class="status pending">Pending</span>
                    @endif
                </td>

                <td>
                    <a href="/admin/ads/{{ $ad->ad_id }}" class="btn">
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                </td>

            </tr>

            @endforeach

            </tbody>
        </table>

    </div>

</div>

</body>
</html>