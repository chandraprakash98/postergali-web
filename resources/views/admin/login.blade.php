<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Postergali</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 50px 40px;
            text-align: center;
        }

        .lock-icon {
            width: 80px;
            height: 80px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 40px;
        }

        h1 {
            font-size: 32px;
            color: #1f2937;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            color: #1f2937;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            appearance: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #9ca3af;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"] {
            padding-left: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            font-size: 18px;
            padding: 4px;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
        }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #667eea;
            margin-right: 6px;
        }

        label[for="remember"] {
            margin: 0;
            cursor: pointer;
            color: #374151;
        }

        a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #764ba2;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .demo-credentials {
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            margin-top: 20px;
        }

        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .success-message {
            background-color: #dcfce7;
            color: #16a34a;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #16a34a;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="lock-icon">🔒</div>
        
        <h1>Admin Login</h1>
        <p class="subtitle">Enter your credentials to access the admin panel</p>

        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="email" id="email" name="email" placeholder="admin@example.com" 
                           value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                </div>
            </div>

            <div class="remember-forgot">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember me</label>
                </div>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login">Sign In</button>

            <div class="demo-credentials">
                Demo credentials: admin@example.com / admin123
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const button = document.querySelector('.toggle-password');
            
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '👁️‍🗨️';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
