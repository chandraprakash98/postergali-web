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
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #1f2937;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 50px;
            width: 100%;
            max-width: 420px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lock-icon {
            width: 56px;
            height: 56px;
            background: #fef3c7;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 28px;
            color: #d97706;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #d97706;
            background: white;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 14px;
            top: 44px;
            color: #9ca3af;
            font-size: 16px;
        }

        .input-icon input {
            padding-left: 42px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 44px;
            cursor: pointer;
            color: #9ca3af;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #6b7280;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #d97706;
        }

        .forgot-password {
            color: #d97706;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #b45309;
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background: #d97706;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 16px;
        }

        .login-button:hover {
            background: #b45309;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .demo-credentials {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .error-list {
            margin: 0;
            padding-left: 20px;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px;
            }

            .login-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">
    <!-- Lock Icon -->
    <div class="lock-icon">
        <i class="fa fa-lock"></i>
    </div>

    <!-- Title -->
    <h1 class="login-title">Admin Login</h1>
    <p class="login-subtitle">Enter your credentials to access the admin panel</p>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="error-message">
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input 
                    type="email" 
                    name="email" 
                    class="form-input"
                    placeholder="admin@example.com"
                    value="{{ old('email') }}"
                    required
                >
            </div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-icon" style="position: relative;">
                <i class="fa fa-lock"></i>
                <input 
                    type="password" 
                    name="password" 
                    class="form-input"
                    id="password"
                    placeholder="Enter your password"
                    required
                >
                <span class="password-toggle" onclick="togglePassword()">
                    <i class="fa fa-eye" id="passwordIcon"></i>
                </span>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="form-footer">
            <label class="remember-me">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>
            <a href="#" class="forgot-password">Forgot password?</a>
        </div>

        <!-- Login Button -->
        <button type="submit" class="login-button">Sign In</button>

        <!-- Demo Credentials -->
        <div class="demo-credentials">
            <strong>Demo credentials:</strong> admin@example.com / admin123
        </div>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }
</script>

</body>

</html>
