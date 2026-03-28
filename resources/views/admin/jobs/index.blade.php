<!DOCTYPE html>
<html>
<head>
    <title>Jobs Management</title>

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
    <a href="/admin/ads"><i class="fa fa-home"></i> Dashboard</a>
    <a href="/admin/ads"><i class="fa fa-bullhorn"></i> Ads</a>
    <a href="/admin/jobs"><i class="fa fa-briefcase"></i> Jobs</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOP BAR -->
    <div class="topbar">

        <h3>Jobs Management</h3>

        <!-- SEARCH -->
        <form method="GET" class="d-flex search-box">
            <input type="text" name="title" placeholder="Search Job Title" class="me-2">
            <input type="text" name="company" placeholder="Search Company" class="me-2">
            <button class="btn btn-dark">Search</button>
        </form>

    </div>

    <!-- STATS -->
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="stat-card bg-purple">
                <h4>{{ $total ?? 0 }}</h4>
                <p>Total Jobs</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card bg-green">
                <h4>{{ $active ?? 0 }}</h4>
                <p>Active</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card bg-pink">
                <h4>{{ $expired ?? 0 }}</h4>
                <p>Expired</p>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>City</th>
                    <th>Salary</th>
                    <th>Posted</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            @if(isset($jobs) && $jobs->count() > 0)
            @foreach($jobs as $job)

            <tr>
                <td>
                    <img src="{{ asset('storage/default-logo.png') }}" alt="Company Logo">
                </td>

                <td>{{ $job->business_name ?? 'N/A' }}</td>
                <td>{{ $job->business_name ?? 'N/A' }}</td>
                <td>{{ $job->city ?? 'N/A' }}</td>
                <td>{{ $job->ad_job_salary ? '$'.$job->ad_job_salary : 'Not specified' }}</td>
                <td>{{ $job->ad_creation_timestamp ? $job->ad_creation_timestamp->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $job->ad_expiry_timestamp ? $job->ad_expiry_timestamp->format('M d, Y') : 'N/A' }}</td>

                <td>
                    @if($job->ad_status == 'Approved')
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fa fa-check"></i> Active
                        </span>

                    @elseif($job->ad_status == 'Rejected')
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
                    <a href="/admin/jobs/{{ $job->ad_id }}" class="btn btn-sm btn-purple">
                        View
                    </a>
                </td>
            </tr>

            @endforeach
            @else
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fa fa-briefcase fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No jobs found</p>
                </td>
            </tr>
            @endif

            </tbody>

        </table>

    </div>

</div>

</body>
</html>