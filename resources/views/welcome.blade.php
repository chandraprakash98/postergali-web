<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PosterGali - India's Hyperlocal Poster Bazaar</title>

<!-- Google Fonts for Modern Display & Geometric Typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<style>
    html {
        scroll-behavior: smooth;
    }

    /* Target section clearance so fixed/sticky navbar doesn't cover headers */
    [id] {
        scroll-margin-top: 85px;
    }

    /* ── Mobile Navigation Toggle & Drawer ── */
    .menu-toggle {
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 5px;
        width: 42px;
        height: 42px;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: background 0.2s ease;
    }

    .menu-toggle:hover {
        background: rgba(196, 53, 29, 0.08);
    }

    .menu-toggle span {
        display: block;
        width: 24px;
        height: 3px;
        background: #C4351D;
        border-radius: 3px;
        transition: transform 0.25s ease, opacity 0.25s ease;
    }

    .mobile-nav-drawer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99999;
    }

    .mobile-nav-drawer.open {
        display: flex;
    }

    .drawer-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(4px);
        animation: drawerFadeIn 0.25s ease-out;
    }

    .drawer-panel {
        position: relative;
        background: #FAF8E9;
        width: 82%;
        max-width: 320px;
        height: 100%;
        padding: 24px 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 6px 0 28px rgba(0, 0, 0, 0.25);
        animation: drawerSlideRight 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow-y: auto;
        z-index: 2;
    }

    .drawer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1.5px solid #E5DFCA;
    }

    .drawer-close {
        background: none;
        border: none;
        font-size: 28px;
        color: #333;
        cursor: pointer;
        line-height: 1;
        padding: 6px;
        border-radius: 6px;
        transition: color 0.15s, background 0.15s;
    }

    .drawer-close:hover {
        background: rgba(0, 0, 0, 0.06);
        color: #C4351D;
    }

    .drawer-links {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .drawer-links a {
        display: block;
        padding: 13px 16px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: #1A1A1A;
        text-decoration: none;
        border-radius: 10px;
        transition: background 0.15s, color 0.15s;
    }

    .drawer-links a:hover,
    .drawer-links a:active {
        background: rgba(196, 53, 29, 0.08);
        color: #C4351D;
    }

    .drawer-download-btn {
        margin-top: auto;
        background: #C4351D;
        color: #ffffff !important;
        text-align: center;
        padding: 14px 22px;
        border-radius: 999px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 15px;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(196, 53, 29, 0.35);
        display: block;
        transition: background 0.2s, transform 0.15s;
    }

    .drawer-download-btn:hover {
        background: #A82813;
        transform: translateY(-1px);
    }

    @keyframes drawerFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes drawerSlideRight {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }

    @media (max-width: 768px) {
        .nav-links {
            display: none !important;
        }
        .menu-toggle {
            display: flex !important;
        }
    }

    /* ── DOWNLOAD NOW SECTION (MATCHING REFERENCE IMAGE 1) ── */
    .app-crease-section {
        background-image: url('{{ asset('images/downloadbg.png') }}') !important;
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        padding: 72px 24px 80px !important;
        text-align: center !important;
        position: relative !important;
        overflow: hidden !important;
        width: 100% !important;
    }

    .app-crease-container {
        max-width: 740px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .app-crease-badge {
        display: inline-block !important;
        background: #9E3324 !important;
        color: #ffffff !important;
        font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
        font-size: 13.5px !important;
        font-weight: 800 !important;
        letter-spacing: 0.9px !important;
        padding: 6px 18px !important;
        border-radius: 2px !important;
        border: 1.5px solid #1a1612 !important;
        box-shadow: 2.5px 3px 0px #1a1612 !important;
        transform: rotate(-1.5deg) !important;
        margin-bottom: 20px !important;
        text-transform: uppercase !important;
    }

    .app-crease-section h2 {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: clamp(26px, 4.4vw, 42px) !important;
        font-weight: 900 !important;
        line-height: 1.16 !important;
        color: #111111 !important;
        letter-spacing: -0.4px !important;
        margin: 0 auto 16px !important;
        max-width: 680px !important;
        text-align: center !important;
    }

    .app-crease-section p {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: clamp(13px, 1.8vw, 16px) !important;
        font-weight: 500 !important;
        color: #1f1b16 !important;
        line-height: 1.5 !important;
        margin: 0 auto 28px !important;
        max-width: 550px !important;
        text-align: center !important;
        opacity: 0.96 !important;
    }

    .app-store-btns {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 16px !important;
        flex-wrap: wrap !important;
    }

    .app-store-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 14px !important;
        background: #FAF4DF !important;
        border: 2px solid #181512 !important;
        border-radius: 999px !important;
        padding: 10px 22px !important;
        text-decoration: none !important;
        color: #111111 !important;
        box-shadow: 3px 4px 0px #181512 !important;
        min-width: 220px !important;
        text-align: left !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease !important;
    }

    .app-store-btn:hover {
        background: #FFF8E7 !important;
        transform: translate(1px, 2px) !important;
        box-shadow: 2px 2px 0px #181512 !important;
    }

    .app-store-btn:active {
        transform: translate(3px, 4px) !important;
        box-shadow: 0px 0px 0px #181512 !important;
    }

    .store-icon-play {
        flex-shrink: 0;
        width: 26px;
        height: 28px;
    }

    .apple-circle-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #1e2530;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .apple-circle-badge svg {
        width: 18px;
        height: 18px;
    }

    .btn-text-wrap {
        display: flex;
        flex-direction: column;
    }

    .app-store-btn .btn-text-small {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        color: #444444 !important;
        letter-spacing: 0.6px !important;
        text-transform: uppercase !important;
        line-height: 1 !important;
        display: block !important;
    }

    .app-store-btn .btn-text-large {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        color: #111111 !important;
        line-height: 1.25 !important;
        margin-top: 2px !important;
        display: block !important;
    }

    @media (max-width: 600px) {
        .app-crease-section {
            padding: 50px 18px 58px !important;
        }
        .desktop-br {
            display: none;
        }
        .app-store-btn {
            min-width: 205px !important;
            padding: 9px 18px !important;
        }
    }

    /* ── WHY POSTERGALI SECTION (MATCHING REFERENCE IMAGE 2) ── */
    .why-section {
        padding: 55px 20px 65px !important;
        background: #FAF8E9 !important;
        text-align: center !important;
        position: relative !important;
    }

    .why-section .sub-header-label {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #222222 !important;
        margin-bottom: 6px !important;
        text-align: center !important;
    }

    .why-section .main-section-title {
        font-family: 'Plus Jakarta Sans', 'BernardMT', serif !important;
        font-size: clamp(22px, 4vw, 32px) !important;
        font-weight: 900 !important;
        color: #B55B09 !important;
        line-height: 1.22 !important;
        margin: 0 auto 28px !important;
        max-width: 480px !important;
        text-align: center !important;
    }

    .folder-container {
        max-width: 450px !important;
        margin: 0 auto !important;
        position: relative !important;
    }

    .folder-tabs {
        display: flex !important;
        gap: 5px !important;
        padding-left: 12px !important;
        margin-bottom: -1px !important;
        position: relative !important;
        z-index: 2 !important;
    }

    .folder-tab {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        padding: 9px 22px !important;
        border-top-left-radius: 14px !important;
        border-top-right-radius: 14px !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        outline: none !important;
    }

    .folder-tab.tab-vendors {
        background: #F6CB7B !important;
        color: #1F1710 !important;
    }

    .folder-tab.tab-locals {
        background: #D98377 !important;
        color: #261B17 !important;
    }

    .folder-tab.tab-hiring {
        background: #DF8C81 !important;
        color: #261B17 !important;
    }

    .folder-tab.active {
        background: #F6CB7B !important;
        color: #1F1710 !important;
        font-weight: 800 !important;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05) !important;
    }

    .folder-card {
        background: #F6CB7B !important;
        border-radius: 22px !important;
        border-top-left-radius: 0px !important;
        padding: 16px !important;
        box-shadow: 0 12px 30px rgba(184, 128, 38, 0.22), 0 4px 10px rgba(0, 0, 0, 0.06) !important;
        text-align: left !important;
    }

    .folder-media-box {
        width: 100% !important;
        height: auto !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        position: relative !important;
        background: #181008 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15) !important;
        line-height: 0 !important;
    }

    .folder-img {
        width: 100% !important;
        height: auto !important;
        display: block !important;
        border-radius: 16px !important;
        object-fit: cover !important;
    }

    .folder-content-body {
        padding-top: 14px !important;
    }

    .folder-content-body h3 {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: clamp(17px, 2.8vw, 20px) !important;
        font-weight: 900 !important;
        color: #381F10 !important;
        margin-bottom: 7px !important;
        line-height: 1.3 !important;
    }

    .folder-content-body p {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 12.5px !important;
        color: #4A3525 !important;
        line-height: 1.45 !important;
        margin-bottom: 16px !important;
        font-weight: 500 !important;
    }

    .btn-folder-cta {
        display: block !important;
        width: 100% !important;
        background: linear-gradient(180deg, #F3AA33 0%, #E69418 100%) !important;
        border: 1px solid #D6880E !important;
        color: #FFFFFF !important;
        padding: 12px 18px !important;
        border-radius: 999px !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        text-align: center !important;
        text-decoration: none !important;
        box-shadow: 0 4px 14px rgba(220, 137, 24, 0.4) !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
        cursor: pointer !important;
        transition: transform 0.15s ease, filter 0.15s ease !important;
    }

    .btn-folder-cta:hover {
        transform: translateY(-1px) !important;
        filter: brightness(1.05) !important;
    }

    .feature-grid-sub {
        max-width: 400px !important;
        margin: 24px auto 0 !important;
        display: flex !important;
        justify-content: space-between !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 12.5px !important;
        color: #2E2A27 !important;
        line-height: 1.8 !important;
        font-weight: 700 !important;
        text-align: left !important;
    }

    .feature-grid-sub .green-highlight {
        color: #2E7D32 !important;
        font-weight: 800 !important;
    }
</style>
</head>

<body>

<div class="company-top-bar" id="company-top">
    <div class="company-top-container">
        <div class="company-line">
            <strong>PosterGali</strong> is a product of
            <strong>Unitygrid Private Limited</strong>
        </div>
        <div class="company-subline">
            PosterGali is owned, operated and developed by
            <strong>Unitygrid Private Limited</strong>,
            an Indian technology company.
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="logo-wrap">
        <a href="#hero" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
            <div class="logo-badge">
                <span>Poster</span>
                <span>गली</span>
            </div>
        </a>
    </div>

    <div class="nav-links">
        <a href="#hero">Home</a>
        <a href="#why-postergali">Features</a>
        <a href="#faq">FAQ</a>
        <a href="#company">Company</a>
        <a href="#contact">Contact</a>
    </div>

    <div class="nav-actions">
        <a href="#download" class="btn-download" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Download Now</a>
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation menu" type="button">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero" id="hero">

    <div class="tag">India ka Poster Bazaar</div>

    <h1>Your Street, your posters<br>now on your phone</h1>

    <p>
        Whether it's hiring, a promotion, or any event — with PosterGali,<br>your message reaches straight to the walls of your city
    </p>

    <!-- DESKTOP CTA BUTTON ONLY -->
    <a href="#download" class="hero-cta-btn" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Download Now</a>

    <!-- UNIFIED STAGGERED POSTERS & PHONE STAGE -->
    <div class="phone-section">
        
        <!-- 1st Poster (Far Left) -->
        <div class="poster-card poster-far-left">
            <img class="poster-img" src="{{ asset('images/image3.png') }}"  alt="Poster 1">
        </div>

        <!-- 3rd Poster (Left Low) -->
        <div class="poster-card poster-left-low">
            <img class="poster-img" src="{{ asset('images/image1.png') }}"  alt="Special Offer Poster">
        </div>

        <!-- 2nd Poster (Left High) -->
        <div class="poster-card poster-left-high">
            <img class="poster-img" src="{{ asset('images/imag1.png') }}"  alt="Poster 3">
        </div>

        <!-- CENTRAL PHONE PREVIEW -->
        <div class="phone">
            <img class="phone-img" src="{{ asset('images/mobile.png') }}" alt="Mobile View">
        </div>

        <!-- 4th Poster (Right Low) -->
        <div class="poster-card poster-right-low">
            <img class="poster-img" src="{{ asset('images/jb1.png') }}"   alt="Poster 4">
        </div>

        <!-- 5th Poster (Right High) -->
        <div class="poster-card poster-right-high">
            <img class="poster-img" src="{{ asset('images/jb2.png') }}"  alt="Poster 5">
        </div>

        <!-- 6th Poster (Far Right) -->
        <div class="poster-card poster-far-right">
            <img class="poster-img" src="{{ asset('images/jb3.png') }}"  alt="Poster 6">
        </div>

    </div>
</section>

<!-- FEATURE STRIP -->
<div class="feature-strip">
    <div>STARTS FROM RS. 19 ONLY</div>
    <span class="dot"></span>
    <div>LOCAL REACH</div>
    <span class="dot"></span>
    <div>INSTANT LIVE</div>
    <span class="dot"></span>
    <div>SMART TARGETING</div>
</div>

<!-- APP DOWNLOAD / CREASED PAPER SECTION (MATCHING REFERENCE IMAGE 1) -->
<section class="app-crease-section" id="download">
    <div class="app-crease-container">
        <div class="app-crease-badge">DOWNLOAD NOW</div>
        <h2>Your Very Own Poster<br>Bazaar  Available On<br>Android & IOS</h2>
        <p>Get it free today, design your first poster in minutes<br class="desktop-br"> and watch your business grow like never before!</p>

        <div class="app-store-btns">
            <!-- Google Play Store -->
            <a href="https://play.google.com" target="_blank" rel="noopener noreferrer" class="app-store-btn" aria-label="Get PosterGali on Google Play Store">
                <svg width="26" height="28" viewBox="0 0 24 24" fill="none" class="store-icon-play">
                    <path d="M3.6 1.8L14.2 12.4L3.6 23C3.2 22.5 3 21.8 3 21V3C3 2.2 3.2 1.5 3.6 1.8Z" fill="#2196F3"/>
                    <path d="M17.5 9.1L14.2 12.4L17.5 15.7L21.3 13.5C22.2 13 22.2 11.8 21.3 11.3L17.5 9.1Z" fill="#FFC107"/>
                    <path d="M3.6 1.8L14.2 12.4L17.5 9.1L4.8 1.9C4.4 1.7 4 1.7 3.6 1.8Z" fill="#4CAF50"/>
                    <path d="M3.6 23C4 23.1 4.4 23.1 4.8 22.9L17.5 15.7L14.2 12.4L3.6 23Z" fill="#F44336"/>
                </svg>
                <div class="btn-text-wrap">
                    <span class="btn-text-small">GET IT ON</span>
                    <span class="btn-text-large">Google Play Store</span>
                </div>
            </a>

            <!-- Apple App Store -->
            <a href="https://apple.com/app-store/" target="_blank" rel="noopener noreferrer" class="app-store-btn" aria-label="Download PosterGali on Apple App Store">
                <div class="apple-circle-badge">
                    <svg width="18" height="20" viewBox="0 0 24 24" fill="#ffffff">
                        <path d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.09 22C7.79 22.05 6.8 20.68 5.96 19.47C4.25 17 2.94 12.45 4.7 9.39C5.57 7.87 7.13 6.91 8.82 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.92 6.84C16.57 6.87 18.39 7.1 19.56 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5ZM15.22 4.93C15.9 4.1 16.36 2.95 16.23 1.8C15.24 1.84 14.04 2.46 13.33 3.29C12.7 4.02 12.15 5.2 12.3 6.33C13.41 6.42 14.54 5.76 15.22 4.93Z"/>
                    </svg>
                </div>
                <div class="btn-text-wrap">
                    <span class="btn-text-small">DOWNLOAD ON THE</span>
                    <span class="btn-text-large">Apple App Store</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- WHY POSTERGALI SECTION (MATCHING REFERENCE IMAGE 2) -->
<section class="why-section" id="why-postergali">
    <div class="sub-header-label">Why PosterGali?</div>
    <h2 class="main-section-title">India's Hyperlocal Street Ad Network</h2>

    <!-- FOLDER TABBED COMPONENT -->
    <div class="folder-container">
        <div class="folder-tabs" role="tablist">
            <button class="folder-tab tab-vendors active" id="tabVendors" role="tab" aria-selected="true" aria-controls="tabContentVendors" onclick="switchFolderTab('vendors')">Vendors</button>
            <button class="folder-tab tab-locals" id="tabLocals" role="tab" aria-selected="false" aria-controls="tabContentLocals" onclick="switchFolderTab('locals')">Locals</button>
            <button class="folder-tab tab-hiring" id="tabHiring" role="tab" aria-selected="false" aria-controls="tabContentHiring" onclick="switchFolderTab('hiring')">Hiring</button>
        </div>

        <div class="folder-card" id="folderCard">
            <!-- Market Scene & Phone Holder Image -->
            <div class="folder-media-box">
                <img src="{{ asset('images/whypostergali.png') }}" alt="PosterGali App in Action" class="folder-img">
            </div>

            <!-- Tab 1: Vendors Content (Default) -->
            <div class="folder-content-body tab-content active" id="tabContentVendors" role="tabpanel">
                <h3>Poster Lagao, Dhanda Badhao</h3>
                <p>Got an opening, offer, new stock, a service, or a job to fill? Make a digital poster in under a minute — no design skills, no big budget. Your first posters are on us. Reach every phone in your mohalla for less than a chai.</p>
                <a href="#download" class="btn-folder-cta">Post Your First Poster For Free</a>
            </div>

            <!-- Tab 2: Locals Content -->
            <div class="folder-content-body tab-content" id="tabContentLocals" role="tabpanel" style="display: none;">
                <h3>Apne Mohalle Ki Har Khabar</h3>
                <p>Discover daily offers, discounts, neighborhood grocery deals, home services, and local community updates directly from verified shops and neighbors in your area.</p>
                <a href="#download" class="btn-folder-cta">Explore Your Mohalla</a>
            </div>

            <!-- Tab 3: Hiring Content -->
            <div class="folder-content-body tab-content" id="tabContentHiring" role="tabpanel" style="display: none;">
                <h3>Staff Chahiye? Poster Lagao</h3>
                <p>Hire shop assistants, delivery riders, cooks, helpers, or technicians in hours. Connect directly with local job seekers in your area with zero middlemen and zero commission.</p>
                <a href="#download" class="btn-folder-cta">Post a Job Poster Free</a>
            </div>
        </div>
    </div>

    <!-- Feature Grid below folder card -->
    <div class="feature-grid-sub">
        <div>
            <div>Not a directory</div>
            <div>Not a paid ad platform</div>
        </div>
        <div>
            <div>Not a delivery app</div>
            <div class="green-highlight">A poster wall for everyone</div>
        </div>
    </div>
</section>

<!-- WHO IS IT FOR SECTION -->
<section class="who-section" id="who-is-it-for">
    <div class="sub-header-label">Who is it for?</div>
    <h2 class="main-section-title">Different People, One Platform</h2>

    <div class="red-hero-card">
        <div class="raghav-phone-frame">
            <div class="phone-notch"></div>
            
            <div class="raghav-illustration">
                <svg width="100%" height="100%" viewBox="0 0 180 120" preserveAspectRatio="xMidYMid meet">
                    <rect width="180" height="120" fill="#F5EFE0"/>
                    <circle cx="90" cy="42" r="14" fill="#3D2314"/>
                    <circle cx="90" cy="44" r="11" fill="#F0C5A3"/>
                    <path d="M 74,58 Q 90,54 106,58 L 108,98 L 72,98 Z" fill="#FFFFFF" stroke="#D7CCC8" stroke-width="1"/>
                    <path d="M 74,98 L 88,98 L 88,120 L 74,120 Z" fill="#A1887F"/>
                    <path d="M 92,98 L 106,98 L 106,120 L 92,120 Z" fill="#A1887F"/>
                    <rect x="100" y="70" width="10" height="18" rx="2" fill="#111"/>
                </svg>
            </div>

            <div class="raghav-name-tag">
                <strong>Raghav</strong>
                <span>24, Gwalior</span>
            </div>

            <div class="box-challenge">
                <div class="icon">⚠️</div>
                <div class="box-text">
                    <b>CHALLENGE:</b>
                    Spent ₹15,000/month on printing posters.
                </div>
            </div>

            <div class="box-solution">
                <div class="icon">✔️</div>
                <div class="box-text">
                    <b>WITH POSTERGALI:</b>
                    Posts his shop's products from home. ₹0 on printing.
                </div>
            </div>
        </div>

        <div class="dots-indicator">
            <span class="dot-item active"></span>
            <span class="dot-item"></span>
            <span class="dot-item"></span>
            <span class="dot-item"></span>
        </div>
    </div>
</section>

<!-- FIVE PROMISES SECTION -->
<section class="promises-section" id="promises">
    <div class="sub-header-label">Why should people trust it?</div>
    <h2 class="main-section-title">Five Promises, No Exceptions</h2>

    <div class="stack-cards-wrapper">
        <div class="stack-card card-1">
            <div class="card-num">01</div>
            <h4>Hyperlocal by default</h4>
            <p>Every poster reaches the exact street and mohalla you select.</p>
        </div>

        <div class="stack-card card-2">
            <div class="card-num">02</div>
            <h4>No favourites—everyone equal</h4>
            <p>Every poster gets equal visibility in your area.</p>
        </div>

        <div class="stack-card card-3">
            <div class="card-num">03</div>
            <h4>No commission, no hidden</h4>
            <p>Direct connection with your customers without middleman fees.</p>
        </div>

        <div class="stack-card card-4">
            <div class="card-num">04</div>
            <h4>No complexity</h4>
            <p>Design and publish your poster in 60 seconds.</p>
        </div>

        <div class="stack-card card-5">
            <div class="card-num">05</div>
            <h4>No learning curve</h4>
            <p>Hindi + English, built for every age and trade</p>
        </div>
    </div>
</section>

<!-- HOW IT WORKS (STAMP CAROUSEL) -->
<section class="how-works-section" id="how-it-works">
    <div class="sub-header-label">How it works?</div>
    <h2 class="main-section-title">Getting started with PosterGali</h2>
    <p>Follow these simple steps to publish your poster in minutes.</p>

    <!-- Hands Holding Phone Graphic -->
    <div class="hands-phone-graphic">
        <svg width="100%" height="100%" viewBox="0 0 180 170" preserveAspectRatio="xMidYMid meet">
            <!-- Left & Right Hands -->
            <path d="M 20,130 Q 50,80 65,70 L 60,170 Z" fill="#D99B73"/>
            <path d="M 160,130 Q 130,80 115,70 L 120,170 Z" fill="#C4885C"/>
            
            <!-- Phone mockup -->
            <rect x="52" y="10" width="76" height="145" rx="14" fill="#111" stroke="#C4351D" stroke-width="2"/>
            <rect x="56" y="14" width="68" height="137" rx="10" fill="#FAF8E9"/>
            
            <rect x="56" y="14" width="68" height="20" fill="#C4351D"/>
            <rect x="60" y="40" width="60" height="25" rx="4" fill="#3B6B4A"/>
            <image href="poster2.png" x="60" y="70" width="28" height="40" preserveAspectRatio="xMidYMid slice" />
            <image href="poster2.png" x="92" y="70" width="28" height="40" preserveAspectRatio="xMidYMid slice" />
        </svg>
    </div>

    <!-- Stamp Ticket Card -->
    <div class="stamp-card-container">
        <div class="stamp-card">
            <h3>Choose Your Location</h3>
            <p>Select your shop or service area, and check available promotion options for your area.</p>
        </div>
        <div class="stamp-dots">
            <span class="stamp-dot active"></span>
            <span class="stamp-dot"></span>
            <span class="stamp-dot"></span>
            <span class="stamp-dot"></span>
        </div>
    </div>
</section>

<!-- GET IN TOUCH / CONTACT SECTION -->
<section class="contact-section" id="contact">
    <div class="sub-header-label">Want to connect business?</div>
    <h2 class="main-section-title">Let's Talk About Growing Your Local Reach Today</h2>

    <div class="contact-card-green">
        <h1>Get in touch</h1>
        <p>We're here to help your business connect locally. Share your details, we'll contact you soon.</p>

        <!-- Phone & Email info pill -->
        <div class="contact-info-pill">
            <div class="contact-info-item">
                <div class="contact-icon-circle">📞</div>
                <div>
                    <div>+91 74709 98914</div>
                    <div>+91 83529 62885</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-icon-circle">✉️</div>
                <div>contact@postergali.com</div>
            </div>
        </div>

        <!-- Contact Form -->
        <form class="contact-form" onsubmit="event.preventDefault();">
            <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <input type="text" placeholder="Enter your full name">
                    <span class="input-icon">👤</span>
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <div class="input-wrapper">
                    <input type="text" placeholder="Enter your city/area">
                    <span class="input-icon">📍</span>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <input type="email" placeholder="Enter your email address">
                    <span class="input-icon">✉️</span>
                </div>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <div class="input-wrapper">
                    <input type="tel" placeholder="Enter your phone number">
                    <span class="input-icon">📞</span>
                </div>
            </div>

            <div class="form-group">
                <label>Message</label>
                <div class="input-wrapper">
                    <textarea placeholder="Enter your message here"></textarea>
                    <span class="input-icon">💬</span>
                </div>
            </div>
        </form>
    </div>
    <div class="scallop-bottom-edge"></div>
</section>

<!-- FAQS SECTION -->
<section class="faq-section" id="faq">
    <div class="faq-header-badge">FAQs</div>
    
    <div class="faq-accordion-box">
        <div class="faq-item">
            <div class="faq-question">
                <span>What is PosterGali?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                PosterGali is India's hyperlocal digital poster bazaar that connects local shops, vendors, and service providers with customers in their exact street or neighborhood.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Who can use PosterGali?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                Any local business owner, shopkeeper, vendor, recruiter, or local resident looking to share job openings, discount sales, or local announcements.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Is it free or paid to use?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                Posting your first poster is completely free! After that, posters start from as low as ₹19 per promotion.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>How do I create a poster?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                Simply download the app, select a design template or upload your details, choose your target area, and publish in under 60 seconds.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>What kind of posters can I create?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                You can create hiring posters, discount sales, new arrivals, announcements, local services, and events.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>How long will my poster stay live or be published?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                Depending on the plan selected, posters remain live from 7 days up to 30 days.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Can I edit or delete my poster after it's published?</span>
                <span class="faq-chevron">▼</span>
            </div>
            <div class="faq-answer">
                Yes, you can easily edit text or delete your poster anytime directly from your profile dashboard in the app.
            </div>
        </div>
    </div>
</section>

<!-- FOOTER / COMPANY SECTION -->
<footer class="footer-section" id="company">
    <!-- Hanging Wooden Signboard Logo -->
    <div class="footer-sign-wrap">
        <a href="#hero" style="text-decoration: none; display: inline-block;">
            <svg width="170" height="110" viewBox="0 0 170 110">
                <!-- Hanging String -->
                <line x1="85" y1="5" x2="35" y2="35" stroke="#795548" stroke-width="2"/>
                <line x1="85" y1="5" x2="135" y2="35" stroke="#795548" stroke-width="2"/>
                <circle cx="85" cy="5" r="4" fill="#5D4037"/>

                <!-- Wooden Board Frame -->
                <rect x="15" y="32" width="140" height="70" rx="8" fill="#FFF9E6" stroke="#8D6E63" stroke-width="3"/>
                <rect x="18" y="35" width="134" height="64" rx="6" fill="none" stroke="#D7CCC8" stroke-width="1.5"/>

                <!-- Logo Text inside Wooden Signboard -->
                <g transform="translate(30, 48)">
                    <text x="0" y="24" fill="#C4351D" font-weight="900" font-size="22" font-family="Impact">POSTER</text>
                    <text x="24" y="48" fill="#E88F2A" font-weight="900" font-size="22" font-family="Garamond">गली</text>
                    <circle cx="48" cy="10" r="3" fill="#C4351D"/>
                </g>
            </svg>
        </a>
    </div>

    <p class="footer-tagline">India's hyperlocal poster platform. Start with your street, reach your whole city.</p>

    <div class="follow-title">FOLLOW US</div>
    <div class="social-icons">
        <a href="https://facebook.com/postergali" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Follow PosterGali on Facebook">f</a>
        <a href="https://instagram.com/postergali" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Follow PosterGali on Instagram">📷</a>
    </div>

    <div class="app-store-btns" style="margin-bottom: 25px;">
        <a href="#download" class="app-store-btn" style="min-width: 140px;" aria-label="Get on Google Play">
            <svg width="18" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M3.6 1.8L14.2 12.4L3.6 23C3.2 22.5 3 21.8 3 21V3C3 2.2 3.2 1.5 3.6 1.8Z" fill="#2196F3"/>
                <path d="M17.5 9.1L14.2 12.4L17.5 15.7L21.3 13.5C22.2 13 22.2 11.8 21.3 11.3L17.5 9.1Z" fill="#FFC107"/>
            </svg>
            <div>
                <span class="btn-text-small">GET IT ON</span>
                <span class="btn-text-large">Google Play Store</span>
            </div>
        </a>

        <a href="#download" class="app-store-btn" style="min-width: 140px;" aria-label="Download on App Store">
            <svg width="18" height="20" viewBox="0 0 24 24" fill="#111">
                <path d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.09 22C7.79 22.05 6.8 20.68 5.96 19.47C4.25 17 2.94 12.45 4.7 9.39C5.57 7.87 7.13 6.91 8.82 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.92 6.84C16.57 6.87 18.39 7.1 19.56 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5ZM15.22 4.93C15.9 4.1 16.36 2.95 16.23 1.8C15.24 1.84 14.04 2.46 13.33 3.29C12.7 4.02 12.15 5.2 12.3 6.33C13.41 6.42 14.54 5.76 15.22 4.93Z"/>
            </svg>
            <div>
                <span class="btn-text-small">DOWNLOAD ON THE</span>
                <span class="btn-text-large">Apple App Store</span>
            </div>
        </a>
    </div>

    <!-- Bottom Golden Bar -->
    <div class="footer-bottom-bar">
        <div>© 2026 PosterGali. All rights reserved.</div>
        <div><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></div>
        <div><a href="{{ url('/privacy-policy') }}">Terms of Service</a></div>
    </div>
</footer>

<!-- MOBILE NAV DRAWER -->
<div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Navigation menu">
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer-panel">
        <div class="drawer-header">
            <div class="logo-badge" style="transform: scale(0.9); transform-origin: left center;">
                <span>Poster</span>
                <span>गली</span>
            </div>
            <button class="drawer-close" id="drawerClose" aria-label="Close navigation menu">&times;</button>
        </div>
        <div class="drawer-links">
            <a href="#hero">Home</a>
            <a href="#why-postergali">Features</a>
            <a href="#faq">FAQ</a>
            <a href="#company">Company</a>
            <a href="#contact">Contact</a>
        </div>
        <a href="#download" class="drawer-download-btn">Download Now</a>
    </div>
</div>

<script>
    // ── Interactive FAQ Accordion ──
    document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(el => el.classList.remove('active'));
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // ── Folder Tab Switcher (Vendors / Locals / Hiring) ──
    function switchFolderTab(tabName) {
        const tabs = {
            vendors: {
                btn: document.getElementById('tabVendors'),
                content: document.getElementById('tabContentVendors')
            },
            locals: {
                btn: document.getElementById('tabLocals'),
                content: document.getElementById('tabContentLocals')
            },
            hiring: {
                btn: document.getElementById('tabHiring'),
                content: document.getElementById('tabContentHiring')
            }
        };

        ['vendors', 'locals', 'hiring'].forEach(key => {
            if (tabs[key] && tabs[key].btn && tabs[key].content) {
                if (key === tabName) {
                    tabs[key].btn.classList.add('active');
                    tabs[key].btn.setAttribute('aria-selected', 'true');
                    tabs[key].content.style.display = 'block';
                } else {
                    tabs[key].btn.classList.remove('active');
                    tabs[key].btn.setAttribute('aria-selected', 'false');
                    tabs[key].content.style.display = 'none';
                }
            }
        });
    }

    // ── Mobile Navigation Drawer ──
    const drawer = document.getElementById('mobileNavDrawer');
    const toggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('drawerOverlay');
    const closeBtn = document.getElementById('drawerClose');

    function openDrawer() {
        if (drawer) {
            drawer.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDrawer() {
        if (drawer) {
            drawer.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    if (toggle) toggle.addEventListener('click', openDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

    // Close drawer when any link inside it is clicked
    document.querySelectorAll('#mobileNavDrawer a').forEach(link => {
        link.addEventListener('click', closeDrawer);
    });

    // ── Smooth Scroll with Header Offset ──
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;
            const target = document.querySelector(targetId);
            if (!target) return;
            e.preventDefault();
            const navbar = document.querySelector('.navbar');
            const offset = navbar ? navbar.offsetHeight + 10 : 80;
            const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });
</script>

</body>
</html>
