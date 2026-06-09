<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hero Section</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Inter',sans-serif;
    background:#f5f5f5;
    overflow-x:hidden;
}

/* ================= NAVBAR ================= */

.navbar{
    background:#FCFAE1;
    max-width:1400px;
    height:75px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 20px;
}

.logo{
    font-size:22px;
    font-weight:800;
}

.nav-links{
    display:flex;
    gap:30px;
}

.nav-links a{
    text-decoration:none;
    color:#111;
    font-size:14px;
}

.btn{
    background:#111;
    color:#fff;
    border:none;
    padding:10px 18px;
    border-radius:999px;
}

/* ================= HERO ================= */

.hero{
    text-align:center;
    background:#FFFCE5;
}

.tag{
    display:inline-block;
    background:#E88F2A;
    padding:8px 14px;
    margin-top:10px;
    margin-bottom:15px;
    font-size:13px;
    color:black;
    font-weight:500;
    transform:rotate(-2deg);
}

.hero h1{
    font-size:42px;
    line-height:1;
    font-weight:900;
    letter-spacing:0.5px;
    color:#F7952A;

    text-shadow:
        -1px -1px 0 #000,
         1px -1px 0 #000,
        -1px  1px 0 #000,
         1px  1px 0 #000;
}
.hero p{
    font-size:13px;
    max-width:650px;
    margin:15px auto 0;
    color:#444;
}

/* ================= PHONE SECTION ================= */

.phone-section{
    position:relative;
    height:380px;
    max-width:1200px;
    margin:90px auto 0;
}


.poster{
     transform:rotate(-2deg);
    position: absolute;
    background-image: url('/images/imag1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 2px;
}

.posterm{
     transform:rotate(-4deg);
    position: absolute;
    background-image: url('/images/image2.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 2px;
}

.postern{
     transform:rotate(-8deg);
    position: absolute;
    background-image: url('/images/image3.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 1px;
}
.postero{
     transform:rotate(-2deg);
    position: absolute;
    background-image: url('/images/image1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 2px;
}


.poster1{
   width:150px;
    height:240px;
    left:180px;
    top:130px;
    opacity:.85;
}

.poster0{
   width:150px;
    height:240px;
    left:5px;
    top:5px;
    opacity:.85;
}

.poster2{
    width:150px;
    height:240px;
    left:350px;
    top:20px;
    z-index:2;
}

.posterlast{
    width:150px;
    height:240px;
    right:5px;
 
    top:130px;
    opacity:.85;
}
.poster3{
    width:150px;
    height:240px;
    right:350px;
    top:70px;
    z-index:2;
}

.poster4{
    width:150px;
    height:240px;
    right:175px;
    top:0px;
    opacity:.85;
}

.phone{
    position:absolute;
    left:50%;
    bottom:20px;
    transform:translateX(-50%);
    width:220px;
    height:420px;
    background:#fff;
    border-radius:32px;
    z-index:10;
    box-shadow:0 10px 20px rgba(0,0,0,.08),
               0 20px 50px rgba(0,0,0,.12);
}

.phone-top{
    height:32px;
    background:#08111d;
    border-radius:32px 32px 0 0;
    position:relative;
}

.notch{
    width:95px;
    height:22px;
    background:#fff;
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    border-radius:0 0 15px 15px;
}

.screen{
    height:calc(100% - 32px);
    background:#fff;
}

/* ================= FEATURE STRIP ================= */

.feature-strip{
    height:65px;
    background:#C4351D;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:45px;
    font-size:13px;
    font-weight:700;
    color:#fff;
    border-top:1px solid #ececec;
    border-bottom:1px solid #ececec;
}

/* ================= APP SECTION ================= */

.app-section{
    background:#F7AF25;
    padding:80px 6%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:60px;
}

/* TEXT */
.app-text{
    flex:1;
}

.app-badge{
    display:inline-block;
    background:red;
    padding:10px 18px;
    transform:rotate(-2deg);
    font-size:13px;
    color:#fff;
    margin-bottom:20px;
}

.app-text h2{
    font-size:45px;
    font-weight:900;
    line-height:1.05;
    margin-bottom:20px;
}

.app-text p{
    font-size:18px;
    color:#333;
    max-width:500px;
}

/* ================= MOCK CARDS ================= */

.app-preview{
    flex:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:25px;

    perspective:1000px;
}

.mock-row{
    display:flex;
    justify-content:center;
    gap:25px;
}

.mock{
    width:200px;
    height:250px;
    background:#fff;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.12);
    transition:0.3s ease;
}

/* LEFT tilt */
.mock-left{
    transform: rotateX(12deg) rotateZ(-8deg);
}

/* RIGHT tilt */
.mock-right{
    transform: rotateX(12deg) rotateZ(8deg);
}

/* hover */
.mock:hover{
    transform: rotateX(0deg) rotateZ(0deg) scale(1.05);
}

/* ================= STORE BUTTONS (BELOW MOCKS) ================= */

.store{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    justify-content:center;
}

.store a{
    display:flex;
    align-items:center;
    gap:12px;
    background:#fff;
    padding:14px 18px;
    border-radius:14px;
    text-decoration:none;
    color:#000;
    min-width:240px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}

.store small{
    display:block;
    font-size:11px;
}

.store strong{
    font-size:16px;
}

/* ================= MOBILE ================= */

@media(max-width:900px){

.hero h1{font-size:38px;}

.phone{
    width:190px;
    height:360px;
}

.poster1,.poster4{display:none;}

.poster2{
    width:130px;
    height:130px;
    left:15px;
    top:110px;
}

.poster3{
    width:130px;
    height:130px;
    right:15px;
    top:140px;
}

.nav-links{display:none;}

.feature-strip{
    flex-wrap:wrap;
    gap:15px;
    height:auto;
    padding:15px;
}

.app-section{
    flex-direction:column;
    text-align:center;
}

.app-text h2{
    font-size:36px;
}

.app-text p{
    margin:auto;
}

.mock{
    width:160px;
    height:300px;
}

}

/* =======================================================
   WHY POSTERGALI CARDS
======================================================= */

.why-postergali{
    max-width:1050px;
    margin:50px auto;
    padding:0 10px;
}

.why-head{
    text-align:center;
    margin-bottom:40px;
}

.why-head small{
    display:block;
    font-size:13px;
    font-weight:700;
    margin-bottom:8px;
}

.why-head h2{
    font-size:42px;
    line-height:1;
    font-weight:900;
    letter-spacing:-1.5px;
}



.pg-cardm{
    background:#F0B13A;
    border-radius:14px;
	height:260px; /* fixed equal height */
    min-height:230px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:28px 35px;

    margin-bottom:26px;

    position:relative;

    overflow:hidden;

    transition:.25s;
}





.pg-cardn{
    background:red;
    border-radius:14px;
	height:260px; /* fixed equal height */
    min-height:230px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:28px 35px;

    margin-bottom:26px;

    position:relative;

    overflow:hidden;

    transition:.25s;
}

.pg-cardo{
    background:#F0B13A;
    border-radius:14px;
	height:260px; /* fixed equal height */
    min-height:230px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:28px 35px;

    margin-bottom:26px;

    position:relative;

    overflow:hidden;

    transition:.25s;
}


.pg-cardp{
    background:red;
    border-radius:14px;
	height:260px; /* fixed equal height */
    min-height:230px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:28px 35px;

    margin-bottom:26px;

    position:relative;

    overflow:hidden;

    transition:.25s;
}


.pg-card:hover{
   
}

.tilt-right{
    transform:rotate(-1.11deg);
}

.tilt-left{
    transform:rotate(1.11deg);
}

.pg-content{
    max-width:420px;
    z-index:2;
}

.pg-content h3{
    font-size:36px;
    line-height:.95;
    font-weight:900;
    color:#111;
    margin-bottom:16px;
}

.pg-content p{
    font-size:12px;
    line-height:1.5;
    color:#333;
}

/* ===== Posters ===== */

.pg-posters{
    position:relative;
    width:520px;
    height:180px;
}

.poster-img{
    position:absolute;
    width:120px;
    height:140px;
    object-fit:cover;
    border-radius:2px;
    box-shadow:0 15px 30px rgba(0,0,0,.18);
}

.poster-img1{
    left:0;
    top: 5px;;
    transform:rotate(-8deg);
}

.poster-img2{
    left:145px;
    top:0;
    transform:rotate(6deg);
}

.poster-img3{
    left:290px;
    top:6px;
    transform:rotate(-4deg);
}

.mini-poster{
    position:absolute;
    width:120px;
    height:180px;

    color:#fff;
    font-size:28px;
    font-weight:900;

    display:flex;
    justify-content:center;
    align-items:center;

    box-shadow:0 15px 30px rgba(0,0,0,.18);
}

.red{
    background:#CB4E3D;
    left:10px;
    top:10px;
    transform:rotate(-8deg);
}

.yellow{
    background:#ffbf00;
    left:95px;
    top:0;
    transform:rotate(6deg);
    color:#111;
}

.blue{
    background:#2962ff;
    right:0;
    top:12px;
    transform:rotate(-4deg);
}

/* ===== Phone frame ===== */

.phone-frame{
    width:180px;
    height:220px;

    background:#fff;

    border-radius:26px;

    position:relative;

    margin-right:25px;

    box-shadow:0 20px 40px rgba(0,0,0,.12);
}

.phone-frame::before{
    content:'';

    position:absolute;

    width:70px;
    height:16px;

    background:#111;

    top:10px;
    left:50%;

    transform:translateX(-50%);

    border-radius:20px;
}

/* ===== Hand phone image ===== */

.pg-phone-wrap{
    width:280px;
    display:flex;
    justify-content:center;
}

.hand-phone{
    width:250px;
    object-fit:contain;
}

/* ===== Mobile ===== */

@media(max-width:900px){

    .why-head h2{
        font-size:28px;
    }

    .pg-cardm,pg-cardn,pg-cardo,pg-cardp{
        flex-direction:column;
        text-align:left;
        min-height:auto;
        gap:25px;
    }

    .pg-content{
        max-width:100%;
    }

    .pg-content h3{
        font-size:34px;
    }

    .pg-posters{
        width:100%;
        height:180px;
    }

    .phone-frame{
        width:140px;
        height:180px;
        margin-right:0;
    }

    .hand-phone{
        width:180px;
    }
}


/* ==========================================
   HOW IT WORKS + FAQ SECTION
========================================== */

.how-faq{
    background:#FFFCE5;
    padding:30px 20px 80px;
}

.how-wrap{
    max-width:1100px;
    margin:auto;
}

/* Heading */

.how-header{
    text-align:center;
    margin-bottom:50px;
}

.how-header small{
    display:block;
    font-size:14px;
    font-weight:700;
    margin-bottom:10px;
}

.how-header h2{
    font-size:44px;
    line-height:1;
    font-weight:900;
    margin-bottom:12px;
}

.how-header p{
    font-size:13px;
    color:#555;
}

/* Process Layout */

.process{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:0;
    margin-bottom:90px;
    position:relative;
}

.process-column{
    display:flex;
    flex-direction:column;
    gap:18px;
    position:relative;
    z-index:1;
}

.process-column:first-child{
    margin-right:-90px;
}

.process-center{
    position:relative;
    z-index:2;
    margin:0 10px;
}

.process-column:last-child{
    margin-left:-50px;
}

.process-column:first-child .process-card:first-child{
    margin-top:4px;
     margin-left:42px;
}

.process-column:first-child .process-cardm{
    margin-top:4px;
    margin-left:12px;
}

.process-column:first-child .process-card:last-child{
    margin-top:6px;
}

.process-column:last-child .process-cardm{
    margin-top:12px;
}

.process-column:last-child .process-card:last-child{
    margin-top:42px;
}

.process-card,
.process-cardm{
    position:relative;
    z-index:1;
}

.process-card{
    width:240px;
    background: #a4dcb3;
    border-radius:14px;
    padding:18px;
}

.process-cardm{
    width:240px;
    background: #eccd6a;
    border-radius:14px;
    padding:18px;
}



.process-card h4{
    font-size:18px;
    font-weight:800;
    margin-bottom:8px;
}

.process-card p{
    font-size:12px;
    line-height:1.5;
    color:#444;
}

/* Phone */

.process-phone{
    width:230px;
    height:300px;
    background:#fff;
    border-radius:35px;
    position:relative;
    box-shadow:0 15px 40px rgba(0,0,0,.12);
}

.process-phone::before{
    content:'';
    position:absolute;
    width:95px;
    height:20px;
    background:#111;
    border-radius:20px;
    left:50%;
    top:12px;
    transform:translateX(-50%);
}

/* FAQ Header */

.faq-top{
    text-align:center;
    margin-bottom:40px;
}

.faq-top small{
    display:block;
    font-weight:700;
    margin-bottom:10px;
}

.faq-top h2{
    font-size:42px;
    font-weight:900;
    margin-bottom:12px;
}

.faq-top p{
    font-size:13px;
    line-height:1.6;
    color:#444;
}

.talk-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    margin-top:18px;
    background:#9c9c9c;
    color:#fff;
    text-decoration:none;
    padding:12px 30px;
    border-radius:999px;
    font-weight:700;
}

/* FAQ */

.faq-list{
    max-width:1000px;
    margin:auto;
}

.faq-item{
    border-bottom:1px solid #bfbfbf;
}

.faq-item summary{
    list-style:none;
    cursor:pointer;
    padding:18px 0;
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:14px;
    font-weight:600;
}

.faq-item summary::-webkit-details-marker{
    display:none;
}

.faq-item summary::after{
    content:"+";
    font-size:18px;
}

.faq-item[open] summary::after{
    content:"−";
}

.faq-content{
    padding-bottom:18px;
    color:#555;
    font-size:13px;
    line-height:1.6;
}

@media(max-width:900px){

    .process{
        flex-direction:column;
    }

    .process-card{
        width:100%;
        max-width:320px;
    }

    .process-phone{
        width:200px;
        height:270px;
    }

    .how-header h2,
    .faq-top h2{
        font-size:30px;
    }
}



/* ==========================================
   CONTACT + FOOTER
========================================== */

.contact-footer{
    background:white;
    margin-top:80px;
}

.contact-section{
    max-width:1150px;
    margin:auto;
    padding:80px 20px 60px;
}

.contact-heading{
    text-align:center;
    margin-bottom:45px;
}

.contact-heading small{
    display:block;
    font-size:14px;
    font-weight:700;
    margin-bottom:10px;
}

.contact-heading h2{
    font-size:52px;
    line-height:1.05;
    font-weight:900;
}

.contact-box{
    background:#bcbcbc;
    border-radius:16px;
    padding:30px;
    display:flex;
    gap:40px;
}

.contact-left{
    width:320px;
}

.contact-left h3{
    font-size:30px;
    font-weight:900;
    margin-bottom:10px;
}

.contact-left p{
    color:#333;
    font-size:14px;
    line-height:1.6;
    margin-bottom:30px;
}

.contact-info{
    display:flex;
    align-items:center;
    gap:15px;
    background:#cfcfcf;
    border-radius:12px;
    padding:14px;
    margin-bottom:15px;
}

.contact-icon{
    width:42px;
    height:42px;
    border-radius:50%;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.contact-info span{
    font-size:14px;
    line-height:1.6;
}

.contact-form{
    flex:1;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group label{
    font-size:12px;
    font-weight:700;
    margin-bottom:8px;
}

.form-group input,
.form-group textarea{
    border:none;
    background:#e7e7e7;
    border-radius:10px;
    padding:14px;
    font-family:inherit;
}

.full{
    grid-column:1/-1;
}

.form-group textarea{
    height:120px;
    resize:none;
}

/* FOOTER */

.pg-footer{
    border-top:1px solid rgba(0,0,0,.08);
    padding:60px 20px 25px;
}

.footer-main{
    max-width:1150px;
    margin:auto;
    display:flex;
    justify-content:space-between;
    gap:40px;
}

.footer-left{
    max-width:320px;
}

.footer-logo{
    font-size:42px;
    font-weight:900;
    line-height:.9;
    margin-bottom:20px;
}

.footer-left p{
    color:#333;
    line-height:1.7;
    margin-bottom:25px;
}

.social-title{
    font-weight:800;
    margin-bottom:15px;
}

.socials{
    display:flex;
    gap:12px;
}

.socials a{
    width:42px;
    height:42px;
    border-radius:50%;
    border:2px solid #111;
    text-decoration:none;
    color:#111;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
}

.footer-right{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:25px;
}

.footer-posters{
    display:flex;
    gap:20px;
}

.footer-poster{
    width:140px;
    height:180px;
    background:#efefef;
    border-radius:4px;
}

.store-buttons{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.store-btn{
    background:#fff;
    padding:14px 20px;
    border-radius:12px;
    text-decoration:none;
    color:#111;
    font-weight:700;
}

.footer-bottom{
    max-width:1150px;
    margin:40px auto 0;
    padding-top:20px;
    border-top:1px solid rgba(0,0,0,.08);
    display:flex;
    justify-content:space-between;
    font-size:12px;
}

.footer-links{
    display:flex;
    gap:25px;
}

.footer-links a{
    text-decoration:none;
    color:#111;
}

@media(max-width:900px){

    .contact-heading h2{
        font-size:34px;
    }

    .contact-box{
        flex-direction:column;
    }

    .contact-left{
        width:100%;
    }

    .form-grid{
        grid-template-columns:1fr;
    }

    .footer-main{
        flex-direction:column;
        text-align:center;
    }

    .footer-right{
        align-items:center;
    }

    .footer-bottom{
        flex-direction:column;
        gap:15px;
        text-align:center;
    }
}

</style>
</head>

<body style="background: #FFFCE5;">

<nav class="navbar">
    <div class="logo">POSTERGALI</div>

    <div class="nav-links">
        <a href="#">Home</a>
        <a href="#">Features</a>
        <a href="#">FAQ</a>
        <a href="#">Contact</a>
    </div>

    <button class="btn">Download App</button>
</nav>

<!-- HERO -->
<section class="hero">

    <div class="tag">India ka Poster Bazaar</div>

    <h1>Your Street, your posters<br>now on your phone</h1>

    <p>
        Whether it's hiring, a promotion, or any event — with PosterGali, <br>your message reaches straight to the walls of your city
    </p>

    <div class="phone-section">
        <div class="posterm poster0"></div>
        <div class="poster poster1"></div>
        <div class="postern poster2"></div>
        <div class="posterm posterlast"></div>

        <div class="phone">
            <div class="phone-top">
                <div class="notch"></div>
            </div>
            <div class="screen"></div>
        </div>
        
         <div class="postero poster3"></div>
         <div class="postern poster4"></div>
        <div class="poster poster5"></div>
        <div class="poster poster6"></div>
    </div>
</section>

<!-- FEATURE STRIP -->
<div class="feature-strip">
    <div>From ₹19 ONLY</div>
    <div>LOCAL REACH</div>
    <div>INSTANT LIVE</div>
    <div>SMART TARGETING</div>
    <div>VERIFIED</div>
</div>

<!-- APP SECTION -->
<section class="app-section">

    <div class="app-text">
        <div class="app-badge">DOWNLOAD NOW — KEEP IT HANDY!</div>

        <h2>
            Your very own Poster Bazaar Available on Android & iOS
        </h2>

        <p>
            Get it free today, design your first poster in minutes and
            watch your business grow like never before!
        </p>
    </div>

    <div class="app-preview">

        <!-- MOCKS -->
        <div class="mock-row">
            <div class="mock mock-left"></div>
            <div class="mock mock-right"></div>
        </div>

        <!-- STORE BUTTONS BELOW -->
        <div class="store">

            <a href="#">
                <div>▶</div>
                <div>
                    <small>GET IT ON</small>
                    <strong>Google Play</strong>
                </div>
            </a>

            <a href="#">
                <div></div>
                <div>
                    <small>DOWNLOAD ON THE</small>
                    <strong>App Store</strong>
                </div>
            </a>

        </div>

    </div>

</section>

<!-- ================= WHY POSTERGALI CARDS ================= -->

<section class="why-postergali">

    <div class="why-head">
        <small>What is PosterGali?</small>
        <h2>Your neighbourhood street<br>gone digital</h2>
    </div>

    <!-- CARD 1 -->
    <div class="pg-cardm tilt-right">
        <div class="pg-content">
            <h3>Hyperlocal<br>Digital<br>Noticeboard</h3>

            <p>
                India’s hyperlocal poster platform<br>
Reach people who live, work, and shop nearby <br>
No agency fees, no middlemen <br>
            </p>
        </div>
    <div class="pg-posters">
        <img src="/images/image1.png" class="mini-poster poster-img poster-img1" alt="Poster">
        <img src="/images/image2.png" class="mini-poster poster-img poster-img2" alt="Poster">
        <img src="/images/image3.png" class="mini-poster poster-img poster-img3" alt="Poster">
    </div>
    </div>

    <!-- CARD 2 -->
    <div class="pg-cardn tilt-left">

        <div class="pg-content">
            <h3>Instant<br>Community<br>Connection</h3>

            <p>
                Promote offers and events.<br>
                Reach nearby customers.<br>
                Engage your neighbourhood.
            </p>
        </div>

        <div class="pg-phone-wrap">

            <img
             src="/images/phonehand.png"
              alt=""
              class="hand-phone">
        </div>

    </div>

    <!-- CARD 3 -->
    <div class="pg-cardo tilt-right">

        <div class="pg-content">
            <h3>Smart<br>Targeting</h3>

            <p>
                Choose specific areas and PIN codes.<br>
                Show only where it matters.
            </p>
        </div>

        <div class="phone-frame"></div>

    </div>

    <!-- CARD 4 -->
    <div class="pg-cardp tilt-left">

        <div class="pg-content">
            <h3>Easy &<br>Affordable</h3>

            <p>
                Go live under ₹19.<br>
                No flex boards or printing costs.
            </p>
        </div>

        <div class="pg-phone-wrap">
            <img
              src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=500"
              alt=""
              class="hand-phone">
        </div>

    </div>

    <!-- CARD 5 -->
    <div class="pg-cardp tilt-right">

        <div class="pg-content">
            <h3>Trust &<br>Verification</h3>

            <p>
                Every poster verified.<br>
                Safe visibility for local users.
            </p>
        </div>

        <div class="phone-frame"></div>

    </div>

</section>

<!-- ==========================================
     HOW IT WORKS + FAQ
========================================== -->

<section class="how-faq">

    <div class="how-wrap">

        <div class="how-header">
            <small>How it works?</small>
            <h2>Getting Started With<br>PosterGali</h2>
            <p>Follow These Simple Steps To Publish Your Poster In Minutes</p>
        </div>

        <div class="process">

            <div class="process-column">

                <div class="process-card">
                    <h4>Define your goal</h4>
                    <p>
                        Choose whether you want to hire talent
                        or promote an offer.
                    </p>
                </div>

                <div class="process-cardm">
                    <h4>Set your reach</h4>
                    <p>
                        Select coverage from 1 km to 25 km,
                        or expand city-wide.
                    </p>
                </div>

                <div class="process-card">
                    <h4>Publish instantly</h4>
                    <p>
                        Go live in your neighbourhood
                        within minutes.
                    </p>
                </div>

            </div>

            <div class="process-center">
                <img src="/images/whypost.png" alt="" style="width: 270px;; height: 270px; object-fit:cover;">
            </div>

            <div class="process-column">

                <div class="process-cardm">
                    <h4>Build your poster</h4>
                    <p>
                       Build your poster
Answer quick questions business name, role, salary, or offer details — and let the chatbot create your poster.
                    </p>
                </div>

                <div class="process-card">
                    <h4>Verify & preview</h4>
                    <p>
                       Complete a quick OTP check, review your poster, and confirm the details.
                    </p>
                </div>

            </div>

        </div>

        <div class="faq-top">
            <small>Got questions?</small>

            <h2>We've Got Answers</h2>

            <p>
                Can't Find What You're Looking For?
                Drop Us A WhatsApp Or Email.<br>
                We Reply Within 24 Hours On Working Days.
            </p>

            <a href="#" class="talk-btn">
                Talk to us →
            </a>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary>How much does it cost to post on PosterGali?</summary>
                <div class="faq-content">
                    Plans start from ₹19 depending on reach and visibility.
                </div>
            </details>

            <details class="faq-item">
                <summary>Do I need any design skills to make a poster?</summary>
                <div class="faq-content">
                    No. PosterGali automatically generates posters using AI.
                </div>
            </details>

            <details class="faq-item">
                <summary>How quickly my poster goes live after I post it?</summary>
                <div class="faq-content">
                    Most posters become visible within minutes after approval.
                </div>
            </details>

            <details class="faq-item">
                <summary>Can I edit my poster when it's been published?</summary>
                <div class="faq-content">
                    Yes, you can update and republish your poster anytime.
                </div>
            </details>

            <details class="faq-item">
                <summary>What kind of posters can I post?</summary>
                <div class="faq-content">
                    Hiring, promotions, events, services, announcements and more.
                </div>
            </details>

            <details class="faq-item">
                <summary>Which payment methods do you accept?</summary>
                <div class="faq-content">
                    UPI, Debit Cards, Credit Cards and Net Banking.
                </div>
            </details>

        </div>

    </div>

</section>

<!-- ==========================================
     CONTACT + FOOTER
========================================== -->

<section class="contact-footer">

    <div class="contact-section">

        <div class="contact-heading">
            <small>Connect with PosterGali</small>
            <h2>Let's Talk About Growing<br>Your Local Reach Today</h2>
        </div>

        <div class="contact-box">

            <div class="contact-left">

                <h3>Get in touch</h3>

                <p>
                    We're here to help your business
                    connect locally.
                </p>

                <div class="contact-info">
                    <div class="contact-icon">☎</div>
                    <span>
                        +91 74839201654<br>
                        +91 83559120375
                    </span>
                </div>

                <div class="contact-info">
                    <div class="contact-icon">✉</div>
                    <span>askpostergali@gmail.com</span>
                </div>

            </div>

            <form class="contact-form">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" placeholder="Type your name">
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" placeholder="Type your name">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="Type your email">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" placeholder="Type your phone number">
                    </div>

                    <div class="form-group full">
                        <label>Message</label>
                        <textarea placeholder="Type your message"></textarea>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- FOOTER -->

    <footer class="pg-footer">

        <div class="footer-main">

            <div class="footer-left">

                <div class="footer-logo">
                    POSTER<br>GALI
                </div>

                <p>
                    India's hyperlocal poster platform.
                    Start with your street. Reach your
                    whole city.
                </p>

                <div class="social-title">
                    FOLLOW US
                </div>

                <div class="socials">
                    <a href="#">f</a>
                    <a href="#">ig</a>
                </div>

            </div>

            <div class="footer-right">

                <div class="footer-posters">
                    <div class="footer-poster"></div>
                    <div class="footer-poster"></div>
                </div>

                <div class="store-buttons">

                    <a href="#" class="store-btn">
                        Google Play Store
                    </a>

                    <a href="#" class="store-btn">
                        Apple App Store
                    </a>

                </div>

            </div>

        </div>

        <div class="footer-bottom">

            <div>
                © 2026 Poster Gali. All Rights Reserved.
            </div>

            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>

        </div>

    </footer>

</section>
</body>
</html>