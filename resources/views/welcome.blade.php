<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>App Promotion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body { background: #f4f7fb; }

        .navbar {
            display: flex;
            justify-content: space-between;
            padding: 20px 60px;
            position: absolute;
            width: 100%;
        }

        .logo { font-weight: 700; color: #5cc400; font-size: 18px; }

        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80vh;
            padding: 0 60px;
            background: linear-gradient(135deg, #5cc400, #9be300);
            color: white;
        }

        .left { max-width: 500px; }

        .left h1 { font-size: 48px; margin-bottom: 20px; }

        .left p { margin-bottom: 30px; }

        .btn {
            padding: 12px 25px;
            background: white;
            color: #5cc400;
            border-radius: 30px;
            border: none;
            cursor: pointer;
        }

        .phone {
            width: 260px;
            height: 500px;
            background: black;
            border-radius: 35px;
            padding: 12px;
        }

        .screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #5cc400, #9be300);
            border-radius: 25px;
            padding: 25px;
            text-align: center;
        }

        .screen input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
        }

        .screen button {
            width: 100%;
            padding: 12px;
            background: white;
            color: #5cc400;
            border: none;
            border-radius: 8px;
        }

        .features {
            padding: 80px 60px;
            text-align: center;
        }

        .feature-grid {
            display: flex;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            flex: 1;
        }

        .cta {
            background: linear-gradient(135deg, #5cc400, #9be300);
            padding: 60px;
            text-align: center;
            color: white;
        }

    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">MyApp</div>
    <div class="nav-links">
        <a href="#">Home</a>
        <a href="#">Features</a>
        <a href="#">Pricing</a>
    </div>
</div>

<div class="hero">
    <div class="left">
        <h1>Promote Your Business 🚀</h1>
        <p>Post ads, jobs and offers easily with our powerful app.</p>
        <button class="btn">Get Started with app</button>
    </div>

    <div class="right">
        <div class="phone">
            <div class="screen">
                <h3>Login</h3>
                <input placeholder="Username">
                <input placeholder="Password">
                <button>Login</button>
            </div>
        </div>
    </div>
</div>

<div class="features">
    <h2>Features</h2>
    <div class="feature-grid">
        <div class="card">📢 Easy Ads</div>
        <div class="card">💼 Job Posting</div>
        <div class="card">📍 Local Reach</div>
    </div>
</div>

<div class="cta">
    <h2>Start Today</h2>
    <button class="btn">Download App</button>
</div>

</body>
</html>