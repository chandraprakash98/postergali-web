<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hyperlocal Ads App</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Poppins', sans-serif; }

/* floating animation */
@keyframes float {
  0% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0); }
}
.float { animation: float 4s ease-in-out infinite; }
</style>
</head>

<body class="bg-gradient-to-br from-purple-200 via-blue-100 to-purple-100 m-0">

<!-- MAIN WRAPPER -->
<div class="min-h-screen flex flex-col justify-between">

<!-- HERO -->
<section class="w-full px-6 md:px-12 lg:px-20 py-12">
<div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">

<!-- LEFT CONTENT -->
<div class="space-y-8">

<!-- HEADING -->
<div>
<div class="mb-4">
<p class="text-sm font-semibold text-purple-600 tracking-wide">postergali.com</p>
</div>

<h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 leading-tight">
Sell. Hire. Promote.<br>
<span class="text-purple-600">All Around You</span>
</h1>

<p class="text-gray-600 text-base sm:text-lg mt-4 max-w-lg">
Hyperlocal platform to grow your business, hire staff, and reach real customers near you — instantly.
</p>
</div>

<!-- FEATURE CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

<div class="bg-white/70 backdrop-blur p-4 rounded-xl shadow">
<p class="font-semibold text-gray-800">📍 Smart Location</p>
<p class="text-sm text-gray-600">Auto detect users nearby</p>
</div>

<div class="bg-white/70 backdrop-blur p-4 rounded-xl shadow">
<p class="font-semibold text-gray-800">⚡ Instant Ads</p>
<p class="text-sm text-gray-600">Post within seconds</p>
</div>

<div class="bg-white/70 backdrop-blur p-4 rounded-xl shadow">
<p class="font-semibold text-gray-800">💰 Low Cost</p>
<p class="text-sm text-gray-600">Start from just ₹20</p>
</div>

<div class="bg-white/70 backdrop-blur p-4 rounded-xl shadow">
<p class="font-semibold text-gray-800">📊 High Reach</p>
<p class="text-sm text-gray-600">Target real nearby users</p>
</div>

</div>

<!-- DOWNLOAD + QR COMBINED -->
<div class="flex flex-col sm:flex-row sm:items-center gap-6">

<div class="space-y-3">
<p class="font-semibold text-gray-700">Available on</p>

<div class="flex gap-3">
<div class="bg-black text-white px-5 py-3 rounded-xl text-sm shadow">▶ Android</div>
<div class="bg-gray-800 text-white px-5 py-3 rounded-xl text-sm shadow"> iOS</div>
</div>
</div>

<div class="flex items-center gap-3">
<div class="bg-white p-2 rounded-xl shadow">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://play.google.com/store/apps" class="w-24 h-24" />
</div>
<div class="text-sm text-gray-700 max-w-[120px]">
Scan & install instantly
</div>
</div>

</div>

<!-- LOCATION -->
<div class="text-gray-700 font-medium">
📍 Delhi NCR
</div>

</div>

<!-- RIGHT SCREENS -->
<div class="flex flex-wrap justify-center gap-6">

<!-- PHONE -->
<div class="bg-white w-56 sm:w-60 rounded-[30px] shadow-xl p-4 float">
<div class="text-xs text-gray-400">Welcome</div>
<div class="font-semibold mb-2">User 👋</div>
<div class="bg-purple-500 text-white p-3 rounded-xl mb-3 text-sm">📍 Delhi NCR</div>
<div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
<div class="bg-gray-100 p-2 rounded text-center">Jobs</div>
<div class="bg-gray-100 p-2 rounded text-center">Shops</div>
<div class="bg-gray-100 p-2 rounded text-center">Services</div>
<div class="bg-purple-600 text-white p-2 rounded text-center">Post</div>
</div>
</div>

<!-- PHONE -->
<div class="bg-white w-56 sm:w-60 rounded-[30px] shadow-xl p-4 float" style="animation-delay:1s">
<div class="font-semibold mb-2">Feed</div>
<div class="space-y-2 text-xs sm:text-sm">
<div class="bg-green-100 p-2 rounded">Hiring ₹15K</div>
<div class="bg-blue-100 p-2 rounded">50% Sale</div>
<div class="bg-yellow-100 p-2 rounded">Repair</div>
</div>
</div>

<!-- PHONE -->
<div class="bg-white w-56 sm:w-60 rounded-[30px] shadow-xl p-4 float" style="animation-delay:2s">
<div class="font-semibold mb-2">Post Ad</div>
<div class="space-y-2 text-xs sm:text-sm">
<div class="bg-gray-100 p-2 rounded">Name</div>
<div class="bg-gray-100 p-2 rounded">Details</div>
<div class="bg-gray-100 p-2 rounded">Upload</div>
<button class="bg-purple-600 text-white w-full py-2 rounded mt-2">Next</button>
</div>
</div>

</div>

</div>
</section>

<!-- FOOTER -->
<footer class="text-center text-gray-500 text-xs sm:text-sm pb-4 space-y-1">
<div class="text-lg font-semibold text-purple-600">postergali.com</div>
Made by <span class="font-semibold text-purple-600">Madeincode</span>
</footer>

</div>

</body>
</html>
