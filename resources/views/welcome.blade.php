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



</html>