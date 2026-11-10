<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $influencer ? 'Edit Influencer' : 'Add Influencer' }} | Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4efe6; color: #302b27; font-family: Arial, sans-serif; }
        .form-wrap { max-width: 640px; margin: 60px auto; padding: 28px; background: #fffdf8; border-radius: 18px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h2 { margin: 0 0 20px; font-size: 28px; }
        .form-grid { display: grid; gap: 18px; }
        .field label { display: block; margin-bottom: 8px; font-weight: 700; }
        .field input, .field select { width: 100%; padding: 12px 14px; border: 1px solid #d7d7d7; border-radius: 10px; font-size: 15px; background: #fff; }
        .actions { display: flex; gap: 12px; margin-top: 8px; }
        .button { display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 10px; padding: 12px 18px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .primary { background: #9b342b; color: #fff; }
        .secondary { background: #f2f2f2; color: #111; }
        .errors { margin-bottom: 16px; padding: 10px 12px; border: 1px solid #f7c2c2; border-radius: 8px; background: #fff1f1; color: #b91c1c; }
        @media (max-width: 700px) { .form-wrap { margin: 20px; padding: 22px; } }
    </style>
</head>
<body>
    <main class="form-wrap">
        <h2>{{ $influencer ? 'Edit Influencer' : 'Add Influencer' }}</h2>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $influencer ? route('admin.influencers.update', $influencer) : route('admin.influencers.store') }}">
            @csrf
            @if($influencer)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label for="influencer_name">Influencer Name</label>
                    <input id="influencer_name" name="influencer_name" type="text" maxlength="255" value="{{ old('influencer_name', $influencer?->influencer_name ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="influencer_bonus_coupon_id">Promo Code</label>
                    <input id="influencer_bonus_coupon_id" name="influencer_bonus_coupon_id" type="text" maxlength="255" value="{{ old('influencer_bonus_coupon_id', $influencer?->influencer_bonus_coupon_id ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="bonus_coupon_poster_credit">Poster Credits</label>
                    <input id="bonus_coupon_poster_credit" name="bonus_coupon_poster_credit" type="number" min="0" step="0.01" value="{{ old('bonus_coupon_poster_credit', $influencer?->bonus_coupon_poster_credit ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="bonus_coupon_status">Status</label>
                    <select id="bonus_coupon_status" name="bonus_coupon_status" required>
                        <option value="active" @selected(old('bonus_coupon_status', $influencer?->bonus_coupon_status ?? 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('bonus_coupon_status', $influencer?->bonus_coupon_status ?? 'active') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="actions">
                    <button type="submit" class="button primary">{{ $influencer ? 'Update Influencer' : 'Create Influencer' }}</button>
                    <a href="{{ route('admin.influencers') }}" class="button secondary">Cancel</a>
                </div>
            </div>
        </form>
    </main>
</body>
</html>
