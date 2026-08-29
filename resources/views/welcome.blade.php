<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PosterGali </title>

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
<nav class="navbar">
    <div class="logo-wrap">
        <div class="logo-badge">
            <span>Poster</span>
            <span>गली</span>
        </div>
    </div>

    <div class="nav-links">
        <a href="#">Home</a>
        <a href="#">Features</a>
        <a href="#">FAQ</a>
        <a href="#">Company</a>
        <a href="#">Contact</a>
    </div>

    <div class="nav-actions">
        <button class="btn-download">Download Now</button>
        <div class="menu-toggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">

    <div class="tag">India ka Poster Bazaar</div>

    <h1>Your Street, your posters<br>now on your phone</h1>

    <p>
        Whether it's hiring, a promotion, or any event — with PosterGali,<br>your message reaches straight to the walls of your city
    </p>

    <!-- DESKTOP CTA BUTTON ONLY -->
    <button class="hero-cta-btn">Download Now</button>

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
            <img class="poster-img"src="{{ asset('images/jb2.png') }}"  alt="Poster 5">
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

<!-- APP DOWNLOAD / CREASED PAPER SECTION -->
<section class="app-crease-section">
    <div class="app-crease-badge">DOWNLOAD NOW</div>
    <h2>Your Very Own Poster Bazaar Available On Android & iOS</h2>
    <p>Get it free today, design your first poster in minutes and watch your business grow like never before!</p>

    <div class="app-store-btns">
        <!-- Google Play Store -->
        <a href="#" class="app-store-btn">
            <svg width="22" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M3.6 1.8L14.2 12.4L3.6 23C3.2 22.5 3 21.8 3 21V3C3 2.2 3.2 1.5 3.6 1.8Z" fill="#2196F3"/>
                <path d="M17.5 9.1L14.2 12.4L17.5 15.7L21.3 13.5C22.2 13 22.2 11.8 21.3 11.3L17.5 9.1Z" fill="#FFC107"/>
                <path d="M3.6 1.8L14.2 12.4L17.5 9.1L4.8 1.9C4.4 1.7 4 1.7 3.6 1.8Z" fill="#4CAF50"/>
                <path d="M3.6 23C4 23.1 4.4 23.1 4.8 22.9L17.5 15.7L14.2 12.4L3.6 23Z" fill="#F44336"/>
            </svg>
            <div>
                <span class="btn-text-small">GET IT ON</span>
                <span class="btn-text-large">Google Play Store</span>
            </div>
        </a>

        <!-- Apple App Store -->
        <a href="#" class="app-store-btn">
            <svg width="22" height="24" viewBox="0 0 24 24" fill="#111">
                <path d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.09 22C7.79 22.05 6.8 20.68 5.96 19.47C4.25 17 2.94 12.45 4.7 9.39C5.57 7.87 7.13 6.91 8.82 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.92 6.84C16.57 6.87 18.39 7.1 19.56 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5ZM15.22 4.93C15.9 4.1 16.36 2.95 16.23 1.8C15.24 1.84 14.04 2.46 13.33 3.29C12.7 4.02 12.15 5.2 12.3 6.33C13.41 6.42 14.54 5.76 15.22 4.93Z"/>
            </svg>
            <div>
                <span class="btn-text-small">DOWNLOAD ON THE</span>
                <span class="btn-text-large">Apple App Store</span>
            </div>
        </a>
    </div>
</section>

<!-- WHY POSTERGALI SECTION -->
<section class="why-section">
    <div class="sub-header-label">Why PosterGali?</div>
    <h2 class="main-section-title">India's Hyperlocal Street Ad Network</h2>

    <!-- FOLDER TABBED COMPONENT -->
    <div class="folder-container">
        <div class="folder-tabs">
            <button class="folder-tab tab-vendors">Vendors</button>
            <button class="folder-tab tab-locals">Locals</button>
            <button class="folder-tab tab-hiring">Hiring</button>
        </div>

        <div class="folder-card">
            <!-- Market Scene & Phone Holder Illustration -->
            <div class="folder-media-box">
                <svg width="100%" height="100%" viewBox="0 0 380 200" preserveAspectRatio="xMidYMid slice">
                    <rect width="380" height="200" fill="#2D1A0D"/>
                    <path d="M 0,0 L 120,40 L 240,10 L 380,50 L 380,0 Z" fill="#992D1D" opacity="0.8"/>
                    <path d="M 40,0 L 160,35 L 280,15 L 380,35 L 380,0 Z" fill="#E69526" opacity="0.7"/>

                    <circle cx="90" cy="90" r="18" fill="#F5C29B"/>
                    <path d="M 70,120 Q 90,105 110,120 L 110,160 L 70,160 Z" fill="#C4351D"/>

                    <circle cx="310" cy="85" r="16" fill="#D99B73"/>
                    <path d="M 290,110 Q 310,95 330,110 L 330,160 L 290,160 Z" fill="#2E7D32"/>

                    <g transform="translate(110, 20)">
                        <path d="M 10,140 Q 30,120 50,110 L 60,180 Z" fill="#C4885C"/>
                        <rect x="30" y="10" width="85" height="165" rx="14" fill="#111111" stroke="#C4351D" stroke-width="2"/>
                        <rect x="34" y="14" width="77" height="157" rx="10" fill="#FAF8E9"/>
                        <rect x="34" y="14" width="77" height="22" fill="#C4351D"/>
                        <text x="40" y="29" fill="#FFF" font-size="9" font-weight="bold" font-family="Impact">PosterGali</text>

                        <rect x="38" y="42" width="69" height="30" rx="4" fill="#3B6B4A"/>
                        <text x="42" y="54" fill="#FFF" font-size="6" font-weight="bold">Poster Lagao</text>
                        <text x="42" y="64" fill="#FFF5C2" font-size="5">Dhanda Badhao</text>

                        <image href="poster2.png" x="38" y="78" width="32" height="48" preserveAspectRatio="xMidYMid slice" />
                        <image href="poster2.png" x="74" y="78" width="33" height="48" preserveAspectRatio="xMidYMid slice" />
                        <circle cx="72.5" cy="148" r="8" fill="#F5A623" stroke="#111" stroke-width="1"/>
                        <text x="69.5" y="152" fill="#111" font-size="11" font-weight="bold">+</text>
                    </g>
                </svg>
            </div>

            <div class="folder-content-body">
                <h3>Poster Lagao, Dhanda Badhao</h3>
                <p>Got an opening, offer, new stock, service or a job to fill? Make a digital poster in under a minute — no design skills, no big budget. Your first posters are on us. Reach every phone in your mohalla for less than a chai.</p>
                <button class="btn-folder-cta">Post Your First Poster For Free</button>
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
<section class="who-section">
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
<section class="promises-section">
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
<section class="how-works-section">
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
<section class="contact-section">
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
<section class="faq-section">
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

<!-- FOOTER SECTION -->
<footer class="footer-section">
    <!-- Hanging Wooden Signboard Logo -->
    <div class="footer-sign-wrap">
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
    </div>

    <p class="footer-tagline">India's hyperlocal poster platform. Start with your street, reach your whole city.</p>

    <div class="follow-title">FOLLOW US</div>
    <div class="social-icons">
        <a href="#" class="social-btn">f</a>
        <a href="#" class="social-btn">📷</a>
    </div>

    <div class="app-store-btns" style="margin-bottom: 25px;">
        <a href="#" class="app-store-btn" style="min-width: 140px;">
            <svg width="18" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M3.6 1.8L14.2 12.4L3.6 23C3.2 22.5 3 21.8 3 21V3C3 2.2 3.2 1.5 3.6 1.8Z" fill="#2196F3"/>
                <path d="M17.5 9.1L14.2 12.4L17.5 15.7L21.3 13.5C22.2 13 22.2 11.8 21.3 11.3L17.5 9.1Z" fill="#FFC107"/>
            </svg>
            <div>
                <span class="btn-text-small">GET IT ON</span>
                <span class="btn-text-large">Google Play Store</span>
            </div>
        </a>

        <a href="#" class="app-store-btn" style="min-width: 140px;">
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
        <div>© 2024 PosterGali. All rights reserved.</div>
        <div><a href="#">Privacy Policy</a></div>
        <div><a href="#">Terms of Service</a></div>
    </div>
</footer>

<script>
    // Interactive FAQ Accordion
    document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(el => el.classList.remove('active'));
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
</script>

</body>
</html>
