<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>PosterGali - India's Hyperlocal Poster Bazaar</title>

<!-- Google Fonts for Modern Display & Geometric Typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/desktop.css') }}?v={{ time() }}" media="(min-width: 901px)">
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

<!-- APP DOWNLOAD / CREASED PAPER SECTION (MATCHING REFERENCE IMAGE) -->
<section class="app-crease-section" id="download">
    <div class="app-crease-container">
        <!-- Left Side: Typography & Download Now Badge -->
        <div class="app-crease-left">
            <h2>Your very own<br>Poster Bazaar<br>Available on<br>Android & iOS</h2>
            <p>Get it free today, design your first poster in minutes<br class="desktop-br"> and watch your business grow like never before!</p>
            <a href="#download" class="app-crease-badge">DOWNLOAD NOW</a>
        </div>

        <!-- Right Side: Dual QR Code Cards & App Store Download Buttons -->
        <div class="app-crease-right">
            <!-- Left Card: Google Play Store -->
            <div class="qr-store-card qr-card-play">
                <div class="qr-code-box">
                    <svg class="qr-code-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Corner bracket alignment markers -->
                        <path d="M14 44V18C14 15.7909 15.7909 14 18 14H44" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>
                        <path d="M156 14H182C184.209 14 186 15.7909 186 18V44" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>
                        <path d="M14 156V182C14 184.209 15.7909 186 18 186H44" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>
                        <path d="M156 186H182C184.209 186 186 184.209 186 182V156" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>

                        <!-- Top-Left Finder Pattern -->
                        <rect x="26" y="26" width="44" height="44" rx="4" fill="none" stroke="#111111" stroke-width="8"/>
                        <rect x="38" y="38" width="20" height="20" rx="2" fill="#111111"/>

                        <!-- Top-Right Finder Pattern -->
                        <rect x="130" y="26" width="44" height="44" rx="4" fill="none" stroke="#111111" stroke-width="8"/>
                        <rect x="142" y="38" width="20" height="20" rx="2" fill="#111111"/>

                        <!-- Bottom-Left Finder Pattern -->
                        <rect x="26" y="130" width="44" height="44" rx="4" fill="none" stroke="#111111" stroke-width="8"/>
                        <rect x="38" y="142" width="20" height="20" rx="2" fill="#111111"/>

                        <!-- QR Code Matrix Data Blocks -->
                        <rect x="80" y="26" width="10" height="20" fill="#111111"/>
                        <rect x="100" y="26" width="18" height="10" fill="#111111"/>
                        <rect x="80" y="54" width="18" height="18" fill="#111111"/>
                        <rect x="108" y="44" width="10" height="28" fill="#111111"/>

                        <rect x="26" y="80" width="18" height="10" fill="#111111"/>
                        <rect x="34" y="98" width="16" height="20" fill="#111111"/>
                        <rect x="58" y="80" width="12" height="38" fill="#111111"/>
                        <rect x="80" y="80" width="22" height="10" fill="#111111"/>
                        <rect x="110" y="80" width="10" height="22" fill="#111111"/>
                        <rect x="130" y="80" width="18" height="10" fill="#111111"/>
                        <rect x="156" y="80" width="20" height="18" fill="#111111"/>

                        <rect x="80" y="100" width="10" height="30" fill="#111111"/>
                        <rect x="98" y="100" width="22" height="10" fill="#111111"/>
                        <rect x="130" y="98" width="12" height="22" fill="#111111"/>
                        <rect x="150" y="106" width="26" height="12" fill="#111111"/>

                        <rect x="80" y="140" width="18" height="12" fill="#111111"/>
                        <rect x="106" y="138" width="14" height="24" fill="#111111"/>
                        <rect x="130" y="130" width="20" height="10" fill="#111111"/>
                        <rect x="158" y="128" width="18" height="22" fill="#111111"/>

                        <rect x="80" y="162" width="30" height="14" fill="#111111"/>
                        <rect x="120" y="152" width="10" height="24" fill="#111111"/>
                        <rect x="138" y="150" width="14" height="26" fill="#111111"/>
                        <rect x="160" y="160" width="16" height="16" fill="#111111"/>
                    </svg>
                </div>

                <!-- Google Play Store Button -->
                <a href="https://play.google.com/store/apps/details?id=com.postergali.postergali" target="_blank" rel="noopener noreferrer" class="app-store-btn" aria-label="Get PosterGali on Google Play Store">
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
            </div>

            <!-- Right Card: Apple App Store -->
            <div class="qr-store-card qr-card-apple">
                <div class="qr-code-box">
                    <svg class="qr-code-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Corner bracket alignment markers -->
                        <path d="M14 44V18C14 15.7909 15.7909 14 18 14H44" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>
                        <path d="M156 14H182C184.209 14 186 15.7909 186 18V44" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>
                        <path d="M14 156V182C14 184.209 15.7909 186 18 186H44" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>
                        <path d="M156 186H182C184.209 186 186 184.209 186 182V156" stroke="#111111" stroke-width="5.5" stroke-linecap="round"/>

                        <!-- Top-Left Finder Pattern -->
                        <rect x="26" y="26" width="44" height="44" rx="4" fill="none" stroke="#111111" stroke-width="8"/>
                        <rect x="38" y="38" width="20" height="20" rx="2" fill="#111111"/>

                        <!-- Top-Right Finder Pattern -->
                        <rect x="130" y="26" width="44" height="44" rx="4" fill="none" stroke="#111111" stroke-width="8"/>
                        <rect x="142" y="38" width="20" height="20" rx="2" fill="#111111"/>

                        <!-- Bottom-Left Finder Pattern -->
                        <rect x="26" y="130" width="44" height="44" rx="4" fill="none" stroke="#111111" stroke-width="8"/>
                        <rect x="38" y="142" width="20" height="20" rx="2" fill="#111111"/>

                        <!-- QR Code Matrix Data Blocks -->
                        <rect x="82" y="26" width="12" height="24" fill="#111111"/>
                        <rect x="102" y="24" width="18" height="12" fill="#111111"/>
                        <rect x="80" y="58" width="24" height="12" fill="#111111"/>
                        <rect x="112" y="44" width="10" height="26" fill="#111111"/>

                        <rect x="26" y="80" width="14" height="12" fill="#111111"/>
                        <rect x="44" y="80" width="26" height="10" fill="#111111"/>
                        <rect x="26" y="100" width="22" height="18" fill="#111111"/>
                        <rect x="54" y="98" width="16" height="20" fill="#111111"/>

                        <rect x="80" y="80" width="12" height="24" fill="#111111"/>
                        <rect x="100" y="80" width="20" height="10" fill="#111111"/>
                        <rect x="128" y="80" width="22" height="12" fill="#111111"/>
                        <rect x="158" y="80" width="18" height="18" fill="#111111"/>

                        <rect x="80" y="112" width="20" height="14" fill="#111111"/>
                        <rect x="108" y="100" width="12" height="28" fill="#111111"/>
                        <rect x="128" y="100" width="14" height="20" fill="#111111"/>
                        <rect x="150" y="106" width="26" height="12" fill="#111111"/>

                        <rect x="80" y="134" width="18" height="20" fill="#111111"/>
                        <rect x="106" y="136" width="14" height="26" fill="#111111"/>
                        <rect x="128" y="128" width="22" height="12" fill="#111111"/>
                        <rect x="158" y="126" width="18" height="24" fill="#111111"/>

                        <rect x="80" y="162" width="30" height="14" fill="#111111"/>
                        <rect x="118" y="152" width="12" height="24" fill="#111111"/>
                        <rect x="138" y="148" width="14" height="28" fill="#111111"/>
                        <rect x="160" y="158" width="16" height="18" fill="#111111"/>
                    </svg>
                </div>

                <!-- Apple App Store Button -->
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
    </div>
</section>

<!-- WHY POSTERGALI SECTION -->
<section class="why-section" id="why-postergali">
    <div class="sub-header-label">Why PosterGali?</div>
    <h2 class="main-section-title">India's Hyperlocal Street Ad Network</h2>

    <!-- FOLDER TABBED COMPONENT -->
    <div class="folder-container">
        <div class="folder-tabs" role="tablist">
            <button class="folder-tab tab-vendors active" id="tabVendors" role="tab" aria-selected="true" aria-controls="tabContentVendors" onclick="switchFolderTab('vendors')">For Shop Owners</button>
            <button class="folder-tab tab-locals" id="tabLocals" role="tab" aria-selected="false" aria-controls="tabContentLocals" onclick="switchFolderTab('locals')">For Neighbours</button>
            <button class="folder-tab tab-hiring" id="tabHiring" role="tab" aria-selected="false" aria-controls="tabContentHiring" onclick="switchFolderTab('hiring')">For Job Seekers</button>
        </div>

        <div class="folder-card tab-vendors-active" id="folderCard">
            <!-- Desktop Two-Column Layout -->
            <div class="folder-desktop-inner">
                <!-- Left: Content -->
                <div class="folder-text-col">
                    <!-- Tab 1: Vendors Content (Default) -->
                    <div class="folder-content-body tab-content active" id="tabContentVendors" role="tabpanel">
                        <h3>Poster lagao,<br>dhanda badhao</h3>
                        <p>Got an opening, offer, new stock, a service, or a job to fill? Make a digital poster in under a minute — no design skills, no big budget. Your first posters are on us. Reach every phone in your mohalla for less than a chai.</p>
                        <a href="#download" class="btn-folder-cta btn-tab-vendors">Post your first poster for free</a>
                    </div>

                    <!-- Tab 2: Locals Content -->
                    <div class="folder-content-body tab-content" id="tabContentLocals" role="tabpanel" style="display: none;">
                        <h3>Aapki gali mein<br>sab milta hai</h3>
                        <p>The best deals and freshest offers around you, in one place. Check before you step out, or stumble on something new. Call, save, share or get directions in a tap.</p>
                        <a href="#download" class="btn-folder-cta btn-tab-locals">See offers near you &nbsp;&gt;</a>
                    </div>

                    <!-- Tab 3: Hiring Content -->
                    <div class="folder-content-body tab-content" id="tabContentHiring" role="tabpanel" style="display: none;">
                        <h3>Kaam dhundo<br>apni gali mein</h3>
                        <p>Find jobs of your choice within walking distance of home or in a place you prefer. New openings from shops and businesses in your own neighbourhood — no endless scrolling, no faraway commutes.</p>
                        <a href="#download" class="btn-folder-cta btn-tab-hiring">Find jobs near you &nbsp;&gt;</a>
                    </div>
                </div>

                <!-- Right: Image -->
                <div class="folder-img-col">
                    <img src="{{ asset('images/tb1.png') }}" alt="PosterGali vendors view" class="folder-img" id="folderImage">
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Grid below folder card -->
    <div class="feature-grid-sub">
        <div>
            <div>Not a directory</div>
            <div>Not a delivery app</div>
        </div>
        <div>
            <div>Not a paid ad platform</div>
            <div class="green-highlight">A poster wall for everyone</div>
        </div>
    </div>
</section>

<!-- WHO IS IT FOR SECTION -->
<section class="who-section" id="who-is-it-for">
    <div class="sub-header-label">Who is it for?</div>
    <h2 class="main-section-title">Different people, one platform</h2>

    <div class="red-hero-card">
        <div class="persona-carousel" id="personaCarousel" tabindex="0" aria-label="PosterGali audience examples">
            <div class="persona-slides">
                <img class="persona-slide active" src="{{ asset('images/mb1.png') }}" alt="Raghav, a local job seeker">
                <img class="persona-slide" src="{{ asset('images/mb3.png') }}" alt="Ramesh Bhai, a mall retailer">
                <img class="persona-slide" src="{{ asset('images/mb2.png') }}" alt="Sunita Tai, a home tiffin service provider">
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
    <h2 class="main-section-title">Five promises, no exceptions</h2>

    <div class="stack-cards-wrapper">
        <div class="stack-card card-1">
            <div class="card-num">01</div>
            <h4>No AI Deciding Your Fate</h4>
            <p>No algorithm boosts a competitor over you</p>
        </div>

        <div class="stack-card card-2">
            <div class="card-num">02</div>
            <h4>No Favourites—Everyone Equal</h4>
            <p>Street vendor or mall shop—same rate, same visibility</p>
        </div>

        <div class="stack-card card-3">
            <div class="card-num">03</div>
            <h4>No Commission, No Hidden Fees</h4>
            <p>₹20 + taxes. That's all we take</p>
        </div>

        <div class="stack-card card-4">
            <div class="card-num">04</div>
            <h4>No Complexity</h4>
            <p>If you've put up a poster before, you know PosterGali</p>
        </div>

        <div class="stack-card card-5">
            <div class="card-num">05</div>
            <h4>No Learning Curve</h4>
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
                <img class="stamp-slide" src="{{ asset('images/md1.png') }}" alt="Define your goal">
                <img class="stamp-slide active" src="{{ asset('images/md2.png') }}" alt="Choose your location">
                <img class="stamp-slide" src="{{ asset('images/md3.png') }}" alt="Create your poster">
                <img class="stamp-slide" src="{{ asset('images/md4.png') }}" alt="Publish your poster">
                <img class="stamp-slide" src="{{ asset('images/md5.png') }}" alt="Reach your local audience">
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
<section class="contact-section{{ session('contact_success') ? ' contact-dialog-visible' : '' }}" id="contact">
    <div class="sub-header-label">Connect with PosterGali</div>
    <h2 class="main-section-title">Let's Talk About Growing Your Local Reach Today</h2>

    <div class="contact-card-green">
        <h1>Get in touch</h1>
        <p>We're here to help your business connect locally. Share your details, we'll contact you soon.</p>

        <!-- Phone & Email info pill -->
        <div class="contact-info-pill">
            <div class="contact-info-item">
                <div class="contact-icon-circle">
                    <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"></path>
                    </svg>
                </div>
                <div>
                    <div>+91 74709 98914</div>
                    <div>+91 83529 62885</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-icon-circle">
                    <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path d="m3 7 9 6 9-6"></path>
                    </svg>
                </div>
                <div>contact@postergali.com</div>
            </div>
        </div>

        <!-- Contact Form -->
        <form id="contactForm" class="contact-form" action="{{ route('contact-us.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="contact-first-name">First Name</label>
                <div class="input-wrapper">
                    <input id="contact-first-name" name="first_name" type="text" placeholder="Enter your first name" value="{{ old('first_name') }}" required>
                    <span class="input-icon">
                        <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="contact-last-name">Last Name</label>
                <div class="input-wrapper">
                    <input id="contact-last-name" name="last_name" type="text" placeholder="Enter your last name" value="{{ old('last_name') }}" required>
                    <span class="input-icon">
                        <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="contact-email">Email</label>
                <div class="input-wrapper">
                    <input id="contact-email" name="email" type="email" placeholder="Enter your email address" value="{{ old('email') }}" required>
                    <span class="input-icon">
                        <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="contact-phone">Phone Number</label>
                <div class="input-wrapper">
                    <input id="contact-phone" name="phone" type="tel" placeholder="Enter your phone number" value="{{ old('phone') }}" required>
                    <span class="input-icon">
                        <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="contact-message">Message</label>
                <div class="input-wrapper">
                    <textarea id="contact-message" name="message" placeholder="Enter your message here" required>{{ old('message') }}</textarea>
                    <span class="input-icon">
                        <svg class="contact-icon-svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9.8 9.8 0 0 1-4-.8L3 21l1.8-4.2A8.5 8.5 0 1 1 21 11.5Z"></path>
                            <path d="M8 12h.01M12 12h.01M16 12h.01"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <p id="contactFormError" class="contact-form-error" role="alert" hidden></p>
            <button class="contact-submit-btn" type="submit">Send Message</button>
        </form>
    </div>

    <dialog id="contactSuccessDialog" class="contact-success-dialog">
        <div class="contact-success-dialog-content">
            <img class="contact-success-logo" src="{{ asset('images/logo.png') }}" alt="PosterGali">
            <div class="contact-success-mark" aria-hidden="true">&#10003;</div>
            <h2>Thank you!</h2>
            <p>Thank you for contacting PosterGali. Our team will get back to you within 24 hours.</p>
            <button type="button" onclick="this.closest('dialog').close()">Continue</button>
        </div>
    </dialog>
    
</section>

<!-- FAQS SECTION -->
<section class="faq-section" id="faq">
    <div class="faq-header-badge">FAQs</div>
    
    <div class="faq-accordion-box">
        <div class="faq-item">
            <div class="faq-question">
                <span>What is PosterGali?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
            </div>
            <div class="faq-answer">
                PosterGali is India's hyperlocal digital poster bazaar that connects local shops, vendors, and service providers with customers in their exact street or neighborhood.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Who can use PosterGali?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
            </div>
            <div class="faq-answer">
                Any local business owner, shopkeeper, vendor, recruiter, or local resident looking to share job openings, discount sales, or local announcements.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Is it free or paid to use?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
            </div>
            <div class="faq-answer">
                Posting your first poster is completely free! After that, posters start from as low as ₹19 per promotion.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>How do I create a poster?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
            </div>
            <div class="faq-answer">
                Simply download the app, select a design template or upload your details, choose your target area, and publish in under 60 seconds.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>What kind of posters can I create?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
            </div>
            <div class="faq-answer">
                You can create hiring posters, discount sales, new arrivals, announcements, local services, and events.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>How long will my poster stay live or be published?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
            </div>
            <div class="faq-answer">
                Depending on the plan selected, posters remain live from 7 days up to 30 days.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Can I edit or delete my poster after it's published?</span>
                <span class="faq-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"></path></svg></span>
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
        <a href="https://facebook.com/postergali" target="_blank" rel="noopener noreferrer"  aria-label="Follow PosterGali on Facebook">
            <img src="{{ asset('images/fb.png') }}" alt="" class="social-icon-svg">
        </a>
        <a href="https://instagram.com/postergali" target="_blank" rel="noopener noreferrer"  aria-label="Follow PosterGali on Instagram">
            <img src="{{ asset('images/insta.png') }}" alt="" class="social-icon-svg">
        </a>
    </div>

    <div class="footer-app-showcase">
        <div class="footer-qr-codes" aria-label="Scan to download PosterGali">
            <div class="footer-qr-box" data-qr-source=".qr-card-play .qr-code-svg"></div>
            <div class="footer-qr-box" data-qr-source=".qr-card-apple .qr-code-svg"></div>
        </div>

    <div class="app-store-btns" style="margin-bottom: 25px;">
        <a href="https://play.google.com/store/apps/details?id=com.postergali.postergali" target="_blank" rel="noopener noreferrer" class="app-store-btn" aria-label="Get on Google Play">
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
    </div>

    <!-- Bottom Golden Bar -->
    <div class="footer-bottom-bar">
        <div>© 2026 PosterGali. All rights reserved.</div>
        <div class="footer-legal-links">
            <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
            <a href="{{ url('/terms-and-conditions') }}">Terms of Service</a>
        </div>
        <div><a href="https://www.instagram.com/madeincode.in/" target="_blank" rel="noopener noreferrer">Developed by Chandra Prakash &amp; MadeInCode.in team</a></div>
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
    const contactForm = document.getElementById('contactForm');
    const contactFormError = document.getElementById('contactFormError');
    const contactSuccessDialog = document.getElementById('contactSuccessDialog');

    function showContactSuccess() {
        document.getElementById('contact')?.classList.add('contact-dialog-visible');
        contactSuccessDialog.showModal();
        document.body.classList.add('contact-dialog-open');
    }

    if (contactSuccessDialog) {
        contactSuccessDialog.addEventListener('close', () => {
            document.body.classList.remove('contact-dialog-open');
            document.getElementById('contact')?.classList.remove('contact-dialog-visible');
        });
    }

    if (contactForm) {
        contactForm.addEventListener('submit', async event => {
            event.preventDefault();
            const submitButton = contactForm.querySelector('.contact-submit-btn');
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';
            contactFormError.hidden = true;

            try {
                const response = await fetch(contactForm.action, {
                    method: 'POST',
                    body: new FormData(contactForm),
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(response.status === 422
                        ? 'Please check the form and try again.'
                        : 'We could not send your message. Please try again.');
                }

                contactForm.reset();
                showContactSuccess();
            } catch (error) {
                contactFormError.textContent = error.message;
                contactFormError.hidden = false;
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Send Message';
            }
        });
    }

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
                image: '{{ asset('images/tb1.png') }}',
                bg: '{{ asset('images/bg1.png') }}'
            },
            locals: {
                btn: document.getElementById('tabLocals'),
                content: document.getElementById('tabContentLocals'),
                image: '{{ asset('images/tb2.png') }}',
                bg: '{{ asset('images/bg2.png') }}'
            },
            hiring: {
                btn: document.getElementById('tabHiring'),
                content: document.getElementById('tabContentHiring'),
                image: '{{ asset('images/tb3.png') }}',
                bg: '{{ asset('images/bg3.png') }}'
            }
        };

        const folderCard = document.getElementById('folderCard');

        ['vendors', 'locals', 'hiring'].forEach(key => {
            if (tabs[key] && tabs[key].btn && tabs[key].content) {
                if (key === tabName) {
                    const imgEl = document.getElementById('folderImage');
                    if (imgEl) {
                        imgEl.src = tabs[key].image;
                        imgEl.alt = `${tabName} PosterGali view`;
                    }
                    if (folderCard) {
                        folderCard.classList.remove('tab-vendors-active', 'tab-locals-active', 'tab-hiring-active');
                        folderCard.classList.add(`tab-${key}-active`);
                        folderCard.style.backgroundImage = `url('${tabs[key].bg}')`;
                    }
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

    document.querySelectorAll('.footer-qr-box').forEach(box => {
        const source = document.querySelector(box.dataset.qrSource);
        if (source) box.appendChild(source.cloneNode(true));
    });

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
