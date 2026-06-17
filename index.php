<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skyline Hostel – Premium Student Living</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <style>
    /* ============================================================
       ROOT VARIABLES & THEME
    ============================================================ */
    :root {
        --sky-900: #0a1628;
        --sky-800: #0d1f3c;
        --sky-700: #112952;
        --sky-600: #1a3a6e;
        --accent:  #e8562a;
        --accent2: #f5a623;
        --gold:    #d4af37;
        --text-primary:   #0a1628;
        --text-secondary: #4a5568;
        --text-muted:     #718096;
        --bg-main:    #f8f9fc;
        --bg-card:    #ffffff;
        --border:     #e2e8f0;
        --shadow-sm:  0 2px 8px rgba(10,22,40,.06);
        --shadow-md:  0 8px 30px rgba(10,22,40,.10);
        --shadow-lg:  0 20px 60px rgba(10,22,40,.15);
        --radius:     16px;
        --radius-sm:  8px;
        --font-display: 'Syne', sans-serif;
        --font-body:    'DM Sans', sans-serif;
        --nav-height:   72px;
    }

    [data-theme="dark"] {
        --text-primary:   #f0f4ff;
        --text-secondary: #a0aec0;
        --text-muted:     #718096;
        --bg-main:    #07101f;
        --bg-card:    #0d1f3c;
        --border:     #1a2d4f;
        --shadow-sm:  0 2px 8px rgba(0,0,0,.3);
        --shadow-md:  0 8px 30px rgba(0,0,0,.4);
        --shadow-lg:  0 20px 60px rgba(0,0,0,.5);
    }

    /* ============================================================
       BASE
    ============================================================ */
    *, *::before, *::after { box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
        font-family: var(--font-body);
        background-color: var(--bg-main);
        color: var(--text-primary);
        line-height: 1.7;
        transition: background-color .3s, color .3s;
        overflow-x: hidden;
    }

    h1,h2,h3,h4,h5,h6 {
        font-family: var(--font-display);
        color: var(--text-primary);
    }

    a { text-decoration: none; transition: all .25s; }

    /* ============================================================
       NAVBAR
    ============================================================ */
    .navbar {
        height: var(--nav-height);
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--border);
        transition: all .3s;
        position: fixed; top: 0; width: 100%; z-index: 1000;
    }

    [data-theme="dark"] .navbar {
        background: rgba(7,16,31,.92);
    }

    .navbar.scrolled {
        box-shadow: var(--shadow-md);
    }

    .navbar-brand {
        font-family: var(--font-display);
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .brand-sky  { color: var(--sky-600); }
    .brand-line { color: var(--accent); }

    .nav-link {
        font-weight: 500;
        font-size: .9rem;
        color: var(--text-secondary) !important;
        padding: .5rem 1rem !important;
        border-radius: var(--radius-sm);
        position: relative;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0; left: 50%;
        transform: translateX(-50%) scaleX(0);
        width: 20px; height: 2px;
        background: var(--accent);
        border-radius: 2px;
        transition: transform .25s;
    }

    .nav-link:hover::after,
    .nav-link.active::after { transform: translateX(-50%) scaleX(1); }
    .nav-link:hover, .nav-link.active { color: var(--text-primary) !important; }

    .btn-nav-login {
        background: transparent;
        border: 1.5px solid var(--border);
        color: var(--text-primary) !important;
        padding: .45rem 1.2rem !important;
        border-radius: 100px;
        font-size: .875rem;
        font-weight: 600;
    }

    .btn-nav-login:hover {
        border-color: var(--accent);
        color: var(--accent) !important;
    }

    .btn-nav-register {
        background: linear-gradient(135deg, var(--accent), #c0410f);
        color: #fff !important;
        border: none;
        padding: .45rem 1.3rem !important;
        border-radius: 100px;
        font-size: .875rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(232,86,42,.3);
    }

    .btn-nav-register:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(232,86,42,.4);
    }

    .theme-toggle {
        background: var(--bg-card);
        border: 1.5px solid var(--border);
        color: var(--text-secondary);
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all .25s;
    }

    .theme-toggle:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    /* ============================================================
       HERO
    ============================================================ */
    .hero {
        min-height: 100vh;
        padding-top: var(--nav-height);
        background: linear-gradient(160deg,
            var(--sky-900) 0%,
            var(--sky-700) 50%,
            #1e3a6e 100%);
        position: relative;
        overflow: hidden;
        display: flex; align-items: center;
    }

    /* Decorative grid */
    .hero::before {
        content: '';
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
        background-size: 50px 50px;
        pointer-events: none;
    }

    /* Glow blobs */
    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        opacity: .35;
    }

    .hero-blob-1 {
        width: 600px; height: 600px;
        background: radial-gradient(circle, #e8562a, transparent);
        top: -200px; right: -200px;
    }

    .hero-blob-2 {
        width: 400px; height: 400px;
        background: radial-gradient(circle, #1a5cb5, transparent);
        bottom: -100px; left: -100px;
    }

    .hero-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.15);
        color: #fff;
        padding: .4rem 1rem;
        border-radius: 100px;
        font-size: .82rem;
        font-weight: 500;
        letter-spacing: .04em;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(10px);
    }

    .hero-badge span.dot {
        width: 6px; height: 6px;
        background: var(--accent2);
        border-radius: 50%;
        display: inline-block;
        animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .5; transform: scale(1.4); }
    }

    .hero-title {
        font-size: clamp(2.8rem, 6vw, 5rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.1;
        letter-spacing: -.03em;
        margin-bottom: 1.5rem;
    }

    .hero-title .highlight {
        color: var(--accent2);
        position: relative;
    }

    .hero-description {
        font-size: 1.1rem;
        color: rgba(255,255,255,.7);
        max-width: 520px;
        font-weight: 300;
        margin-bottom: 2.5rem;
        line-height: 1.8;
    }

    .hero-cta-group {
        display: flex; flex-wrap: wrap; gap: 1rem;
        align-items: center;
    }

    .btn-hero-primary {
        background: linear-gradient(135deg, var(--accent), #c0410f);
        color: #fff;
        border: none;
        padding: .9rem 2.2rem;
        border-radius: 100px;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: .01em;
        box-shadow: 0 8px 30px rgba(232,86,42,.4);
        transition: all .3s;
        display: inline-flex; align-items: center; gap: .5rem;
    }

    .btn-hero-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 40px rgba(232,86,42,.5);
        color: #fff;
    }

    .btn-hero-secondary {
        background: rgba(255,255,255,.08);
        color: #fff;
        border: 1.5px solid rgba(255,255,255,.25);
        padding: .9rem 2.2rem;
        border-radius: 100px;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: all .3s;
        display: inline-flex; align-items: center; gap: .5rem;
    }

    .btn-hero-secondary:hover {
        background: rgba(255,255,255,.15);
        border-color: rgba(255,255,255,.5);
        transform: translateY(-3px);
        color: #fff;
    }

    .hero-stats-row {
        display: flex; flex-wrap: wrap; gap: 2rem;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,.1);
    }

    .hero-stat-item {
        display: flex; flex-direction: column;
    }

    .hero-stat-num {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .hero-stat-num span { color: var(--accent2); }

    .hero-stat-label {
        font-size: .8rem;
        color: rgba(255,255,255,.55);
        font-weight: 400;
        margin-top: .25rem;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    /* Hero room cards preview */
    .hero-visual {
        position: relative;
        display: flex; justify-content: center; align-items: center;
    }

    .hero-card-stack {
        position: relative;
        width: 380px;
    }

    .hero-room-card {
        background: rgba(255,255,255,.06);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: var(--radius);
        padding: 1.5rem;
        color: #fff;
        transition: transform .4s;
    }

    .hero-room-card.card-back {
        position: absolute;
        width: 90%;
        top: -20px; right: -20px;
        opacity: .6;
        transform: rotate(4deg);
    }

    .hero-room-card.card-front {
        position: relative;
        z-index: 1;
    }

    .hero-room-card:hover { transform: translateY(-5px); }
    .hero-room-card.card-back:hover { transform: rotate(4deg) translateY(-5px); }

    .room-card-type {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(var(--accent), .2);
        background: rgba(232,86,42,.15);
        border: 1px solid rgba(232,86,42,.3);
        color: var(--accent2);
        padding: .3rem .8rem;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 1rem;
    }

    .room-card-number {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: .3rem;
    }

    .room-card-floor { font-size: .85rem; color: rgba(255,255,255,.6); }

    .room-card-features {
        display: flex; flex-wrap: wrap; gap: .4rem;
        margin-top: 1rem;
    }

    .feature-chip {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.75);
        padding: .2rem .7rem;
        border-radius: 100px;
        font-size: .75rem;
    }

    .room-card-price {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 1.2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,.1);
    }

    .price-amount {
        font-family: var(--font-display);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--accent2);
    }

    .price-label { font-size: .75rem; color: rgba(255,255,255,.5); }

    .availability-badge {
        background: rgba(52,211,153,.15);
        border: 1px solid rgba(52,211,153,.3);
        color: #34d399;
        padding: .25rem .7rem;
        border-radius: 100px;
        font-size: .72rem;
        font-weight: 600;
    }

    /* Floating elements */
    .float-badge {
        position: absolute;
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: .75rem 1.2rem;
        box-shadow: var(--shadow-lg);
        display: flex; align-items: center; gap: .6rem;
        font-size: .82rem;
        font-weight: 600;
        color: var(--text-primary);
        animation: float-y 3s ease-in-out infinite;
    }

    .float-badge-1 { top: 10%; left: -30px; animation-delay: 0s; }
    .float-badge-2 { bottom: 15%; right: -20px; animation-delay: 1.5s; }

    @keyframes float-y {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-8px); }
    }

    .float-badge .icon { font-size: 1.2rem; }

    /* ============================================================
       SECTION BASE
    ============================================================ */
    section { padding: 100px 0; }

    .section-tag {
        display: inline-flex; align-items: center; gap: .5rem;
        color: var(--accent);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-bottom: .75rem;
    }

    .section-tag::before {
        content: '';
        width: 24px; height: 2px;
        background: var(--accent);
        border-radius: 2px;
    }

    .section-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -.03em;
        line-height: 1.2;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }

    .section-subtitle {
        font-size: 1.05rem;
        color: var(--text-secondary);
        max-width: 540px;
        font-weight: 300;
        line-height: 1.8;
    }

    /* ============================================================
       FEATURES SECTION
    ============================================================ */
    .features-section {
        background: var(--bg-main);
    }

    .feature-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        height: 100%;
        transition: all .35s cubic-bezier(.175,.885,.32,1.275);
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .35s;
    }

    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: transparent;
    }

    .feature-card:hover::before { transform: scaleX(1); }

    .feature-icon-wrap {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1.2rem;
        transition: transform .3s;
    }

    .feature-card:hover .feature-icon-wrap { transform: scale(1.1) rotate(-5deg); }

    .feature-icon-blue   { background: rgba(59,130,246,.1);  color: #3b82f6; }
    .feature-icon-orange { background: rgba(232,86,42,.1);   color: var(--accent); }
    .feature-icon-green  { background: rgba(52,211,153,.1);  color: #34d399; }
    .feature-icon-purple { background: rgba(139,92,246,.1);  color: #8b5cf6; }
    .feature-icon-gold   { background: rgba(212,175,55,.1);  color: var(--gold); }
    .feature-icon-pink   { background: rgba(236,72,153,.1);  color: #ec4899; }

    .feature-title {
        font-family: var(--font-display);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: .6rem;
    }

    .feature-desc {
        font-size: .9rem;
        color: var(--text-secondary);
        line-height: 1.7;
        margin: 0;
    }

    /* ============================================================
       STATS SECTION
    ============================================================ */
    .stats-section {
        background: linear-gradient(135deg, var(--sky-900), var(--sky-700));
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
        background-size: 32px 32px;
    }

    .stat-card {
        text-align: center;
        padding: 2rem 1rem;
        position: relative;
    }

    .stat-number {
        font-family: var(--font-display);
        font-size: 3.5rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .stat-number span { color: var(--accent2); }

    .stat-label {
        font-size: .9rem;
        color: rgba(255,255,255,.6);
        margin-top: .5rem;
        font-weight: 400;
        letter-spacing: .02em;
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: .75rem;
        color: rgba(255,255,255,.3);
    }

    /* ============================================================
       ROOMS PREVIEW SECTION
    ============================================================ */
    .rooms-section { background: var(--bg-main); }

    .room-preview-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all .35s;
        height: 100%;
    }

    .room-preview-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: transparent;
    }

    .room-img-placeholder {
        height: 200px;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem;
        position: relative;
        overflow: hidden;
    }

    .room-img-1 { background: linear-gradient(135deg, #0a1628, #1a3a6e); }
    .room-img-2 { background: linear-gradient(135deg, #1a1a2e, #16213e); }
    .room-img-3 { background: linear-gradient(135deg, #0f3460, #533483); }
    .room-img-4 { background: linear-gradient(135deg, #162447, #1f4068); }

    .room-img-placeholder .room-number-bg {
        position: absolute;
        font-family: var(--font-display);
        font-size: 6rem;
        font-weight: 800;
        color: rgba(255,255,255,.06);
        right: 10px; bottom: -10px;
        line-height: 1;
    }

    .room-type-tag {
        position: absolute;
        top: 1rem; left: 1rem;
        background: rgba(255,255,255,.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,.2);
        color: #fff;
        padding: .3rem .8rem;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .06em;
    }

    .available-dot {
        position: absolute;
        top: 1rem; right: 1rem;
        width: 10px; height: 10px;
        background: #34d399;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(52,211,153,.2);
    }

    .room-card-body { padding: 1.5rem; }

    .room-number {
        font-family: var(--font-display);
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: .3rem;
    }

    .room-floor { font-size: .85rem; color: var(--text-muted); margin-bottom: 1rem; }

    .room-amenities {
        display: flex; flex-wrap: wrap; gap: .35rem;
        margin-bottom: 1.2rem;
    }

    .amenity-chip {
        background: var(--bg-main);
        border: 1px solid var(--border);
        color: var(--text-secondary);
        padding: .2rem .65rem;
        border-radius: 100px;
        font-size: .72rem;
        font-weight: 500;
    }

    .room-footer {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .room-price {
        font-family: var(--font-display);
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--accent);
    }

    .room-price small {
        font-family: var(--font-body);
        font-size: .75rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    .btn-room-apply {
        background: var(--sky-700);
        color: #fff;
        border: none;
        padding: .45rem 1.1rem;
        border-radius: 100px;
        font-size: .82rem;
        font-weight: 600;
        transition: all .25s;
    }

    .btn-room-apply:hover {
        background: var(--accent);
        color: #fff;
        transform: translateY(-1px);
    }

    /* ============================================================
       HOW IT WORKS
    ============================================================ */
    .how-section { background: var(--bg-card); }

    .step-card {
        text-align: center;
        padding: 2rem 1.5rem;
        position: relative;
    }

    .step-number {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--sky-700), var(--sky-600));
        color: #fff;
        font-family: var(--font-display);
        font-size: 1.4rem;
        font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.2rem;
        box-shadow: 0 8px 25px rgba(10,22,40,.2);
        position: relative;
        z-index: 1;
    }

    .step-connector {
        position: absolute;
        top: calc(2rem + 32px);
        left: calc(50% + 32px);
        width: calc(100% - 64px);
        height: 2px;
        background: linear-gradient(90deg, var(--sky-600), transparent);
        z-index: 0;
    }

    .col-lg-3:last-child .step-connector { display: none; }

    .step-title {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: .5rem;
    }

    .step-desc { font-size: .88rem; color: var(--text-secondary); line-height: 1.7; }

    /* ============================================================
       TESTIMONIALS
    ============================================================ */
    .testimonials-section { background: var(--bg-main); }

    .testimonial-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        height: 100%;
        transition: all .3s;
        position: relative;
    }

    .testimonial-card::before {
        content: '\201C';
        position: absolute;
        top: 1rem; right: 1.5rem;
        font-family: Georgia, serif;
        font-size: 5rem;
        color: var(--border);
        line-height: 1;
    }

    .testimonial-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .testimonial-stars { color: var(--accent2); margin-bottom: 1rem; font-size: .9rem; }

    .testimonial-text {
        font-size: .95rem;
        color: var(--text-secondary);
        line-height: 1.8;
        margin-bottom: 1.5rem;
        font-style: italic;
    }

    .testimonial-author {
        display: flex; align-items: center; gap: .75rem;
    }

    .author-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .avatar-blue   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .avatar-orange { background: linear-gradient(135deg, var(--accent), #c0410f); }
    .avatar-green  { background: linear-gradient(135deg, #34d399, #059669); }

    .author-name {
        font-family: var(--font-display);
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
    }

    .author-course { font-size: .8rem; color: var(--text-muted); }

    /* ============================================================
       CTA BANNER
    ============================================================ */
    .cta-section {
        background: linear-gradient(135deg, var(--sky-900) 0%, var(--sky-700) 50%, #1e3a6e 100%);
        position: relative; overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 24px 24px;
    }

    .cta-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        color: #fff;
        letter-spacing: -.03em;
    }

    .cta-subtitle { color: rgba(255,255,255,.65); font-size: 1.05rem; font-weight: 300; }

    /* ============================================================
       FOOTER
    ============================================================ */
    .footer {
        background: var(--sky-900);
        color: rgba(255,255,255,.65);
        padding: 70px 0 30px;
        border-top: 1px solid rgba(255,255,255,.05);
    }

    .footer-brand-name {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
    }

    .footer-desc { font-size: .9rem; line-height: 1.8; margin: 1rem 0 1.5rem; }

    .footer-social a {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.1);
        display: inline-flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,.65);
        font-size: .85rem;
        transition: all .25s;
        margin-right: .4rem;
    }

    .footer-social a:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        transform: translateY(-2px);
    }

    .footer-heading {
        font-family: var(--font-display);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #fff;
        margin-bottom: 1.2rem;
    }

    .footer-links li { margin-bottom: .5rem; }

    .footer-links a {
        color: rgba(255,255,255,.55);
        font-size: .88rem;
        transition: color .25s;
    }

    .footer-links a:hover { color: var(--accent2); }

    .footer-contact-item {
        display: flex; align-items: flex-start; gap: .75rem;
        margin-bottom: .75rem;
    }

    .footer-contact-item .icon {
        color: var(--accent);
        font-size: .9rem;
        margin-top: .15rem;
        flex-shrink: 0;
    }

    .footer-contact-item span { font-size: .88rem; }

    .footer-divider {
        border-color: rgba(255,255,255,.07);
        margin: 2rem 0 1.5rem;
    }

    .footer-bottom {
        display: flex; flex-wrap: wrap; justify-content: space-between;
        align-items: center; gap: 1rem;
    }

    .footer-copy { font-size: .82rem; }

    .footer-bottom-links a {
        color: rgba(255,255,255,.5);
        font-size: .82rem;
        margin-left: 1.2rem;
        transition: color .25s;
    }

    .footer-bottom-links a:hover { color: var(--accent2); }

    /* ============================================================
       TOAST NOTIFICATIONS
    ============================================================ */
    .toast-container-custom {
        position: fixed;
        bottom: 24px; right: 24px;
        z-index: 9999;
        display: flex; flex-direction: column; gap: .6rem;
    }

    .toast-item {
        display: flex; align-items: center; gap: .75rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: .9rem 1.3rem;
        box-shadow: var(--shadow-lg);
        min-width: 300px;
        animation: toast-in .35s cubic-bezier(.175,.885,.32,1.275) forwards;
        border-left: 4px solid var(--accent);
    }

    @keyframes toast-in {
        from { opacity: 0; transform: translateX(60px) scale(.9); }
        to   { opacity: 1; transform: translateX(0) scale(1); }
    }

    /* ============================================================
       BACK TO TOP
    ============================================================ */
    #backToTop {
        position: fixed;
        bottom: 24px; left: 24px;
        width: 44px; height: 44px;
        background: var(--sky-700);
        color: #fff;
        border: none;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        z-index: 999;
        opacity: 0; pointer-events: none;
        transition: all .3s;
        box-shadow: var(--shadow-md);
    }

    #backToTop.visible { opacity: 1; pointer-events: auto; }
    #backToTop:hover { background: var(--accent); transform: translateY(-3px); }

    /* ============================================================
       SCROLL ANIMATIONS
    ============================================================ */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity .6s ease, transform .6s ease;
    }

    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-delay-1 { transition-delay: .1s; }
    .reveal-delay-2 { transition-delay: .2s; }
    .reveal-delay-3 { transition-delay: .3s; }
    .reveal-delay-4 { transition-delay: .4s; }

    /* ============================================================
       UTILITIES
    ============================================================ */
    .fw-800 { font-weight: 800; }

    @media (max-width: 768px) {
        .hero-card-stack { display: none; }
        .hero-stats-row { gap: 1.2rem; }
        .step-connector { display: none; }
        .float-badge { display: none; }
    }
    </style>
</head>
<body>

<!-- ============================================================
     NAVBAR
============================================================ -->
<nav class="navbar navbar-expand-lg" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-sky">Sky</span><span class="brand-line">line</span>
            <span style="color:var(--text-secondary);font-size:.85rem;font-weight:500;"> Hostel</span>
        </a>

        <div class="d-flex align-items-center gap-2 d-lg-none">
            <button class="theme-toggle" id="themeToggle" title="Toggle Theme">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>
            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars" style="color:var(--text-primary);font-size:1.1rem;"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button class="theme-toggle d-none d-lg-flex" id="themeToggleLg" title="Toggle Theme">
                    <i class="fa-solid fa-moon" id="themeIconLg"></i>
                </button>
                <a href="student/login.php"   class="btn-nav-login">Student Login</a>
                <a href="student/register.php" class="btn-nav-register">Apply Now</a>
            </div>
        </div>
    </div>
</nav>

<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="hero" id="home">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>

    <div class="container py-5">
        <div class="row align-items-center g-5">

            <!-- Left Content -->
            <div class="col-lg-6">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Now Accepting Applications for 2024–25
                </div>

                <h1 class="hero-title">
                    Your Home<br>
                    Away From<br>
                    <span class="highlight">Home.</span>
                </h1>

                <p class="hero-description">
                    Experience premium student living at Skyline Hostel — where modern amenities, vibrant community, and academic focus come together in one address.
                </p>

                <div class="hero-cta-group">
                    <a href="student/register.php" class="btn-hero-primary">
                        <i class="fa-solid fa-rocket"></i>
                        Apply for Room
                    </a>
                    <a href="rooms.php" class="btn-hero-secondary">
                        <i class="fa-regular fa-eye"></i>
                        Browse Rooms
                    </a>
                </div>

                <div class="hero-stats-row">
                    <div class="hero-stat-item">
                        <div class="hero-stat-num counter" data-target="200">0<span>+</span></div>
                        <div class="hero-stat-label">Students Housed</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num counter" data-target="50">0<span>+</span></div>
                        <div class="hero-stat-label">Rooms Available</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num counter" data-target="5">0<span>+</span></div>
                        <div class="hero-stat-label">Years of Excellence</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num">4.9<span>★</span></div>
                        <div class="hero-stat-label">Student Rating</div>
                    </div>
                </div>
            </div>

            <!-- Right: Room Card Stack -->
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="hero-card-stack">
                        <!-- Back Card -->
                        <div class="hero-room-card card-back">
                            <div class="room-card-type"><i class="fa-solid fa-bed"></i> Double Room</div>
                            <div class="room-card-number">201</div>
                            <div class="room-card-floor">First Floor · 2 Beds</div>
                        </div>

                        <!-- Front Card -->
                        <div class="hero-room-card card-front">
                            <div class="room-card-type"><i class="fa-solid fa-star"></i> Single Room</div>
                            <div class="room-card-number">101</div>
                            <div class="room-card-floor">Ground Floor · Attached Bath</div>
                            <div class="room-card-features">
                                <span class="feature-chip"><i class="fa-solid fa-snowflake"></i> AC</span>
                                <span class="feature-chip"><i class="fa-solid fa-wifi"></i> WiFi</span>
                                <span class="feature-chip"><i class="fa-solid fa-bath"></i> Attached</span>
                            </div>
                            <div class="room-card-price">
                                <div>
                                    <div class="price-amount">Rs. 8,000</div>
                                    <div class="price-label">per month</div>
                                </div>
                                <div class="availability-badge"><i class="fa-solid fa-circle" style="font-size:.5rem;"></i> Available</div>
                            </div>
                        </div>

                        <!-- Floating badges -->
                        <div class="float-badge float-badge-1">
                            <span class="icon">🏆</span>
                            <div>
                                <div style="font-size:.8rem;font-weight:700;">Best Hostel 2024</div>
                                <div style="font-size:.7rem;color:var(--text-muted);">Student Choice Award</div>
                            </div>
                        </div>
                        <div class="float-badge float-badge-2">
                            <span class="icon">⚡</span>
                            <div>
                                <div style="font-size:.8rem;font-weight:700;">24/7 Support</div>
                                <div style="font-size:.7rem;color:var(--text-muted);">Always here for you</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES SECTION
============================================================ -->
<section class="features-section" id="features">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-tag justify-content-center">Why Choose Us</div>
                <h2 class="section-title">Everything You Need<br>Under One Roof</h2>
                <p class="section-subtitle mx-auto">From cozy rooms to cutting-edge amenities, we've built the perfect environment for you to thrive academically and personally.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $features = [
                ['icon'=>'fa-wifi',             'wrap'=>'feature-icon-blue',   'title'=>'High-Speed WiFi',      'desc'=>'Blazing-fast internet connectivity across the entire hostel — from your room to common areas.'],
                ['icon'=>'fa-shield-halved',     'wrap'=>'feature-icon-orange', 'title'=>'24/7 Security',       'desc'=>'Round-the-clock security with CCTV surveillance and trained guards to keep you safe.'],
                ['icon'=>'fa-utensils',          'wrap'=>'feature-icon-green',  'title'=>'Quality Dining',      'desc'=>'Nutritious meals prepared by professional chefs, with multiple options to suit every palate.'],
                ['icon'=>'fa-bolt',              'wrap'=>'feature-icon-gold',   'title'=>'Power Backup',        'desc'=>'Uninterrupted power supply with generator backup so you never miss a beat.'],
                ['icon'=>'fa-book-open',         'wrap'=>'feature-icon-purple', 'title'=>'Study Rooms',         'desc'=>'Quiet, well-lit study rooms available around the clock for focused academic sessions.'],
                ['icon'=>'fa-dumbbell',          'wrap'=>'feature-icon-pink',   'title'=>'Fitness Center',      'desc'=>'A fully equipped gym so you can maintain a healthy lifestyle without leaving the hostel.'],
            ];
            foreach ($features as $i => $f): ?>
            <div class="col-md-6 col-lg-4 reveal reveal-delay-<?= ($i % 4) + 1 ?>">
                <div class="feature-card">
                    <div class="feature-icon-wrap <?= $f['wrap'] ?>">
                        <i class="fa-solid <?= $f['icon'] ?>"></i>
                    </div>
                    <h5 class="feature-title"><?= $f['title'] ?></h5>
                    <p class="feature-desc"><?= $f['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS SECTION
============================================================ -->
<section class="stats-section py-5" id="stats">
    <div class="container">
        <div class="row g-4">
            <?php
            $stats = [
                ['icon'=>'fa-users',       'num'=>200, 'suffix'=>'+', 'label'=>'Happy Students'],
                ['icon'=>'fa-door-open',   'num'=>50,  'suffix'=>'+', 'label'=>'Total Rooms'],
                ['icon'=>'fa-star',        'num'=>4.9, 'suffix'=>'★', 'label'=>'Average Rating', 'fixed'=>true],
                ['icon'=>'fa-calendar',    'num'=>5,   'suffix'=>'+', 'label'=>'Years Running'],
            ];
            foreach ($stats as $s): ?>
            <div class="col-6 col-md-3 reveal">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid <?= $s['icon'] ?>"></i></div>
                    <div class="stat-number <?= empty($s['fixed']) ? 'counter' : '' ?>" <?= empty($s['fixed']) ? 'data-target="'.$s['num'].'"' : '' ?>>
                        <?= empty($s['fixed']) ? '0' : $s['num'] ?>
                        <span><?= $s['suffix'] ?></span>
                    </div>
                    <div class="stat-label"><?= $s['label'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     ROOMS PREVIEW SECTION
============================================================ -->
<section class="rooms-section" id="rooms">
    <div class="container">
        <div class="row justify-content-between align-items-end mb-5">
            <div class="col-lg-6">
                <div class="section-tag">Available Rooms</div>
                <h2 class="section-title">Choose Your<br>Perfect Space</h2>
                <p class="section-subtitle">All rooms are fully furnished, clean, and maintained to the highest standards for your comfort.</p>
            </div>
            <div class="col-lg-auto">
                <a href="rooms.php" class="btn-hero-secondary" style="color:var(--text-primary);border-color:var(--border);">
                    View All Rooms <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php
            require_once 'includes/db.php';
            $roomsRes = $conn->query("SELECT * FROM rooms WHERE status='available' ORDER BY monthly_fee ASC LIMIT 4");
            $roomColors = ['room-img-1','room-img-2','room-img-3','room-img-4'];
            $i = 0;
            if ($roomsRes && $roomsRes->num_rows > 0):
                while ($room = $roomsRes->fetch_assoc()):
                    $amenities = explode(',', $room['amenities']);
                    $spotsLeft  = $room['capacity'] - $room['occupied'];
            ?>
            <div class="col-md-6 col-lg-3 reveal reveal-delay-<?= ($i % 4) + 1 ?>">
                <div class="room-preview-card">
                    <div class="room-img-placeholder <?= $roomColors[$i % 4] ?>">
                        <span class="room-type-tag"><?= htmlspecialchars($room['room_type']) ?></span>
                        <div class="available-dot" title="Available"></div>
                        <div class="room-number-bg"><?= htmlspecialchars($room['room_number']) ?></div>
                        <i class="fa-solid fa-bed" style="color:rgba(255,255,255,.2);font-size:3rem;"></i>
                    </div>
                    <div class="room-card-body">
                        <div class="room-number">Room <?= htmlspecialchars($room['room_number']) ?></div>
                        <div class="room-floor">
                            <i class="fa-solid fa-layer-group"></i>
                            <?= htmlspecialchars($room['floor']) ?> &bull;
                            <?= $room['capacity'] ?> bed<?= $room['capacity'] > 1 ? 's' : '' ?> &bull;
                            <?= $spotsLeft ?> spot<?= $spotsLeft > 1 ? 's' : '' ?> left
                        </div>
                        <div class="room-amenities">
                            <?php foreach (array_slice($amenities, 0, 3) as $am): ?>
                            <span class="amenity-chip"><?= trim(htmlspecialchars($am)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="room-footer">
                            <div class="room-price">
                                <?= formatMoney($room['monthly_fee']) ?>
                                <br><small>/ month</small>
                            </div>
                            <a href="student/register.php" class="btn-room-apply">Apply</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                $i++;
                endwhile;
            else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-door-open fa-3x" style="color:var(--border);"></i>
                <p class="mt-3" style="color:var(--text-muted);">Rooms will be listed soon. Check back later!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     HOW IT WORKS
============================================================ -->
<section class="how-section" id="how">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-tag justify-content-center">Simple Process</div>
                <h2 class="section-title">Get a Room in<br>4 Easy Steps</h2>
            </div>
        </div>

        <div class="row g-0">
            <?php
            $steps = [
                ['num'=>'01','icon'=>'fa-user-plus',  'title'=>'Register Account',   'desc'=>'Create your student account in minutes with your basic details.'],
                ['num'=>'02','icon'=>'fa-file-alt',   'title'=>'Submit Application', 'desc'=>'Fill in your preferences and submit your room application online.'],
                ['num'=>'03','icon'=>'fa-check-circle','title'=>'Admin Approval',    'desc'=>'Our team reviews your application and allocates the best room.'],
                ['num'=>'04','icon'=>'fa-home',       'title'=>'Move In!',           'desc'=>'Receive your room details and check in on your arrival date.'],
            ];
            foreach ($steps as $step): ?>
            <div class="col-6 col-lg-3 reveal">
                <div class="step-card">
                    <div class="step-connector"></div>
                    <div class="step-number"><?= $step['num'] ?></div>
                    <h5 class="step-title"><?= $step['title'] ?></h5>
                    <p class="step-desc"><?= $step['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
============================================================ -->
<section class="testimonials-section" id="testimonials">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-tag justify-content-center">Student Reviews</div>
                <h2 class="section-title">What Our Students<br>Are Saying</h2>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $testimonials = [
                ['name'=>'Ahmed Raza',    'course'=>'BS Computer Science, Year 3', 'avatar'=>'AR', 'class'=>'avatar-blue',   'rating'=>5, 'text'=>'Skyline Hostel completely transformed my university experience. The WiFi is reliable, the rooms are spotless, and management is always ready to help. Best decision I made!'],
                ['name'=>'Sara Khan',     'course'=>'BBA Marketing, Year 2',       'avatar'=>'SK', 'class'=>'avatar-orange', 'rating'=>5, 'text'=>'The study rooms are incredible — quiet, well-lit, and accessible 24/7. My grades improved significantly since moving here. Highly recommend to every student.'],
                ['name'=>'Usman Malik',   'course'=>'BE Civil Engineering, Year 4', 'avatar'=>'UM', 'class'=>'avatar-green',  'rating'=>5, 'text'=>'Four years and I\'ve never wanted to leave! The community here is amazing, the facilities are top-notch, and the management team actually listens to student concerns.'],
            ];
            foreach ($testimonials as $i => $t): ?>
            <div class="col-md-4 reveal reveal-delay-<?= $i + 1 ?>">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for ($s=0; $s<$t['rating']; $s++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"<?= $t['text'] ?>"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar <?= $t['class'] ?>"><?= $t['avatar'] ?></div>
                        <div>
                            <div class="author-name"><?= $t['name'] ?></div>
                            <div class="author-course"><?= $t['course'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA SECTION
============================================================ -->
<section class="cta-section py-5" id="apply">
    <div class="container position-relative py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="section-tag" style="color:rgba(255,255,255,.6);">Limited Seats Available</div>
                <h2 class="cta-title">Ready to Call<br>Skyline Home?</h2>
                <p class="cta-subtitle mt-2">Join hundreds of students who have made Skyline Hostel their second home. Applications are open — don't miss your spot!</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex flex-wrap gap-3 justify-content-lg-end">
                    <a href="student/register.php" class="btn-hero-primary">
                        <i class="fa-solid fa-rocket"></i> Apply Now — It's Free
                    </a>
                    <a href="contact.php" class="btn-hero-secondary">
                        <i class="fa-solid fa-phone"></i> Talk to Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FOOTER
============================================================ -->
<footer class="footer">
    <div class="container">
        <div class="row g-5">

            <!-- Brand -->
            <div class="col-lg-4">
                <div class="footer-brand-name">
                    <span style="color:var(--accent2);">Sky</span>line Hostel
                </div>
                <p class="footer-desc">Premium student accommodation designed for academic success, personal growth, and community living in the heart of the city.</p>
                <div class="footer-social">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Quick Links</div>
                <ul class="footer-links list-unstyled mb-0">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="student/login.php">Student Login</a></li>
                    <li><a href="admin/login.php">Admin Login</a></li>
                </ul>
            </div>

            <!-- Room Types -->
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Room Types</div>
                <ul class="footer-links list-unstyled mb-0">
                    <li><a href="rooms.php?type=Single">Single Room</a></li>
                    <li><a href="rooms.php?type=Double">Double Room</a></li>
                    <li><a href="rooms.php?type=Triple">Triple Room</a></li>
                    <li><a href="rooms.php?type=Quad">Quad Room</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-4">
                <div class="footer-heading">Get In Touch</div>
                <div class="footer-contact-item">
                    <span class="icon"><i class="fa-solid fa-location-dot"></i></span>
                    <span>Block 7, Near University Road,<br>Karachi, Sindh, Pakistan</span>
                </div>
                <div class="footer-contact-item">
                    <span class="icon"><i class="fa-solid fa-phone"></i></span>
                    <span>+92 300 123 4567</span>
                </div>
                <div class="footer-contact-item">
                    <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                    <span>info@skylinehostel.com</span>
                </div>
                <div class="footer-contact-item">
                    <span class="icon"><i class="fa-solid fa-clock"></i></span>
                    <span>Reception: Mon–Sat, 8am–8pm</span>
                </div>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="footer-bottom">
            <p class="footer-copy mb-0">
                &copy; <?= date('Y') ?> Skyline Hostel Management System. All rights reserved.
            </p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Use</a>
                <a href="#">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button id="backToTop" title="Back to Top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- Toast Container -->
<div class="toast-container-custom" id="toastContainer"></div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ============================================================
// THEME TOGGLE
// ============================================================
const html = document.documentElement;
const stored = localStorage.getItem('skyline-theme') || 'light';
html.setAttribute('data-theme', stored);

function updateIcons(theme) {
    document.querySelectorAll('#themeIcon, #themeIconLg').forEach(el => {
        el.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    });
}
updateIcons(stored);

document.querySelectorAll('#themeToggle, #themeToggleLg').forEach(btn => {
    btn.addEventListener('click', () => {
        const cur = html.getAttribute('data-theme');
        const next = cur === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('skyline-theme', next);
        updateIcons(next);
    });
});

// ============================================================
// NAVBAR SCROLL EFFECT
// ============================================================
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 20);
    document.getElementById('backToTop').classList.toggle('visible', window.scrollY > 400);
});

document.getElementById('backToTop').addEventListener('click', () =>
    window.scrollTo({ top: 0, behavior: 'smooth' })
);

// ============================================================
// SCROLL REVEAL
// ============================================================
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.15 });

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

// ============================================================
// COUNTER ANIMATION
// ============================================================
function animateCounter(el) {
    const target = parseFloat(el.dataset.target);
    const suffix = el.querySelector('span')?.textContent || '';
    const isFloat = !Number.isInteger(target);
    const duration = 1800;
    const step = 16;
    const increment = target / (duration / step);
    let current = 0;
    const timer = setInterval(() => {
        current = Math.min(current + increment, target);
        const val = isFloat ? current.toFixed(1) : Math.floor(current);
        el.innerHTML = val + '<span>' + suffix + '</span>';
        if (current >= target) clearInterval(timer);
    }, step);
}

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting && !e.target.dataset.counted) {
            e.target.dataset.counted = 'true';
            animateCounter(e.target);
        }
    });
}, { threshold: .5 });

document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));

// ============================================================
// TOAST HELPER
// ============================================================
function showToast(message, type = 'info') {
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast-item';
    toast.innerHTML = `<span style="font-size:1.2rem">${icons[type]||icons.info}</span>
        <span style="flex:1;font-size:.88rem">${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1rem;">&times;</button>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Show PHP session toast if present
<?php if (isset($_SESSION['toast'])): ?>
showToast('<?= addslashes($_SESSION['toast']['message']) ?>', '<?= $_SESSION['toast']['type'] ?>');
<?php unset($_SESSION['toast']); endif; ?>

// Demo welcome toast on first visit
if (!sessionStorage.getItem('welcomed')) {
    setTimeout(() => {
        showToast('Welcome to Skyline Hostel! 🏠 Applications are open.', 'info');
        sessionStorage.setItem('welcomed', '1');
    }, 1500);
}
</script>

</body>
</html>