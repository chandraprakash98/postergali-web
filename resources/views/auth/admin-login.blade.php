<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - PosterGali</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top, #fff7ed, #f3f4f6);
        padding: 20px;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        padding: 42px;
    }

    .icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 18px;
        border-radius: 14px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    h1 {
        text-align: center;
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    p.subtitle {
        text-align: center;
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 26px;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 12px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #374151;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    input {
        width: 100%;
        padding: 12px 14px 12px 38px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        font-size: 14px;
    }

    input:focus {
        outline: none;
        border-color: #f59e0b;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.12);
    }

    .toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #9ca3af;
    }

    /* ✅ FIXED ALIGNMENT SECTION */
    .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 10px 0 18px;
        font-size: 13px;
    }

    .remember {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        cursor: pointer;
    }

    .remember input {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: #d97706;
    }

    .forgot {
        color: #d97706;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .forgot:hover {
        text-decoration: underline;
    }

    .btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(217, 119, 6, 0.25);
    }

    .demo {
        margin-top: 16px;
        font-size: 12px;
        text-align: center;
        color: #6b7280;
        background: #f3f4f6;
        padding: 10px;
        border-radius: 10px;
    }
</style>
</head>

<body>

<div class="login-card">

    <div class="icon">
        <i class="fa fa-lock"></i>
    </div>

    <h1>Admin Login</h1>
    <p class="subtitle">Access your dashboard securely</p>

    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrapper">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="input-wrapper">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" id="password" required>
                <span class="toggle" onclick="togglePassword()">
                    <i class="fa fa-eye" id="icon"></i>
                </span>
            </div>
        </div>

        <!-- ✅ FIXED ALIGNMENT -->
        <div class="row">
            <label class="remember">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            <a href="#" class="forgot">Forgot password?</a>
        </div>

        <button class="btn" type="submit">Sign In</button>

        <div class="demo">
            Demo: <b>admin@example.com</b> / <b>admin123</b>
        </div>
    </form>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.getElementById("icon");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>