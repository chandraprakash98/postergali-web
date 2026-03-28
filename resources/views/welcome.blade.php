<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Postergali</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Poppins', sans-serif; }

/* Background overlay */
.hero-bg {
  background-image: linear-gradient(rgba(139,92,246,0.85), rgba(59,130,246,0.85)),
  url('https://plus.unsplash.com/premium_photo-1682310158823-917a4f726802?q=80&w=912&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
  background-size: cover;
  background-position: center;
}

/* floating animation */
@keyframes float {
  0% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0); }
}
.float { animation: float 4s ease-in-out infinite; }

.glass {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(10px);
}

/* SCROLLING DISCLAIMER */
.ticker {
  position: fixed;
  top: 0;
  width: 100%;
  background: #000;
  color: #fff;
  overflow: hidden;
  z-index: 9999;
  height: 30px;
  display: flex;
  align-items: center;
}

.ticker p {
  white-space: nowrap;
  display: inline-block;
  padding-left: 100%;
  animation: scrollText 15s linear infinite;
  font-size: 12px;
}

@keyframes scrollText {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
</style>
</head>

<body class="m-0">

<!-- TOP MOVING DISCLAIMER -->
<div class="ticker">
  <p>🚧 This website is currently under development. Some features may not work properly. Stay tuned for updates 🚀 🚧 This website is currently under development. Some features may not work properly. Stay tuned for updates 🚀</p>
</div>

<!-- WORK IN PROGRESS RIBBON -->
<div style="
position: fixed;
top: 40px;
right: -60px;
background: #facc15;
color: #000;
padding: 10px 80px;
font-size: 12px;
font-weight: 600;
transform: rotate(45deg);
z-index: 9999;
box-shadow: 0 4px 10px rgba(0,0,0,0.2);
letter-spacing: 1px;
">
🚧 WORK IN PROGRESS
</div>

<div class="min-h-screen flex flex-col hero-bg">

<section class="flex-1 flex items-center px-4 sm:px-6 md:px-12 py-10">
<div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-10 items-center w-full">

<!-- LEFT -->
<div class="space-y-6 text-center lg:text-left text-white">

<p class="text-xs font-semibold tracking-wide opacity-90">
postergali.com
</p>

<h1 class="text-3xl sm:text-4xl md:text-5xl font-bold leading-tight">
Post Ads Like Flyers.<br>
<span class="text-yellow-300">Reach People Nearby.</span>
</h1>

<p class="text-sm sm:text-base max-w-md mx-auto lg:mx-0 opacity-90">
Turn your walls into digital billboards. Post jobs, promotions & services and reach real people around you instantly.
</p>

<!-- FEATURES -->
<div class="grid grid-cols-2 gap-3 text-xs sm:text-sm">
<div class="glass p-3 rounded-xl shadow text-gray-800">📍 Smart Location</div>
<div class="glass p-3 rounded-xl shadow text-gray-800">⚡ Instant Ads</div>
<div class="glass p-3 rounded-xl shadow text-gray-800">💰 ₹20 Start</div>
<div class="glass p-3 rounded-xl shadow text-gray-800">📊 High Reach</div>
</div>

<!-- CTA -->
<div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">

<div>
<p class="text-xs font-semibold mb-1">Available on</p>
<div class="flex gap-2 justify-center sm:justify-start">
<div class="bg-black text-white px-4 py-2 rounded-lg text-xs">Android</div>
<div class="bg-gray-900 text-white px-4 py-2 rounded-lg text-xs">iOS</div>
</div>
</div>

<div class="bg-white p-2 rounded-lg shadow">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://play.google.com/store/apps" class="w-20 h-20">
</div>

</div>

<p class="text-sm opacity-90">📍 Delhi NCR</p>

</div>

<!-- RIGHT -->
<div class="flex justify-center gap-4 flex-wrap">

<div class="glass w-40 sm:w-44 rounded-[25px] shadow-xl p-3 float">
<div class="text-xs text-gray-500">Welcome</div>
<div class="font-semibold text-xs sm:text-sm mb-2">User 👋</div>
<div class="bg-purple-500 text-white p-2 rounded-lg text-xs mb-2">📍 Delhi</div>
<div class="grid grid-cols-2 gap-2 text-[10px]">
<div class="bg-gray-100 p-2 rounded text-center">Jobs</div>
<div class="bg-gray-100 p-2 rounded text-center">Shops</div>
<div class="bg-gray-100 p-2 rounded text-center">Services</div>
<div class="bg-purple-600 text-white p-2 rounded text-center">Post</div>
</div>
</div>

<div class="glass w-40 sm:w-44 rounded-[25px] shadow-xl p-3 float" style="animation-delay:1s">
<div class="font-semibold text-xs sm:text-sm mb-2">Feed</div>
<div class="space-y-2 text-[10px]">
<div class="bg-green-100 p-2 rounded">Hiring ₹15K</div>
<div class="bg-blue-100 p-2 rounded">50% Sale</div>
<div class="bg-yellow-100 p-2 rounded">Repair</div>
</div>
</div>

<div class="glass w-40 sm:w-44 rounded-[25px] shadow-xl p-3 float" style="animation-delay:2s">
<div class="font-semibold text-xs sm:text-sm mb-2">Post Ad</div>
<div class="space-y-2 text-[10px]">
<div class="bg-gray-100 p-2 rounded">Name</div>
<div class="bg-gray-100 p-2 rounded">Details</div>
<div class="bg-gray-100 p-2 rounded">Upload</div>
<button class="bg-purple-600 text-white w-full py-1 rounded text-xs">Next</button>
</div>
</div>

</div>

</div>
</section>

<div class="text-center text-white text-xs pb-4 opacity-80">
Made by <span class="font-semibold">Madeincode</span>
</div>

</div>

</body>
</html>