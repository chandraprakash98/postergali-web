<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICONS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        /* TABLE PROFESSIONAL LOOK */
.table thead th {
    font-weight: 600;
    font-size: 14px;
    color: #333;
}

.table tbody td {
    font-weight: 400;
    font-size: 13px;
    color: #666;
}

/* Improve spacing */
.table td, .table th {
    vertical-align: middle;
    padding: 12px 10px;
}

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fb;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: white;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }

        .sidebar h3 {
            font-weight: 600;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            margin-bottom: 10px;
            color: #555;
            border-radius: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #f1f1f1;
        }

        /* MAIN */
        .main {
            margin-left: 270px;
            padding: 20px;
        }

        /* TOP BAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-box input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        /* CARDS */
        .stat-card {
            color: white;
            border-radius: 20px;
            padding: 20px;
        }

        .bg-purple {
            background: linear-gradient(45deg,#6a11cb,#2575fc);
        }

        .bg-pink {
            background: linear-gradient(45deg,#ff416c,#ff4b2b);
        }

        .bg-green {
            background: linear-gradient(45deg,#00b09b,#96c93d);
        }

        /* TABLE */
        .table-box {
            background: white;
            padding: 20px;
            border-radius: 20px;
        }

        .table img {
            width: 55px;
            height: 55px;
            border-radius: 10px;
        }

        .btn-purple {
            background: #6a11cb;
            color: white;
        }
    </style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3><i class="fa fa-cube"></i> Admin</h3>
    <hr>
    <a href="#"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#"><i class="fa fa-bullhorn"></i> Ads</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOP BAR -->
    <div class="topbar">

        <h3>Dashboard</h3>

        <!-- SEARCH -->
        <form method="GET" class="d-flex search-box">
            <input type="text" name="city" placeholder="Search City" class="me-2">
            <input type="text" name="range" placeholder="Search Range" class="me-2">
            <button class="btn btn-dark">Search</button>
        </form>

    </div>

    <!-- STATS -->
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="stat-card bg-purple">
                <h4>{{ $total }}</h4>
                <p>Total Ads</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card bg-green">
                <h4>{{ $approved }}</h4>
                <p>Approved</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card bg-pink">
                <h4>{{ $rejected }}</h4>
                <p>Rejected</p>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Image</th>
                    <th>Business</th>
                    <th>Mobile</th>
                    <th>City</th>
                    <th>Location</th>
                    <th>Created</th>
                     <th>Range</th>
               
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            @foreach($ads as $ad)

            @php
                $coords = explode(',', $ad->location);
                $lat = $coords[0] ?? '';
                $lng = $coords[1] ?? '';
                $mapLink = "https://www.google.com/maps?q=$lat,$lng";
            @endphp

            <tr>
                <td>
                    <img src="{{ asset('storage/'.$ad->ad_media) }}">
                </td>

                <td>{{ $ad->business_name }}</td>
                <td>{{ $ad->mobile }}</td>
                <td>{{ $ad->city }}</td>
                <!-- LOCATION CONVERTED -->
                <td>
                    <a href="{{ $mapLink }}" target="_blank">View Map</a>
                </td>
                <td>{{ $ad->created_at }}</td>
                <td>{{ $ad->ad_range }}</td>
              
                <td>{{ $ad->expired_at }}</td>

                <td>
                    @if($ad->status == 'approved')
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fa fa-check"></i> Approved
                        </span>

                    @elseif($ad->status == 'rejected')
                        <span class="badge bg-danger rounded-pill px-3 py-2">
                            <i class="fa fa-times"></i> Rejected
                        </span>

                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                            <i class="fa fa-clock"></i> Pending
                        </span>
                    @endif
                </td>

                <td>
                    <a href="/admin/ads/{{ $ad->ad_id }}" class="btn btn-sm btn-purple">
                        View
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