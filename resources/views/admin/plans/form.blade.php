<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $plan ? 'Edit Plan' : 'Create Plan' }} | Admin</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { background: #FAF8E9; font-family: Arial, sans-serif; }
        .plan-form-wrap {
            max-width: 640px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border: 1px solid #eee;
            padding: 28px;
        }
        .plan-form-wrap h2 {
            margin-bottom: 20px;
            color: #111;
            font-size: 28px;
        }
        .form-grid { display: grid; gap: 18px; }
        .field label { display:block; font-weight:700; margin-bottom:8px; color:#333; }
        .field input {
            width:100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #d7d7d7;
            font-size: 15px;
        }
        .actions { display:flex; gap:12px; margin-top:8px; }
        .btn-primary, .btn-secondary {
            display:inline-flex; align-items:center; justify-content:center;
            border: none; border-radius: 10px; padding: 12px 18px; font-weight:700; cursor:pointer; text-decoration:none;
        }
        .btn-primary { background: #C4351D; color:#fff; }
        .btn-secondary { background: #f2f2f2; color:#111; }
        .errors { background:#fff1f1; border:1px solid #f7c2c2; color:#b91c1c; padding:10px 12px; border-radius:8px; margin-bottom:16px; }
    </style>
</head>
<body>
    <div class="plan-form-wrap">
        <h2>{{ $plan ? 'Edit Plan' : 'Create Plan' }}</h2>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $plan ? route('admin.plans.update', $plan->id) : route('admin.plans.store') }}">
            @csrf
            @if($plan)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label for="plan_title">Plan Title</label>
                    <input id="plan_title" name="plan_title" type="text" value="{{ old('plan_title', $plan?->plan_title ?? '') }}" placeholder="e.g. Starter" required>
                </div>

                <div class="field">
                    <label for="duration">Duration</label>
                    <input id="duration" name="duration" type="text" value="{{ old('duration', $plan?->duration ?? '') }}" placeholder="e.g. 7 Days" required>
                </div>

                <div class="field">
                    <label for="price">Price</label>
                    <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $plan?->price ?? '') }}" placeholder="e.g. 199" required>
                </div>

                <div class="actions">
                    <button type="submit" class="btn-primary">{{ $plan ? 'Update Plan' : 'Create Plan' }}</button>
                    <a href="{{ route('admin.pricingInfo') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
