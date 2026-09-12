<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>PosterGali - India's Hyperlocal Poster Bazaar</title>

<!-- Google Fonts for Modern Display & Geometric Typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>


<div class="company-top-bar">
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
            <img class="logo-image" src="{{ asset('images/logo.png') }}" alt="PosterGali">
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
        Whether it's hiring, a promotion, or any event — with PosterGali, your message reaches straight to the walls of your city
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
<div class="feature-strip" aria-label="PosterGali features">
    <div class="feature-strip-track">
        <div class="feature-strip-group">
            <div class="feature-strip-item">STARTS FROM RS. 19 ONLY</div>
            <span class="dot"></span>
            <div class="feature-strip-item">LOCAL REACH</div>
            <span class="dot"></span>
            <div class="feature-strip-item">INSTANT LIVE</div>
            <span class="dot"></span>
            <div class="feature-strip-item">SMART TARGETING</div>
        </div>
        <div class="feature-strip-group" aria-hidden="true">
            <div class="feature-strip-item">STARTS FROM RS. 19 ONLY</div>
            <span class="dot"></span>
            <div class="feature-strip-item">LOCAL REACH</div>
            <span class="dot"></span>
            <div class="feature-strip-item">INSTANT LIVE</div>
            <span class="dot"></span>
            <div class="feature-strip-item">SMART TARGETING</div>
        </div>
    </div>
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
                <img src="{{ asset('images/tb1.png') }}" alt="PosterGali vendors view" class="folder-img" id="folderImage">
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
        <div class="persona-carousel" id="personaCarousel" tabindex="0" aria-label="PosterGali audience examples">
            <div class="persona-slides">
                <img class="persona-slide active" src="{{ asset('images/mb1.png') }}" alt="Raghav, a local job seeker">
                <img class="persona-slide" src="{{ asset('images/mb2.png') }}" alt="Sunita Tai, a home tiffin service provider">
                <img class="persona-slide" src="{{ asset('images/mb3.png') }}" alt="Ramesh Bhai, a mall retailer">
                <img class="persona-slide" src="{{ asset('images/mb4.png') }}" alt="Shrey, a local deal seeker">
            </div>
        </div>

        <div class="dots-indicator" role="tablist" aria-label="Audience examples">
            <button class="dot-item active" type="button" role="tab" aria-label="Show example 1" aria-selected="true"></button>
            <button class="dot-item" type="button" role="tab" aria-label="Show example 2" aria-selected="false"></button>
            <button class="dot-item" type="button" role="tab" aria-label="Show example 3" aria-selected="false"></button>
            <button class="dot-item" type="button" role="tab" aria-label="Show example 4" aria-selected="false"></button>
        </div>
    </div>
</section>

<!-- FIVE PROMISES SECTION -->
<section class="promises-section" id="promises">
    <div class="sub-header-label">Why should people trust it?</div>
    <p class="main-section-title">Five Promises, No Exceptions</p>

    <div class="stack-cards-wrapper">
        <div class="stack-card card-1">
            <div class="card-num">01</div>
            <h4>Hyperlocal by default</h4>
            <p>Every poster reaches the exact street and mohalla you select.</p>
        </div>

        <div class="stack-card card-2">
            <div class="card-num">02</div>
            <h4>No favourites-everyone equal</h4>
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

    <!-- Hands Holding Phone Graphic with overlapping instruction cards -->
    <div class="how-works-visual">
        <div class="hands-phone-graphic">
            <img src="{{ asset('images/phhand.png') }}" alt="Hands holding a PosterGali phone" class="hands-phone-image">
        </div>

        <div class="stamp-card-container" aria-label="How PosterGali works">
            <div class="stamp-card-track">
                <img class="stamp-slide" src="{{ asset('images/hp1.png') }}" alt="Define your goal">
                <img class="stamp-slide active" src="{{ asset('images/hp2.png') }}" alt="Choose your location">
                <img class="stamp-slide" src="{{ asset('images/hp3.png') }}" alt="Create your poster">
                <img class="stamp-slide" src="{{ asset('images/hp4.png') }}" alt="Publish your poster">
                <img class="stamp-slide" src="{{ asset('images/hp5.png') }}" alt="Reach your local audience">
            </div>
        </div>

        <div class="stamp-dots">
            <span class="stamp-dot"></span>
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
                <div class="contact-icon-circle">✉</div>
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
                <label>Last Name</label>
                <div class="input-wrapper">
                    <input type="text" placeholder="Enter your last name">
                    <span class="input-icon">📍</span>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <input type="email" placeholder="Enter your email address">
                    <span class="input-icon">✉</span>
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
    <div class="footer-sign-wrap">
        <a href="#hero" style="text-decoration: none; display: inline-block;">
            <img src="{{ asset('images/logo.png') }}" alt="PosterGali" class="footer-logo-image">
        </a>
    </div>

    <p class="footer-tagline">India's hyperlocal poster platform. Start with your street, reach your whole city.</p>

    <div class="follow-title">FOLLOW US</div>
    <div class="social-icons">
        <a href="https://facebook.com/postergali" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Follow PosterGali on Facebook">f</a>
        <a href="https://instagram.com/postergali" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Follow PosterGali on Instagram">📷</a>
    </div>

    <div class="app-store-btns" style="margin-bottom: 25px;">
        <a href="#download" class="app-store-btn" aria-label="Get on Google Play">
            <svg width="18" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M3.6 1.8L14.2 12.4L3.6 23C3.2 22.5 3 21.8 3 21V3C3 2.2 3.2 1.5 3.6 1.8Z" fill="#2196F3"/>
                <path d="M17.5 9.1L14.2 12.4L17.5 15.7L21.3 13.5C22.2 13 22.2 11.8 21.3 11.3L17.5 9.1Z" fill="#FFC107"/>
            </svg>
            <div>
                <span class="btn-text-small">GET IT ON</span>
                <span class="btn-text-large">Google Play Store</span>
            </div>
        </a>

        <a href="#download" class="app-store-btn" aria-label="Download on App Store">
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
        <div><a href="{{ url('/terms-and-conditions') }}">Terms of Service</a></div>
        <div>Developed by Chandra Prakash &amp; MadeInCode.in team</div>
    </div>
</footer>

<!-- MOBILE NAV DRAWER -->
<div class="mobile-nav-drawer" id="mobileNavDrawer" role="dialog" aria-modal="true" aria-label="Navigation menu">
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer-panel">
        <div class="drawer-header">
            <img class="logo-image" src="{{ asset('images/logo.png') }}" alt="PosterGali">
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
                content: document.getElementById('tabContentVendors'),
                image: '{{ asset('images/tb1.png') }}'
            },
            locals: {
                btn: document.getElementById('tabLocals'),
                content: document.getElementById('tabContentLocals'),
                image: '{{ asset('images/tb2.png') }}'
            },
            hiring: {
                btn: document.getElementById('tabHiring'),
                content: document.getElementById('tabContentHiring'),
                image: '{{ asset('images/tb3.png') }}'
            }
        };

        ['vendors', 'locals', 'hiring'].forEach(key => {
            if (tabs[key] && tabs[key].btn && tabs[key].content) {
                if (key === tabName) {
                    document.getElementById('folderImage').src = tabs[key].image;
                    document.getElementById('folderImage').alt = `${tabName} PosterGali view`;
                    tabs[key].btn.classList.add('active');
                    tabs[key].btn.setAttribute('aria-selected', 'true');
                    tabs[key].content.classList.add('active');
                    tabs[key].content.style.display = 'block';
                } else {
                    tabs[key].btn.classList.remove('active');
                    tabs[key].btn.setAttribute('aria-selected', 'false');
                    tabs[key].content.classList.remove('active');
                    tabs[key].content.style.display = 'none';
                }
            }
        });
    }

    // ── Audience Card Carousel ──
    const personaCarousel = document.getElementById('personaCarousel');
    const personaSlides = personaCarousel ? Array.from(personaCarousel.querySelectorAll('.persona-slide')) : [];
    const personaDots = personaCarousel ? Array.from(document.querySelectorAll('.dot-item')) : [];
    let personaIndex = 0;
    let personaTouchStart = 0;

    function showPersona(index) {
        if (!personaSlides.length) return;
        personaIndex = (index + personaSlides.length) % personaSlides.length;
        personaSlides.forEach((slide, slideIndex) => {
            slide.classList.toggle('active', slideIndex === personaIndex);
            slide.classList.toggle('next', slideIndex === (personaIndex + 1) % personaSlides.length);
            slide.classList.toggle('previous', slideIndex === (personaIndex - 1 + personaSlides.length) % personaSlides.length);
        });
        personaDots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === personaIndex;
            dot.classList.toggle('active', isActive);
            dot.setAttribute('aria-selected', String(isActive));
        });
    }

    if (personaCarousel) {
        personaCarousel.addEventListener('keydown', event => {
            if (event.key === 'ArrowLeft') showPersona(personaIndex - 1);
            if (event.key === 'ArrowRight') showPersona(personaIndex + 1);
        });
        personaCarousel.addEventListener('touchstart', event => {
            personaTouchStart = event.changedTouches[0].clientX;
        }, { passive: true });
        personaCarousel.addEventListener('touchend', event => {
            const distance = event.changedTouches[0].clientX - personaTouchStart;
            if (Math.abs(distance) > 40) showPersona(personaIndex + (distance < 0 ? 1 : -1));
        }, { passive: true });
        personaDots.forEach((dot, dotIndex) => dot.addEventListener('click', () => showPersona(dotIndex)));
        showPersona(0);
        setInterval(() => showPersona(personaIndex + 1), 5000);
    }

    // How-it-works image slider
    const stampContainer = document.querySelector('.stamp-card-container');
    const stampTrack = document.querySelector('.stamp-card-track');
    const stampSlides = stampTrack ? Array.from(stampTrack.querySelectorAll('.stamp-slide')) : [];
    const stampDots = stampContainer ? Array.from(document.querySelectorAll('.stamp-dot')) : [];
    let stampIndex = 1;
    let stampTouchStart = 0;

    function positionStampSlider() {
        if (!stampContainer || !stampTrack || !stampSlides.length) return;
        const slideWidth = stampSlides[0].getBoundingClientRect().width;
        const gap = parseFloat(getComputedStyle(stampTrack).gap) || 0;
        const offset = (stampContainer.clientWidth - slideWidth) / 2 - (stampIndex * (slideWidth + gap));
        stampTrack.style.transform = `translateX(${offset}px)`;
    }

    function showStampSlide(index) {
        if (!stampSlides.length) return;
        stampIndex = (index + stampSlides.length) % stampSlides.length;
        stampSlides.forEach((slide, slideIndex) => slide.classList.toggle('active', slideIndex === stampIndex));
        stampDots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === stampIndex));
        positionStampSlider();
    }

    if (stampTrack) {
        stampDots.forEach((dot, dotIndex) => dot.addEventListener('click', () => showStampSlide(dotIndex)));
        stampContainer.addEventListener('touchstart', event => {
            stampTouchStart = event.changedTouches[0].clientX;
        }, { passive: true });
        stampContainer.addEventListener('touchend', event => {
            const distance = event.changedTouches[0].clientX - stampTouchStart;
            if (Math.abs(distance) > 40) showStampSlide(stampIndex + (distance < 0 ? 1 : -1));
        }, { passive: true });
        window.addEventListener('resize', positionStampSlider);
        showStampSlide(1);
        setInterval(() => showStampSlide(stampIndex + 1), 4500);
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
