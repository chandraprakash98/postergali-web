<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fb;
        }

        .container-box {
            max-width: 1050px;
            margin: auto;
        }

        /* HEADER */
        .header-bar {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        /* CARD */
        .card-custom {
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
            border: none;
        }

        /* IMAGE */
        .media-box {
            text-align: center;
        }

        .media-box img {
            width: 180px;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .media-box img:hover {
            transform: scale(1.05);
        }

        /* FIELD */
        .field-box {
            margin-bottom: 12px;
        }

        .label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
        }

        .value-input {
            border: none;
            background: transparent;
            font-size: 14px;
            width: 100%;
            outline: none;
        }

        .value-input[readonly] {
            color: #333;
        }

        .editable {
            background: #fff !important;
            border-bottom: 1px solid #ddd !important;
        }

        /* STATUS */
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
        }

        .approved { background: #1cc88a; color: #fff; }
        .rejected { background: #e74a3b; color: #fff; }
        .pending { background: #f6c23e; color: #000; }

        /* BUTTONS */
        .btn-custom {
            border-radius: 8px;
            height: 40px;
        }

    </style>
</head>

<body>

<div class="container mt-4 container-box">

    <!-- HEADER -->
    <div class="header-bar d-flex justify-content-between align-items-center">
        <h5 class="m-0">Job Details</h5>
        <div>
            <button onclick="enableEdit()" class="btn btn-light btn-sm">✏️ Edit</button>
            <a href="{{ url()->previous() }}" class="btn btn-dark btn-sm">← Back</a>
        </div>
    </div>

    <form method="POST" action="/admin/jobs/{{ $job->ad_id }}/update">
        @csrf

        <div class="row">

            <!-- IMAGE -->
            <div class="col-md-3">
                <div class="card-custom p-3 media-box">
                    <img src="{{ asset('storage/default-logo.png') }}" onclick="openImageModal(this.src)">
                    <small class="text-muted d-block mt-2">Click to view</small>
                </div>
            </div>

            <!-- DETAILS -->
            <div class="col-md-9">
                <div class="card-custom p-4">

                    <div class="row">

                        <div class="col-md-4 field-box">
                            <div class="label">Job Title</div>
                            <input type="text" name="business_name" value="{{ $job->business_name }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Company</div>
                            <input type="text" name="business_name" value="{{ $job->business_name }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Phone</div>
                            <input type="text" name="phone_number" value="{{ $job->phone_number }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Job Type</div>
                            <input type="text" name="ad_job_type" value="{{ $job->ad_job_type }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Salary</div>
                            <input type="text" name="ad_job_salary" value="{{ $job->ad_job_salary ? '$'.$job->ad_job_salary : 'Not specified' }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">City</div>
                            <input type="text" name="city" value="{{ $job->city }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Category</div>
                            <input type="text" name="master_category" value="{{ $job->master_category }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Subcategory</div>
                            <input type="text" name="ad_subcategory" value="{{ $job->ad_subcategory }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Coverage Area</div>
                            <input type="text" name="ad_area_coverage" value="{{ $job->ad_area_coverage }}" class="value-input editable-field" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Posted</div>
                            <input type="text" value="{{ $job->ad_creation_timestamp ? \Carbon\Carbon::parse($job->ad_creation_timestamp)->format('d M Y, h:i A') : 'N/A' }}" class="value-input" readonly>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Expires</div>
                            <input type="text" value="{{ $job->ad_expiry_timestamp ? \Carbon\Carbon::parse($job->ad_expiry_timestamp)->format('d M Y, h:i A') : 'Not set' }}" class="value-input" readonly>
                        </div>

                        <div class="col-md-8 field-box">
                            <div class="label">Description</div>
                            <textarea name="ad_description" class="value-input editable-field" readonly>{{ $job->ad_description }}</textarea>
                        </div>

                        <div class="col-md-4 field-box">
                            <div class="label">Status</div>
                            @if($job->ad_status == 'Approved')
                                <span class="badge-status approved">Approved</span>
                            @elseif($job->ad_status == 'Rejected')
                                <span class="badge-status rejected">Rejected</span>
                            @else
                                <span class="badge-status pending">Pending</span>
                            @endif
                        </div>

                    </div>

                    <button id="updateBtn" class="btn btn-warning btn-custom w-100 mt-3" disabled>
                        Update Changes
                    </button>

                </div>
            </div>

        </div>

    </form>

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card-custom p-3">
                <h6>Actions</h6>
                @if($job->ad_status != 'Approved')
                <form method="POST" action="/admin/jobs/{{ $job->ad_id }}/approve" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success btn-custom w-100">✓ Approve (30 days)</button>
                </form>
                @endif

                @if($job->ad_status != 'Rejected')
                <button type="button" class="btn btn-danger btn-custom w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">✕ Reject</button>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- REJECT MODAL -->
<div class="modal fade" id="rejectModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Reject Job</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="/admin/jobs/{{ $job->ad_id }}/reject">
            @csrf
            <div class="modal-body">
                <label for="reasonInput" class="form-label">Reason for rejection:</label>
                <textarea id="reasonInput" name="reason" class="form-control" placeholder="Enter rejection reason..." rows="4" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Submit Rejection</button>
            </div>
        </form>
    </div>
</div>

<!-- IMAGE MODAL -->
<div class="modal fade" id="imageModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-body text-center">
            <img id="modalImg" style="width:100%;">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function enableEdit() {
    document.querySelectorAll('.editable-field').forEach(el => {
        el.removeAttribute('readonly');
        el.classList.add('editable');
    });
    document.getElementById('updateBtn').disabled = false;
}

function openImageModal(src) {
    document.getElementById('modalImg').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

</body>
</html>