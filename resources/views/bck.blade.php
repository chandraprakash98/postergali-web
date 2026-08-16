<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PosterGali - Hero & Mobile View</title>
<style>

    @font-face {
        font-family: 'BernardMT';
        src: url('/fonts/Times New Roman Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'GaramondRegular';
        src: url('/fonts/Garamond - Garamond - Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'ImpactFont';
        src: url('/fonts/impact.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'KumarOne';
        src: url('/fonts/KumarOne-Regular.otf') format('opentype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'LemonMilkBold';
        src: url('/fonts/LEMONMILK-Bold.otf') format('opentype');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'MonteStella';
        src: url('/fonts/MonteStella_Trial_Rg.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'QasiraFont';
        src: url('/fonts/Qasira.otf') format('opentype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'QuintessentialFont';
        src: url('/fonts/Quintessential-Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'TimesNewRomanLocal';
        src: url('/fonts/Times New Roman Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    /* Fallback Font Stacks */
    body {
        font-family: 'GaramondRegular', 'Garamond', 'Times New Roman', serif;
        background: #FAF8E9;
        color: #111;
        overflow-x: hidden;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
    }

    .logo,
    .footer-logo {
        font-family: 'ImpactFont', 'Impact', sans-serif;
    }

    .hero h1,
    .why-head h2,
    .how-header h2,
    .faq-top h2,
    .main-section-title {
        font-family: 'BernardMT', 'Georgia', serif;
    }

    .tag,
    .app-badge,
    .process-cardm h4 {
        font-family: 'QasiraFont', 'Trebuchet MS', sans-serif;
    }

    .feature-strip,
    .footer-links a,
    .socials a {
        font-family: 'MonteStella', 'Arial Narrow', sans-serif;
    }

    .process-card h4,
    .pg-content h3 {
        font-family: 'KumarOne', 'Impact', cursive, sans-serif;
    }

    .nav-links a,
    .btn,
    .store a,
    .talk-btn,
    .footer-links a,
    .folder-tab {
        font-family: 'LemonMilkBold', 'Arial Black', sans-serif;
    }

    .process-card p,
    .faq-top p,
    .faq-content,
    .contact-left p,
    .app-text p {
        font-family: 'QuintessentialFont', 'Georgia', serif;
    }

    .faq-item summary,
    .process-column .process-cardm p,
    .process-column .process-card p {
        font-family: 'TimesNewRomanLocal', 'Times New Roman', serif;
    }

/* ================= GLOBAL RESET ================= */

*, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* ================= TOP BAR & NAVBAR ================= */

.company-top-bar {
    width: 100%;
    background: #C4351D;
    color: #fff;
    border-bottom: 2px solid rgba(255, 255, 255, 0.15);
}

.company-top-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 10px 20px;
    text-align: center;
}

.company-line {
    font-size: clamp(12px, 2.2vw, 17px);
    font-weight: 700;
    letter-spacing: 0.3px;
    line-height: 1.4;
}

.company-line strong {
    color: #FFF5C2;
}

.company-subline {
    margin-top: 4px;
    font-size: clamp(10px, 1.8vw, 12px);
    color: rgba(255, 255, 255, 0.95);
    line-height: 1.4;
}

.company-subline strong {
    color: #FFF5C2;
}

.navbar {
    background: #FAF8E9;
    max-width: 1400px;
    margin: 0 auto;
    height: 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    border-bottom: 1px solid #E2DEC3;
}

.logo-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
}

.logo-badge {
    background: #FFFCE5;
    border: 2px solid #C4351D;
    border-radius: 6px;
    padding: 4px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.logo-badge span:first-child {
    font-family: 'ImpactFont', 'Impact', sans-serif;
    font-size: 18px;
    color: #C4351D;
    line-height: 1;
}

.logo-badge span:last-child {
    font-size: 11px;
    font-weight: bold;
    color: #E88F2A;
    line-height: 1;
}

.nav-links {
    display: flex;
    gap: 30px;
}

.nav-links a {
    text-decoration: none;
    color: #111;
    font-size: 14px;
    transition: opacity 0.2s;
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-download {
    background: #C4351D;
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    transition: transform 0.2s, background 0.2s;
    box-shadow: 0 4px 12px rgba(196, 53, 29, 0.25);
    white-space: nowrap;
}

.btn-download:hover {
    background: #A32813;
}

.menu-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    cursor: pointer;
    padding: 6px;
}

.menu-toggle span {
    display: block;
    width: 22px;
    height: 3px;
    background: #C4351D;
    border-radius: 2px;
}

/* ================= HERO SECTION ================= */

.hero {
    text-align: center;
    background-color: #FAF8E9;
    background-image: 
        linear-gradient(90deg, rgba(220, 215, 190, 0.15) 1px, transparent 1px),
        linear-gradient(0deg, rgba(220, 215, 190, 0.15) 1px, transparent 1px);
    background-size: 40px 20px;
    padding: 30px 0 25px; /* 25px bottom padding for gap below phone */
    position: relative;
    overflow: hidden;
    width: 100%;
}

.tag {
    display: inline-block;
    background: #F5A623;
    padding: 10px 22px;
    margin-top: 5px;
    margin-bottom: 18px;
    font-size: clamp(13px, 2.8vw, 16px);
    color: #111;
    font-weight: 700;
    border: 2px solid #111;
    border-radius: 2px;
    box-shadow: 3px 3px 0px #111;
    letter-spacing: 0.3px;
}

.hero h1 {
    font-size: clamp(26px, 5.8vw, 48px);
    line-height: 1.15;
    font-weight: 900;
    letter-spacing: -0.5px;
    color: #F7952A;
    max-width: 900px;
    margin: 0 auto;
    padding: 0 15px;
    text-shadow:
        -1px -1px 0 #000,
         1px -1px 0 #000,
        -1px  1px 0 #000,
         1px  1px 0 #000,
         2px  3px 0 #000;
}

.hero p {
    font-size: clamp(13px, 2.5vw, 17px);
    max-width: 680px;
    margin: 16px auto 0;
    color: #222;
    line-height: 1.5;
    padding: 0 15px;
}

.hero-cta-btn {
    display: inline-block;
    background: #C4351D;
    color: #fff;
    border: none;
    padding: 12px 32px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 700;
    margin-top: 15px;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(196, 53, 29, 0.3);
    transition: transform 0.2s, background 0.2s;
}

.hero-cta-btn:hover {
    background: #A32813;
    transform: translateY(-2deg);
}

/* ================= STAGGERED POSTER STAGE ================= */

.phone-section {
    position: relative;
    width: 100%;
    margin: 10px auto 30px; /* 30px margin-bottom for gap below phone */
    display: flex;
    justify-content: center;
    align-items: flex-end;
}

/* Central Mobile Phone */
.phone {
    position: relative;
    z-index: 10;
    background: #000;
    border: 4px solid #C4351D;
    border-radius: 36px;
    box-shadow: 0 16px 40px rgba(0,0,0,.30);
    flex-shrink: 0;
    overflow: hidden;
}

.phone-screen-content {
    width: 100%;
    height: 100%;
    background: #FAF8E9;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

.phone-top-bar {
    background: #FAF8E9;
    padding: 6px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.phone-brand {
    font-family: 'ImpactFont', sans-serif;
    font-size: 14px;
    color: #C4351D;
}

.phone-location {
    font-size: 8px;
    color: #666;
}

.phone-banner {
    background: #3B6B4A;
    color: #fff;
    margin: 6px 8px;
    padding: 8px;
    border-radius: 8px;
    font-size: 9px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.phone-tabs {
    display: flex;
    gap: 8px;
    padding: 4px 8px;
}

.phone-tab {
    flex: 1;
    padding: 4px 0;
    font-size: 9px;
    text-align: center;
    border-radius: 12px;
    background: #E5E0C2;
    color: #333;
    font-weight: bold;
}

.phone-tab.active {
    background: #C4351D;
    color: #fff;
}

.phone-cards-grid {
    display: flex;
    gap: 6px;
    padding: 6px 8px;
}

.phone-mini-card {
    flex: 1;
    border-radius: 6px;
    padding: 6px;
    font-size: 7px;
    color: #fff;
    height: 110px;
}

.phone-mini-card.pink { background: #AD1457; }
.phone-mini-card.blue { background: #0288D1; }

.phone-floating-add {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 34px;
    height: 34px;
    background: #F5A623;
    border: 2px solid #111;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 20;
}

/* Universal Poster Component */
.poster-card {
    position: absolute;
    border-radius: 2px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.18);
    display: flex;
    flex-direction: column;
    padding: 8px;
    box-sizing: border-box;
    overflow: hidden;
    line-height: 1.2;
    transform: none !important;
    transition: all 0.3s ease;
}

.poster-card .p-title {
    font-weight: 900;
    font-size: clamp(8.5px, 1.1vw, 12px);
    text-transform: uppercase;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.poster-card .p-sub {
    font-size: clamp(6.8px, 0.85vw, 9.5px);
    line-height: 1.2;
    opacity: 0.95;
}

.poster-card .p-badge {
    display: inline-block;
    background: #FFF59D;
    color: #111;
    font-weight: bold;
    font-size: clamp(6px, 0.8vw, 8px);
    padding: 2px 4px;
    margin-top: 4px;
    border-radius: 2px;
    align-self: flex-start;
}

/* ================= DESKTOP SPECIFIC LAYOUT ================= */

@media (min-width: 901px) {
    .phone-section {
        max-width: 1240px;
        height: 435px;
        overflow: visible;
        padding: 0 20px;
    }

    .phone {
        width: 235px;
        height: 435px;
    }

    .poster-card {
        width: 140px;
        height: 205px;
        padding: 12px;
    }

    .poster-far-left {
        left: 20px;
        bottom: 110px;
        background: #3A1054;
        color: #fff;
        z-index: 2;
        opacity: 1;
    }

    .poster-left-high {
        left: 180px;
        bottom: 195px;
        background: #0288D1;
        color: #fff;
        z-index: 3;
    }

    .poster-left-low {
        left: 230px;
        bottom: 25px;
        background: #AD1457;
        color: #fff;
        border: 1px solid #880E4F;
        z-index: 4;
    }

    .poster-right-low {
        right: 230px;
        bottom: 25px;
        background: #FFFFFF;
        color: #111;
        border: 1px solid #ccc;
        z-index: 4;
    }

    .poster-right-high {
        right: 180px;
        bottom: 180px;
        background: #8E24AA;
        color: #fff;
        z-index: 3;
    }

    .poster-far-right {
        right: 20px;
        bottom: 100px;
        background: #7CB342;
        color: #fff;
        z-index: 2;
        opacity: 1;
    }
}

/* ================= MOBILE SPECIFIC LAYOUT ================= */

@media (max-width: 900px) {
    .nav-links { display: none; }
    .menu-toggle { display: flex; }

    .hero-cta-btn {
        display: none !important;
    }

    .phone-section {
        max-width: 100%;
        height: clamp(340px, 80vw, 440px);
        overflow: hidden;
    }

    .phone {
        width: clamp(160px, 40vw, 215px);
        height: clamp(340px, 80vw, 440px);
        z-index: 10;
    }

    .poster-card {
        padding: 6px;
    }

    .poster-far-left {
        width: clamp(74px, 17.5vw, 108px);
        height: clamp(115px, 26.5vw, 160px);
        background: #3A1054;
        color: #fff;
        left: calc(0px - clamp(59px, 14vw, 86px));
        bottom: clamp(65px, 14vw, 95px);
        z-index: 2;
    }

    .poster-left-low {
        width: clamp(78px, 18.5vw, 115px);
        height: clamp(120px, 27.5vw, 168px);
        background: #AD1457;
        color: #fff;
        border: 1px solid #880E4F;
        left: calc(50% - clamp(168px, 43vw, 230px));
        bottom: clamp(18px, 4.5vw, 32px);
        z-index: 4;
    }

    .poster-left-high {
        width: clamp(76px, 18vw, 112px);
        height: clamp(118px, 27vw, 165px);
        background: #0288D1;
        color: #fff;
        left: calc(50% - clamp(120px, 30vw, 170px));
        bottom: clamp(165px, 38vw, 220px);
        z-index: 3;
    }

    .poster-right-low {
        width: clamp(76px, 18vw, 112px);
        height: clamp(118px, 27vw, 165px);
        background: #FFFFFF;
        color: #111;
        border: 1px solid #ccc;
        right: calc(50% - clamp(118px, 29vw, 165px));
        bottom: clamp(20px, 5vw, 35px);
        z-index: 3;
    }

    .poster-right-high {
        width: clamp(78px, 18.5vw, 115px);
        height: clamp(120px, 27.5vw, 168px);
        background: #8E24AA;
        color: #fff;
        right: calc(50% - clamp(168px, 43vw, 230px));
        bottom: clamp(155px, 36vw, 215px);
        z-index: 4;
    }

    .poster-far-right {
        width: clamp(74px, 17.5vw, 108px);
        height: clamp(115px, 26.5vw, 160px);
        background: #7CB342;
        color: #fff;
        right: calc(0px - clamp(59px, 14vw, 86px));
        bottom: clamp(65px, 14vw, 95px);
        z-index: 2;
    }
}

/* ================= FEATURE STRIP ================= */

.feature-strip {
    min-height: 52px;
    background: #C4351D;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 14px;
    font-size: clamp(11px, 2.8vw, 13px);
    font-weight: 800;
    color: #fff;
    padding: 10px 15px;
    flex-wrap: wrap;
    text-align: center;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.feature-strip div {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.feature-strip .dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    background: #F5A623;
    border-radius: 50%;
}

/* ================= APP DOWNLOAD / CREASED PAPER SECTION ================= */

.app-crease-section {
    background: linear-gradient(145deg, #EFA838 0%, #D49026 50%, #E8AC43 100%);
    background-image: 
        radial-gradient(ellipse at center, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0.08) 100%),
        repeating-linear-gradient(45deg, rgba(0,0,0,0.015) 0px, rgba(0,0,0,0.015) 2px, transparent 2px, transparent 10px);
    padding: 35px 20px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.app-crease-badge {
    display: inline-block;
    background: #A82E1B;
    color: #fff;
    padding: 6px 14px;
    transform: rotate(-3deg);
    font-size: 12px;
    font-weight: 900;
    letter-spacing: 0.5px;
    border-radius: 3px;
    box-shadow: 2px 3px 0px rgba(0,0,0,0.25);
    margin-bottom: 14px;
}

.app-crease-section h2 {
    font-size: clamp(22px, 5.8vw, 32px);
    font-weight: 900;
    line-height: 1.2;
    color: #111;
    max-width: 360px;
    margin: 0 auto 12px;
    letter-spacing: -0.3px;
}

.app-crease-section p {
    font-size: 13px;
    color: #222;
    max-width: 340px;
    margin: 0 auto 24px;
    line-height: 1.45;
    opacity: 0.95;
}

.app-store-btns {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.app-store-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    padding: 9px 14px;
    border-radius: 12px;
    text-decoration: none;
    color: #111;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    min-width: 145px;
    text-align: left;
}

.app-store-btn svg {
    flex-shrink: 0;
}

.app-store-btn .btn-text-small {
    font-size: 8px;
    font-weight: 800;
    color: #444;
    text-transform: uppercase;
    display: block;
    line-height: 1;
}

.app-store-btn .btn-text-large {
    font-size: 12px;
    font-weight: 900;
    color: #111;
    display: block;
    line-height: 1.2;
    margin-top: 1px;
}

/* ================= WHY POSTERGALI SECTION ================= */

.why-section {
    padding: 35px 16px 25px;
    background: #FAF8E9;
}

.sub-header-label {
    text-align: center;
    font-size: 12px;
    color: #555;
    margin-bottom: 4px;
    font-family: sans-serif;
    font-weight: 600;
}

.main-section-title {
    text-align: center;
    font-size: clamp(20px, 5.2vw, 28px);
    color: #A3660C;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 22px;
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
}

.folder-container {
    max-width: 420px;
    margin: 0 auto;
}

.folder-tabs {
    display: flex;
    gap: 4px;
    padding-left: 8px;
    margin-bottom: -1px;
}

.folder-tab {
    padding: 8px 16px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    font-size: 12px;
    font-weight: 900;
    border: none;
    cursor: pointer;
}

.folder-tab.tab-vendors {
    background: #F8E3B0;
    color: #222;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.05);
}

.folder-tab.tab-locals {
    background: #E0887A;
    color: #fff;
}

.folder-tab.tab-hiring {
    background: #E39D90;
    color: #fff;
}

.folder-card {
    background: #F8E3B0;
    border-radius: 16px;
    border-top-left-radius: 0px;
    padding: 14px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}

.folder-media-box {
    width: 100%;
    height: 195px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    background: #1e1208;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.folder-content-body {
    padding-top: 14px;
}

.folder-content-body h3 {
    font-size: 16px;
    font-weight: 900;
    color: #111;
    margin-bottom: 6px;
}

.folder-content-body p {
    font-size: 11.5px;
    color: #333;
    line-height: 1.45;
    margin-bottom: 14px;
}

.btn-folder-cta {
    width: 100%;
    background: linear-gradient(180deg, #F7BA35 0%, #EA9C1B 100%);
    border: 1px solid #D9880D;
    color: #fff;
    padding: 11px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    text-align: center;
    box-shadow: 0 4px 10px rgba(234, 156, 27, 0.35);
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    cursor: pointer;
}

.feature-grid-sub {
    max-width: 380px;
    margin: 20px auto 0;
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #444;
    line-height: 1.7;
    font-weight: 600;
}

.feature-grid-sub .green-highlight {
    color: #2E7D32;
    font-weight: 800;
}

/* ================= WHO IS IT FOR SECTION ================= */

.who-section {
    padding: 25px 16px 30px;
    background: #FAF8E9;
}

.red-hero-card {
    max-width: 400px;
    margin: 0 auto;
    background: linear-gradient(180deg, #D44230 0%, #C33524 100%);
    border-radius: 24px;
    padding: 24px 16px 18px;
    box-shadow: 0 12px 30px rgba(195, 53, 36, 0.28);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.raghav-phone-frame {
    width: 210px;
    background: #ffffff;
    border-radius: 28px;
    border: 4px solid #1a1a1a;
    padding: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    position: relative;
}

.phone-notch {
    width: 50px;
    height: 10px;
    background: #1a1a1a;
    border-radius: 10px;
    margin: 0 auto 8px;
}

.raghav-illustration {
    width: 100%;
    height: 130px;
    background: #FAF6ED;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    border: 1px solid #eee;
    position: relative;
    overflow: hidden;
}

.raghav-name-tag {
    text-align: center;
    margin-bottom: 8px;
}

.raghav-name-tag strong {
    display: block;
    font-size: 11px;
    color: #111;
    line-height: 1.1;
}

.raghav-name-tag span {
    font-size: 9px;
    color: #666;
}

.box-challenge {
    background: #FDEDED;
    border: 1px solid #F5C2C2;
    border-radius: 10px;
    padding: 6px 8px;
    margin-bottom: 6px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.box-challenge .icon {
    color: #D32F2F;
    font-size: 12px;
    line-height: 1;
}

.box-challenge .box-text {
    font-size: 8.5px;
    color: #880E4F;
    line-height: 1.25;
}

.box-challenge .box-text b {
    color: #B71C1C;
    display: block;
    font-size: 9px;
}

.box-solution {
    background: #E8F5E9;
    border: 1px solid #C8E6C9;
    border-radius: 10px;
    padding: 6px 8px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.box-solution .icon {
    color: #2E7D32;
    font-size: 12px;
    line-height: 1;
}

.box-solution .box-text {
    font-size: 8.5px;
    color: #1B5E20;
    line-height: 1.25;
}

.box-solution .box-text b {
    color: #1B5E20;
    display: block;
    font-size: 9px;
}

.dots-indicator {
    display: flex;
    gap: 6px;
    margin-top: 16px;
}

.dot-item {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #E66655;
}

.dot-item.active {
    background: #FFE0B2;
}

/* ================= FIVE PROMISES SECTION ================= */

.promises-section {
    padding: 30px 16px 40px;
    background: #FAF8E9;
}

.stack-cards-wrapper {
    max-width: 360px;
    margin: 20px auto 0;
    display: flex;
    flex-direction: column;
}

.stack-card {
    border-radius: 16px;
    padding: 16px 20px;
    box-shadow: 0 -4px 15px rgba(0,0,0,0.06), 0 6px 15px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    margin-top: -30px;
    position: relative;
    transition: transform 0.2s ease;
}

.stack-card:first-child {
    margin-top: 0;
}

.stack-card .card-num {
    font-size: 14px;
    font-weight: 900;
    opacity: 0.7;
    margin-bottom: 2px;
    font-family: monospace;
}

.stack-card h4 {
    font-size: 16px;
    font-weight: 900;
    margin-bottom: 3px;
    line-height: 1.25;
}

.stack-card p {
    font-size: 11px;
    opacity: 0.9;
    line-height: 1.35;
}

.card-1 { background: #FFF9E6; color: #5C4A00; z-index: 1; }
.card-2 { background: #FFE8B2; color: #523D00; z-index: 2; }
.card-3 { background: #FFD980; color: #473200; z-index: 3; }
.card-4 { background: #FFC84D; color: #3D2800; z-index: 4; }
.card-5 { background: #F7B228; color: #2E1B00; z-index: 5; box-shadow: 0 10px 25px rgba(247, 178, 40, 0.4); }

/* ================= HOW IT WORKS (STAMP CAROUSEL) ================= */

.how-works-section {
    padding: 20px 16px 40px;
    background: #FAF8E9;
    text-align: center;
}

.how-works-section p {
    font-size: 12px;
    color: #444;
    max-width: 320px;
    margin: 0 auto 20px;
}

.hands-phone-graphic {
    width: 180px;
    height: 170px;
    margin: 0 auto 15px;
    position: relative;
}

/* Serrated Stamp Ticket Card */
.stamp-card-container {
    max-width: 310px;
    margin: 0 auto;
    position: relative;
}

.stamp-card {
    background: #358A50;
    color: #fff;
    border-radius: 14px;
    padding: 20px 18px;
    text-align: center;
    position: relative;
    box-shadow: 0 8px 22px rgba(53, 138, 80, 0.25);
    border: 2px dashed rgba(255,255,255,0.4);
}

.stamp-card h3 {
    font-size: 16px;
    font-weight: 900;
    margin-bottom: 8px;
    color: #ffffff;
}

.stamp-card p {
    font-size: 11px;
    line-height: 1.45;
    color: #E8F5E9;
    margin: 0;
}

.stamp-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 14px;
}

.stamp-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #D9D2B5;
}

.stamp-dot.active {
    background: #F7BA35;
}

/* ================= GET IN TOUCH (CONTACT SECTION) ================= */

.contact-section {
    padding: 30px 16px 30px;
    background: #FAF8E9;
}

.contact-card-green {
    max-width: 400px;
    margin: 0 auto;
    background: #358A50;
    border-radius: 24px 24px 0 0;
    padding: 28px 18px 35px;
    color: #fff;
    position: relative;
    box-shadow: 0 10px 30px rgba(53, 138, 80, 0.2);
}

.contact-card-green h2 {
    font-size: 24px;
    font-weight: 900;
    margin-bottom: 8px;
    color: #ffffff;
}

.contact-card-green > p {
    font-size: 12px;
    line-height: 1.45;
    color: #E8F5E9;
    margin-bottom: 20px;
    opacity: 0.95;
}

.contact-info-pill {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 16px;
    padding: 12px 14px;
    margin-bottom: 22px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contact-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: bold;
}

.contact-icon-circle {
    width: 32px;
    height: 32px;
    background: #C4351D;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 14px;
    flex-shrink: 0;
}

/* Form Styles */
.contact-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    text-align: left;
}

.form-group label {
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
}

.input-wrapper {
    position: relative;
}

.input-wrapper input,
.input-wrapper textarea {
    width: 100%;
    background: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 12px 38px 12px 14px;
    font-size: 12px;
    color: #111;
    outline: none;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

.input-wrapper textarea {
    resize: none;
    height: 45px;
}

.input-wrapper .input-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    background: #FDEDED;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #C4351D;
}

/* Scallop Wavy Bottom Border */
.scallop-bottom-edge {
    width: 100%;
    height: 12px;
    background: radial-gradient(circle, transparent 6px, #358A50 7px);
    background-size: 16px 16px;
    background-position: 0 -8px;
    max-width: 400px;
    margin: 0 auto;
}

/* ================= FAQS SECTION ================= */

.faq-section {
    padding: 30px 16px 40px;
    background: #FAF8E9;
    max-width: 420px;
    margin: 0 auto;
}

.faq-header-badge {
    background: #F7A626;
    color: #ffffff;
    font-weight: 900;
    font-size: 16px;
    padding: 10px 22px;
    border-radius: 14px 14px 0 0;
    display: inline-block;
    margin-bottom: -1px;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}

.faq-accordion-box {
    background: #FFFFFF;
    border-radius: 0 16px 16px 16px;
    border: 1px solid #E5E0C2;
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    overflow: hidden;
}

.faq-item {
    border-bottom: 1px solid #F0ECD5;
    padding: 13px 16px;
    cursor: pointer;
    transition: background 0.2s;
}

.faq-item:last-child {
    border-bottom: none;
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    font-weight: bold;
    color: #222;
}

.faq-chevron {
    color: #C4351D;
    font-size: 10px;
    transition: transform 0.2s;
}

.faq-answer {
    display: none;
    font-size: 11px;
    color: #555;
    line-height: 1.4;
    padding-top: 8px;
}

.faq-item.active .faq-answer {
    display: block;
}

.faq-item.active .faq-chevron {
    transform: rotate(180deg);
}

/* ================= FOOTER SECTION ================= */

.footer-section {
    background: #FAF8E9;
    padding: 35px 20px 0;
    text-align: center;
    border-top: 1px solid #E5E0C2;
}

.footer-sign-wrap {
    margin: 0 auto 16px;
    width: 170px;
}

.footer-tagline {
    font-size: 11.5px;
    color: #444;
    max-width: 280px;
    margin: 0 auto 20px;
    line-height: 1.4;
}

.follow-title {
    font-size: 10px;
    font-weight: 900;
    color: #333;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.social-icons {
    display: flex;
    justify-content: center;
    gap: 14px;
    margin-bottom: 25px;
}

.social-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #FFFCE5;
    border: 2px solid #F7A626;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #F7A626;
    font-size: 16px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

.footer-bottom-bar {
    background: #F7A626;
    color: #ffffff;
    padding: 16px 20px;
    font-size: 11px;
    font-weight: bold;
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 30px;
}

.footer-bottom-bar a {
    color: #ffffff;
    text-decoration: none;
}

</style>
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
        
        <!-- 1st Poster (Far Left Dark Purple - 80% Hidden) -->
        <div class="poster-card poster-far-left">
            <div class="p-title">HIRING</div>
            <div class="p-sub">STORE MANAGER</div>
            <div class="p-badge">URGENT</div>
        </div>

        <!-- 3rd Poster (Magenta Pink - Jindal Garments) -->
        <div class="poster-card poster-left-low">
            <div class="p-title">Jindal Garments</div>
            <div class="p-badge" style="background: #FFEB3B; color: #880E4F;">FLAT 50% OFF</div>
            <div class="p-sub" style="margin-top: 4px;">ON WOMEN'S COLLECTION<br>Refresh your wardrobe with trendy styles</div>
        </div>

        <!-- 2nd Poster (Light Blue - Raj Electronics) -->
        <div class="poster-card poster-left-high">
            <div class="p-title">Raj Electronics</div>
            <div class="p-badge" style="background: #FFF; color: #0288D1;">SALE SALE SALE</div>
            <div class="p-sub" style="margin-top: 4px;">Best deals on electronics and appliances</div>
        </div>

        <!-- CENTRAL PHONE PREVIEW -->
        <div class="phone">
            <div class="phone-screen-content">
                <div class="phone-top-bar">
                    <div class="phone-brand">PosterGali</div>
                    <div class="phone-location">📍 Sarojini Nagar, New Delhi</div>
                </div>
                <div class="phone-banner">
                    <div>
                        <b>Refer & Win</b><br>Get up to 5 friends & credits
                    </div>
                </div>
                <div class="phone-tabs">
                    <div class="phone-tab active">Jobs</div>
                    <div class="phone-tab">Offers</div>
                </div>
                <div class="phone-cards-grid">
                    <div class="phone-mini-card pink">
                        <b>Jindal Garments</b><br>FLAT 50% OFF<br>Women's Collection
                    </div>
                    <div class="phone-mini-card blue">
                        <b>Raj Electronics</b><br>SALE SALE SALE<br>Best deals
                    </div>
                </div>
                <div class="phone-floating-add">+</div>
            </div>
        </div>

        <!-- 4th Poster (White/Yellow - We're Hiring) -->
        <div class="poster-card poster-right-low">
            <div class="p-title" style="color: #C4351D;">WE'RE HIRING</div>
            <div class="p-badge" style="background: #FFF59D; color: #111;">Jewellers</div>
            <div class="p-sub" style="margin-top: 4px; color: #333;"><b>DELIVERY BOY</b><br>FULL-TIME ₹12,000</div>
        </div>

        <!-- 5th Poster (Purple - Job Opening Om Jewellers) -->
        <div class="poster-card poster-right-high">
            <div class="p-title">JOB OPENING</div>
            <div class="p-sub">Om Jewellers</div>
            <div class="p-badge" style="background: #E1BEE7; color: #4A148C;">DELIVERY BOY</div>
            <div class="p-sub" style="margin-top: 4px;">FULL-TIME<br><b>₹12,000</b></div>
        </div>

        <!-- 6th Poster (Far Right Lime Green - 80% Hidden) -->
        <div class="poster-card poster-far-right">
            <div class="p-title">VERIFIED</div>
            <div class="p-sub">UP TO 50% OFF</div>
            <div class="p-badge">LIVE NOW</div>
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

                        <rect x="38" y="78" width="32" height="48" rx="4" fill="#AD1457"/>
                        <rect x="74" y="78" width="33" height="48" rx="4" fill="#0288D1"/>
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
            <rect x="60" y="70" width="28" height="40" rx="3" fill="#AD1457"/>
            <rect x="92" y="70" width="28" height="40" rx="3" fill="#0288D1"/>
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
        <h2>Get in touch</h2>
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
                <div>askpostergali@gmail.com</div>
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