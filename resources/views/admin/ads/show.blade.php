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
    margin-bottom:15px;
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
    margin-bottom:3px;
}

.header p{
    font-size:14px;
    font-weight:400;
    color:#6b7280;
    margin-bottom:15px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}

/* CARD */
.card{
    background:#fff;
    border-radius:14px;
    border:1px solid #e5e7eb;
    padding:16px;
}

.card-title{
    font-size:14px;
    font-weight:600;
    margin-bottom:12px;
    display:flex;
    align-items:center;
    gap:8px;
}

/* TEXT */
.label{
    font-size:12px;
    font-weight:500;
    color:#6b7280;
    margin-bottom:4px;
}

.value{
    font-size:14px;
    font-weight:500;
    margin-bottom:8px;
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

/* EDIT MODE */
.edit-btn{
    background:none;
    border:none;
    cursor:pointer;
    font-size:16px;
    color:#6b7280;
    padding:5px 10px;
}

.edit-btn:hover{
    color:#111827;
}

.value input, .value textarea{
    width:100%;
    padding:8px 12px;
    border:1px solid #e5e7eb;
    border-radius:8px;
    font-size:14px;
    font-family:inherit;
    margin-top:5px;
}

.value textarea{
    resize:vertical;
    min-height:80px;
}

.save-btn{
    background:#0f172a;
    color:#fff;
    padding:8px 16px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    font-size:13px;
    margin-top:10px;
}

.save-btn:hover{
    background:#1e293b;
}

/* EDIT SECTION */
.edit-section{
    display:none;
    margin-top:15px;
    padding-top:15px;
    border-top:1px solid #e5e7eb;
}

.edit-section.active{
    display:flex;
    gap:10px;
}

.edit-buttons{
    display:flex;
    gap:10px;
}

.btn-save{
    background:#0f172a;
    color:#fff;
    padding:8px 16px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
    transition:all 0.2s;
}

.btn-save:hover{
    background:#1e293b;
    transform:translateY(-1px);
}

.btn-cancel{
    background:#e5e7eb;
    color:#374151;
    padding:8px 16px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
    transition:all 0.2s;
}

.btn-cancel:hover{
    background:#d1d5db;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:1000;
    align-items:center;
    justify-content:center;
}

.modal.show{
    display:flex;
}

.modal-content{
    background:#fff;
    border-radius:14px;
    padding:30px;
    max-width:400px;
    width:90%;
    box-shadow:0 20px 60px rgba(0,0,0,0.3);
}

.modal-title{
    font-size:18px;
    font-weight:600;
    margin-bottom:15px;
}

.modal-text{
    font-size:14px;
    color:#6b7280;
    margin-bottom:20px;
}

.modal-input{
    width:100%;
    padding:10px 12px;
    border:1px solid #e5e7eb;
    border-radius:8px;
    font-size:14px;
    margin-bottom:20px;
    min-height:80px;
    font-family:inherit;
    resize:vertical;
}

.modal-actions{
    display:flex;
    gap:10px;
    justify-content:flex-end;
}

.modal-btn{
    padding:8px 16px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    font-size:13px;
    font-weight:500;
}

.modal-btn-cancel{
    background:#e5e7eb;
    color:#374151;
}

.modal-btn-submit{
    background:#ef4444;
    color:#fff;
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
            <button onclick="openRejectModal()" class="btn btn-reject">Reject</button>
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
            <div class="card-title"><i class="fa-solid fa-building"></i> Business Information <button type="button" onclick="startEdit(this)" class="edit-btn"><i class="fa-solid fa-pen"></i></button></div>
            <div class="label">Business Name</div>
            <div class="value">
                <span class="display-value">{{ $ad->business_name }}</span>
                <input type="text" class="edit-value" value="{{ $ad->business_name }}" style="display:none;" data-field="business_name">
            </div>

            <div class="label">Ad ID</div>
            <div class="value">{{ $ad->ad_id }}</div>

            <div class="edit-section">
                <div class="edit-buttons">
                    <button type="button" class="btn-save" onclick="saveAllChanges()">Update</button>
                    <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancel</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-tag"></i> Category & Pricing <button type="button" onclick="startEdit(this)" class="edit-btn"><i class="fa-solid fa-pen"></i></button></div>
            <div class="label">Category</div>
            <div class="value">
                <span class="display-value">{{ $ad->master_category }}</span>
                <input type="text" class="edit-value" value="{{ $ad->master_category }}" style="display:none;" data-field="master_category">
            </div>

            <div class="label">Sub Category</div>
            <div class="value">
                <span class="display-value">{{ $ad->ad_subcategory }}</span>
                <input type="text" class="edit-value" value="{{ $ad->ad_subcategory }}" style="display:none;" data-field="ad_subcategory">
            </div>

            <div class="edit-section">
                <div class="edit-buttons">
                    <button type="button" class="btn-save" onclick="saveAllChanges()">Update</button>
                    <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancel</button>
                </div>
            </div>
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
            
            <div class="label">Full Address</div>
            <div class="value">
                @if($address)
                    <span class="display-value">{{ $address }}</span>
                @else
                    <span class="display-value" style="color: #9ca3af; font-style: italic;">Loading address...</span>
                @endif
            </div>

            <div class="label">Coordinates (Lat, Long)</div>
            <div class="value">
                <span class="display-value">{{ $ad->location }}</span>
                <input type="text" class="edit-value" value="{{ $ad->location }}" style="display:none;" data-field="location">
            </div>

            <div class="edit-section">
                <div class="edit-buttons">
                    <button type="button" class="btn-save" onclick="saveAllChanges()">Update</button>
                    <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancel</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title"><i class="fa-solid fa-phone"></i> Phone Number <button type="button" onclick="startEdit(this)" class="edit-btn"><i class="fa-solid fa-pen"></i></button></div>
            <div class="label">Phone</div>
            <div class="value">
                <span class="display-value">{{ $ad->mobile }}</span>
                <input type="text" class="edit-value" value="{{ $ad->mobile }}" style="display:none;" data-field="mobile">
            </div>

            <div class="edit-section">
                <div class="edit-buttons">
                    <button type="button" class="btn-save" onclick="saveAllChanges()">Update</button>
                    <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancel</button>
                </div>
            </div>
        </div>

        <!-- SAME CODE ABOVE (UNCHANGED) -->

<div class="card full">
    <div>
        <div class="card-title">
            <i class="fa-solid fa-circle-info"></i> Additional Information 
            <button type="button" onclick="startEdit(this)" class="edit-btn">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>

        <div class="label">Description</div>
        <div class="value">
            <span class="display-value">{{ $ad->ad_description }}</span>
            <textarea class="edit-value" style="display:none;" data-field="ad_description">
                {{ $ad->ad_description }}
            </textarea>
        </div>

        <div class="edit-section">
            <div class="edit-buttons">
                <button type="button" class="btn-save" onclick="saveAllChanges()">Update</button>
                <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- ✅ UPDATED MULTIPLE MEDIA SUPPORT -->
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        @php
            // Support both array (API) and comma-separated (DB)
            $mediaFiles = is_array($ad->ad_media) ? $ad->ad_media : explode(',', $ad->ad_media);
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];
        @endphp

        @foreach($mediaFiles as $media)
            @php
                // If DB path → convert to URL
                $mediaUrl = str_contains($media, 'http') ? $media : asset('storage/'.$media);

                $extension = pathinfo($mediaUrl, PATHINFO_EXTENSION);
                $isVideo = in_array(strtolower($extension), $videoExtensions);
            @endphp

            <div class="image-box">
                @if($isVideo)
                    <video width="100%" height="100%" controls style="object-fit: cover;">
                        <source src="{{ $mediaUrl }}" 
                                type="video/{{ strtolower($extension) == 'mov' ? 'quicktime' : strtolower($extension) }}">
                    </video>
                @else
                    <img src="{{ $mediaUrl }}">
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- SAME CODE BELOW (UNCHANGED) -->

    </div>

</div>

<!-- REJECT MODAL -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-title">Reject Ad</div>
        <p class="modal-text">Please provide a reason for rejecting this ad:</p>
        
        <textarea id="rejectReason" class="modal-input" placeholder="Enter rejection reason..."></textarea>
        
        <div class="modal-actions">
            <button class="modal-btn modal-btn-cancel" onclick="closeRejectModal()">Cancel</button>
            <button class="modal-btn modal-btn-submit" onclick="submitReject()">Reject</button>
        </div>
    </div>
</div>

<script>
    let currentEditCard = null;

    function startEdit(btn){
        // Close any previously open edit sections
        const allEditSections = document.querySelectorAll('.edit-section');
        allEditSections.forEach(section => {
            section.classList.remove('active');
            const card = section.closest('.card');
            const displayValues = card.querySelectorAll('.display-value');
            const editValues = card.querySelectorAll('.edit-value');
            displayValues.forEach(display => display.style.display = 'block');
            editValues.forEach(input => input.style.display = 'none');
        });

        // Open new edit section
        const card = btn.closest('.card');
        const displayValues = card.querySelectorAll('.display-value');
        const editValues = card.querySelectorAll('.edit-value');
        const editSection = card.querySelector('.edit-section');

        displayValues.forEach(display => display.style.display = 'none');
        editValues.forEach(input => input.style.display = 'block');
        if(editSection) editSection.classList.add('active');

        currentEditCard = card;
    }

    function cancelEdit(){
        if(currentEditCard){
            const displayValues = currentEditCard.querySelectorAll('.display-value');
            const editValues = currentEditCard.querySelectorAll('.edit-value');
            const editSection = currentEditCard.querySelector('.edit-section');

            // Revert values
            editValues.forEach(input => {
                const display = currentEditCard.querySelector(`[data-field="${input.dataset.field}"]`).closest('.value').querySelector('.display-value');
                input.value = display.textContent;
            });

            displayValues.forEach(display => display.style.display = 'block');
            editValues.forEach(input => input.style.display = 'none');
            if(editSection) editSection.classList.remove('active');

            currentEditCard = null;
        }
    }

    function saveAllChanges(){
        if(!currentEditCard) return;

        const editValues = currentEditCard.querySelectorAll('.edit-value');
        let hasChanges = false;
        let saveCount = 0;
        let completeCount = 0;

        editValues.forEach(input => {
            const field = input.dataset.field;
            const value = input.value.trim();
            const display = currentEditCard.querySelector(`[data-field="${field}"]`).closest('.value').querySelector('.display-value');

            if(display.textContent !== value){
                hasChanges = true;
                saveCount++;

                fetch('/admin/ads/{{ $ad->ad_id }}/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        field: field,
                        value: value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    completeCount++;
                    if(data.success){
                        display.textContent = value;

                        // If location field was updated, reverse geocode to get address
                        if(field === 'location'){
                            const coords = value.split(',');
                            if(coords.length === 2){
                                reverseGeocode(coords[0].trim(), coords[1].trim());
                            }
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }

                    // If all saves complete, exit edit mode
                    if(completeCount === saveCount){
                        closeEditMode();
                    }
                })
                .catch(error => {
                    completeCount++;
                    console.error('Error:', error);
                    alert('Error saving changes');
                    if(completeCount === saveCount){
                        closeEditMode();
                    }
                });
            }
        });

        if(!hasChanges){
            closeEditMode();
        }
    }

    async function reverseGeocode(lat, lon){
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
            const data = await response.json();
            
            if(data.address){
                const addressParts = [];
                const address = data.address;
                
                if(address.house_number) addressParts.push(address.house_number);
                if(address.road) addressParts.push(address.road);
                if(address.suburb) addressParts.push(address.suburb);
                if(address.city) addressParts.push(address.city);
                else if(address.town) addressParts.push(address.town);
                if(address.state) addressParts.push(address.state);
                if(address.postcode) addressParts.push(address.postcode);
                if(address.country) addressParts.push(address.country);
                
                const fullAddress = addressParts.filter(Boolean).join(', ');
                
                // Find and update the address display
                const locationCard = currentEditCard.querySelector('[data-field="location"]')?.closest('.card');
                if(locationCard){
                    const addressDisplay = locationCard.querySelector('.label:nth-of-type(1)').nextElementSibling.querySelector('.display-value');
                    if(addressDisplay){
                        addressDisplay.textContent = fullAddress;
                        addressDisplay.style.color = 'inherit';
                    }
                }
            }
        } catch(error){
            console.error('Geocode error:', error);
        }
    }

    function closeEditMode(){
        if(currentEditCard){
            const displayValues = currentEditCard.querySelectorAll('.display-value');
            const editValues = currentEditCard.querySelectorAll('.edit-value');
            const editSection = currentEditCard.querySelector('.edit-section');

            displayValues.forEach(display => display.style.display = 'block');
            editValues.forEach(input => input.style.display = 'none');
            if(editSection) editSection.classList.remove('active');

            currentEditCard = null;
        }
    }

    function openRejectModal(){
        document.getElementById('rejectModal').classList.add('show');
    }

    function closeRejectModal(){
        document.getElementById('rejectModal').classList.remove('show');
        document.getElementById('rejectReason').value = '';
    }

    function submitReject(){
        const reason = document.getElementById('rejectReason').value.trim();
        if(!reason){
            alert('Please enter a rejection reason');
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/ads/{{ $ad->ad_id }}/reject';
        
        form.innerHTML = `
            @csrf
            <input type="hidden" name="rejection_reason" value="${reason}">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    // Close modal when clicking outside
    document.getElementById('rejectModal').addEventListener('click', function(e){
        if(e.target === this){
            closeRejectModal();
        }
    });
</script>

</body>
</html>