<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — Skyline Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    :root{--sky-900:#0a1628;--sky-700:#112952;--sky-600:#1a3a6e;--accent:#e8562a;--accent2:#f5a623;--bg:#f8f9fc;--card:#fff;--border:#e2e8f0;--text:#0a1628;--muted:#718096;--font-d:'Syne',sans-serif;--font-b:'DM Sans',sans-serif;--radius:16px;--nav-height:72px;}
    [data-theme="dark"]{--bg:#07101f;--card:#0d1f3c;--border:#1a2d4f;--text:#f0f4ff;--muted:#718096;}
    *{box-sizing:border-box;margin:0;padding:0;}
    html{scroll-behavior:smooth;}
    body{font-family:var(--font-b);background:var(--bg);color:var(--text);transition:.3s;}
    a{text-decoration:none;transition:all .25s;}
    /* NAVBAR */
    .navbar{height:var(--nav-height);background:rgba(255,255,255,.92);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:fixed;top:0;width:100%;z-index:1000;transition:all .3s;}
    [data-theme="dark"] .navbar{background:rgba(7,16,31,.92);}
    .navbar.scrolled{box-shadow:0 8px 30px rgba(10,22,40,.1);}
    .navbar-brand{font-family:var(--font-d);font-size:1.45rem;font-weight:800;}
    .brand-sky{color:var(--sky-600);}.brand-line{color:var(--accent);}
    .nav-link{font-weight:500;font-size:.9rem;color:var(--muted) !important;padding:.5rem 1rem !important;border-radius:8px;}
    .nav-link:hover,.nav-link.active{color:var(--text) !important;}
    .btn-nav-login{background:transparent;border:1.5px solid var(--border);color:var(--text) !important;padding:.45rem 1.2rem !important;border-radius:100px;font-size:.875rem;font-weight:600;}
    .btn-nav-login:hover{border-color:var(--accent);color:var(--accent) !important;}
    .btn-nav-register{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff !important;border:none;padding:.45rem 1.3rem !important;border-radius:100px;font-size:.875rem;font-weight:600;box-shadow:0 4px 15px rgba(232,86,42,.3);}
    .btn-nav-register:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,86,42,.4);}
    .theme-toggle{background:var(--card);border:1.5px solid var(--border);color:var(--muted);width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .25s;}
    .theme-toggle:hover{border-color:var(--accent);color:var(--accent);}
    /* PAGE HEADER */
    .page-header{background:linear-gradient(160deg,var(--sky-900),var(--sky-700));padding:140px 0 80px;text-align:center;position:relative;overflow:hidden;}
    .page-header::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:40px 40px;}
    .page-header h1{font-family:var(--font-d);font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#fff;letter-spacing:-.03em;}
    .page-header p{color:rgba(255,255,255,.65);font-size:1.05rem;margin-top:.8rem;}
    .breadcrumb-item a{color:rgba(255,255,255,.6);}.breadcrumb-item.active{color:rgba(255,255,255,.9);}
    .breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4);}
    /* SECTIONS */
    section{padding:80px 0;}
    .section-tag{display:inline-flex;align-items:center;gap:.5rem;color:var(--accent);font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.75rem;}
    .section-tag::before{content:'';width:24px;height:2px;background:var(--accent);border-radius:2px;}
    .section-title{font-family:var(--font-d);font-size:clamp(1.8rem,3.5vw,2.5rem);font-weight:800;letter-spacing:-.03em;line-height:1.2;margin-bottom:1rem;}
    .section-subtitle{font-size:1rem;color:var(--muted);line-height:1.8;font-weight:300;}
    /* ABOUT CARDS */
    .about-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:2rem;height:100%;transition:.3s;box-shadow:0 4px 20px rgba(10,22,40,.06);}
    .about-card:hover{transform:translateY(-5px);box-shadow:0 12px 35px rgba(10,22,40,.12);}
    .about-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:1.2rem;}
    .ai-blue{background:rgba(59,130,246,.1);color:#3b82f6;}
    .ai-orange{background:rgba(232,86,42,.1);color:var(--accent);}
    .ai-green{background:rgba(52,211,153,.1);color:#34d399;}
    .ai-purple{background:rgba(139,92,246,.1);color:#8b5cf6;}
    /* TEAM CARDS */
    .team-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.8rem;text-align:center;transition:.3s;box-shadow:0 4px 20px rgba(10,22,40,.06);}
    .team-card:hover{transform:translateY(-5px);box-shadow:0 12px 35px rgba(10,22,40,.12);}
    .team-avatar{width:80px;height:80px;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:1.8rem;font-weight:800;color:#fff;}
    .team-name{font-family:var(--font-d);font-size:1rem;font-weight:700;margin-bottom:.2rem;}
    .team-role{font-size:.82rem;color:var(--muted);}
    /* STATS */
    .stats-strip{background:linear-gradient(135deg,var(--sky-900),var(--sky-700));border-radius:var(--radius);padding:2.5rem;}
    .strip-stat{text-align:center;}
    .strip-num{font-family:var(--font-d);font-size:2.5rem;font-weight:800;color:#fff;line-height:1;}
    .strip-num span{color:var(--accent2);}
    .strip-label{font-size:.82rem;color:rgba(255,255,255,.55);margin-top:.3rem;}
    /* FOOTER */
    .footer{background:var(--sky-900);color:rgba(255,255,255,.65);padding:60px 0 30px;border-top:1px solid rgba(255,255,255,.05);}
    .footer-brand{font-family:var(--font-d);font-size:1.4rem;font-weight:800;color:#fff;}
    .footer-links li{margin-bottom:.5rem;}
    .footer-links a{color:rgba(255,255,255,.55);font-size:.88rem;transition:color .25s;}
    .footer-links a:hover{color:var(--accent2);}
    .footer-divider{border-color:rgba(255,255,255,.07);margin:2rem 0 1.5rem;}
    .footer-copy{font-size:.82rem;}
    </style>
</head>
<body>
<?php
$theme = 'light';
if (isset($_COOKIE['skyline-theme'])) $theme = $_COOKIE['skyline-theme'];
?>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-sky">Sky</span><span class="brand-line">line</span>
            <span style="color:var(--muted);font-size:.85rem;font-weight:500;"> Hostel</span>
        </a>
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <button class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars" style="color:var(--text);font-size:1.1rem;"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button class="theme-toggle d-none d-lg-flex" id="themeToggleLg"><i class="fa-solid fa-moon" id="themeIconLg"></i></button>
                <a href="student/login.php" class="btn-nav-login">Student Login</a>
                <a href="student/register.php" class="btn-nav-register">Apply Now</a>
            </div>
        </div>
    </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" style="justify-content:center;display:flex;margin-bottom:1rem;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
        <h1>About Skyline Hostel</h1>
        <p>Learn about our story, mission, and the team behind your home away from home.</p>
    </div>
</div>

<!-- OUR STORY -->
<section style="background:var(--bg);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="section-tag">Our Story</div>
                <h2 class="section-title">More Than Just a Hostel — It's a Community</h2>
                <p class="section-subtitle mb-3">Founded in 2019, Skyline Hostel was born from a simple vision: to create a student living experience that goes beyond four walls and a bed. We wanted a place where students could thrive academically, grow personally, and build lifelong friendships.</p>
                <p style="font-size:1rem;color:var(--muted);line-height:1.8;font-weight:300;">Over the years, we have housed hundreds of students from across the country, continuously improving our facilities and services based on student feedback. Today, Skyline Hostel stands as one of the most trusted student accommodations in the city.</p>
                <div style="display:flex;gap:2rem;margin-top:2rem;flex-wrap:wrap;">
                    <div><div style="font-family:var(--font-d);font-size:2rem;font-weight:800;color:var(--accent);">5+</div><div style="font-size:.82rem;color:var(--muted);">Years of Excellence</div></div>
                    <div><div style="font-family:var(--font-d);font-size:2rem;font-weight:800;color:var(--sky-600);">200+</div><div style="font-size:.82rem;color:var(--muted);">Students Housed</div></div>
                    <div><div style="font-family:var(--font-d);font-size:2rem;font-weight:800;color:#34d399;">4.9★</div><div style="font-size:.82rem;color:var(--muted);">Student Rating</div></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stats-strip">
                    <div class="row g-4">
                        <?php $ss=[['200+','Students Housed'],['50+','Rooms Available'],['24/7','Support Available'],['100%','Safety Record']];
                        foreach($ss as $s): ?>
                        <div class="col-6">
                            <div class="strip-stat">
                                <div class="strip-num"><?= $s[0] ?></div>
                                <div class="strip-label"><?= $s[1] ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MISSION & VALUES -->
<section style="background:var(--card);">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-tag justify-content-center">Our Values</div>
                <h2 class="section-title">What We Stand For</h2>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['ai-blue','fa-shield-halved','Safety First','Your safety is our top priority. 24/7 CCTV surveillance, trained security personnel, and secure access systems keep you protected at all times.'],
                ['ai-orange','fa-heart','Student Wellbeing','We care about your physical and mental wellbeing. From nutritious meals to study spaces, everything is designed for your success.'],
                ['ai-green','fa-handshake','Community Building','We foster a vibrant community where students from different backgrounds connect, collaborate, and create memories.'],
                ['ai-purple','fa-star','Excellence','We continuously raise our standards. From room cleanliness to internet speed, we never settle for anything less than the best.'],
            ];
            foreach($values as $v): ?>
            <div class="col-md-6 col-lg-3">
                <div class="about-card">
                    <div class="about-icon <?= $v[0] ?>"><i class="fa-solid <?= $v[1] ?>"></i></div>
                    <h5 style="font-family:var(--font-d);font-weight:700;margin-bottom:.6rem;"><?= $v[2] ?></h5>
                    <p style="font-size:.88rem;color:var(--muted);line-height:1.7;margin:0;"><?= $v[3] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FACILITIES -->
<section style="background:var(--bg);">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-tag justify-content-center">Facilities</div>
                <h2 class="section-title">Everything You Need</h2>
                <p class="section-subtitle mx-auto">World-class amenities designed to support your academic journey and personal growth.</p>
            </div>
        </div>
        <div class="row g-3">
            <?php
            $facilities = [
                ['fa-wifi','#3b82f6','High-Speed WiFi','100 Mbps fiber internet in every room and common area.'],
                ['fa-utensils','#34d399','Dining Hall','Nutritious meals prepared fresh daily by professional chefs.'],
                ['fa-book-open','#8b5cf6','Study Rooms','Quiet, air-conditioned study rooms available 24/7.'],
                ['fa-dumbbell','#e8562a','Fitness Center','Fully equipped gym with modern exercise equipment.'],
                ['fa-bolt','#f5a623','Power Backup','Uninterrupted power supply with generator backup.'],
                ['fa-droplet','#14b8a6','Hot Water','24/7 hot water supply in all bathrooms.'],
                ['fa-shirt','#ec4899','Laundry','Washing machines and dryers available on each floor.'],
                ['fa-car','#6366f1','Parking','Secure parking space for bicycles and motorcycles.'],
            ];
            foreach($facilities as $f): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1.2rem;display:flex;align-items:center;gap:.8rem;transition:.3s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(10,22,40,.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="width:40px;height:40px;border-radius:10px;background:<?= $f[1] ?>18;color:<?= $f[1] ?>;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;"><i class="fa-solid <?= $f[0] ?>"></i></div>
                    <div>
                        <div style="font-weight:700;font-size:.85rem;"><?= $f[2] ?></div>
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.1rem;"><?= $f[3] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TEAM -->
<section style="background:var(--card);">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-tag justify-content-center">Our Team</div>
                <h2 class="section-title">The People Behind Skyline</h2>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['SA','linear-gradient(135deg,#0a1628,#1a3a6e)','Mr. Ahmad Raza','Hostel Manager'],
                ['FK','linear-gradient(135deg,#e8562a,#c0410f)','Ms. Fatima Khan','Student Affairs Officer'],
                ['ZA','linear-gradient(135deg,#34d399,#059669)','Mr. Zubair Ali','Maintenance Head'],
                ['SB','linear-gradient(135deg,#8b5cf6,#6d28d9)','Ms. Sara Baig','Admin Coordinator'],
            ];
            foreach($team as $t): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="team-card">
                    <div class="team-avatar" style="background:<?= $t[1] ?>;"><?= $t[0] ?></div>
                    <div class="team-name"><?= $t[2] ?></div>
                    <div class="team-role"><?= $t[3] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background:linear-gradient(135deg,var(--sky-900),var(--sky-700));padding:60px 0;">
    <div class="container text-center">
        <h2 style="font-family:var(--font-d);font-size:2rem;font-weight:800;color:#fff;margin-bottom:.8rem;">Ready to Join Our Community?</h2>
        <p style="color:rgba(255,255,255,.65);margin-bottom:1.8rem;">Apply today and become part of the Skyline family.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="student/register.php" style="background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.85rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;"><i class="fa-solid fa-rocket"></i> Apply Now</a>
            <a href="contact.php" style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);padding:.85rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;"><i class="fa-solid fa-phone"></i> Contact Us</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand"><span style="color:var(--accent2);">Sky</span>line Hostel</div>
                <p style="font-size:.88rem;color:rgba(255,255,255,.55);margin:1rem 0;">Premium student accommodation for academic success and personal growth.</p>
            </div>
            <div class="col-6 col-lg-2">
                <div style="font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#fff;margin-bottom:1rem;">Quick Links</div>
                <ul class="footer-links list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="student/login.php">Student Login</a></li>
                    <li><a href="admin/login.php">Admin Login</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <div style="font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#fff;margin-bottom:1rem;">Contact</div>
                <div style="font-size:.88rem;color:rgba(255,255,255,.55);line-height:2;">
                    <div><i class="fa-solid fa-location-dot" style="color:var(--accent);width:16px;"></i> Block 7, University Road, Karachi</div>
                    <div><i class="fa-solid fa-phone" style="color:var(--accent);width:16px;"></i> +92 300 123 4567</div>
                    <div><i class="fa-solid fa-envelope" style="color:var(--accent);width:16px;"></i> info@skylinehostel.com</div>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
            <p class="footer-copy mb-0">&copy; <?= date('Y') ?> Skyline Hostel. All rights reserved.</p>
            <div style="display:flex;gap:1rem;"><a href="index.php" style="color:rgba(255,255,255,.4);font-size:.82rem;">Privacy Policy</a><a href="index.php" style="color:rgba(255,255,255,.4);font-size:.82rem;">Terms</a></div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const html=document.documentElement;
const stored=localStorage.getItem('skyline-theme')||'light';
html.setAttribute('data-theme',stored);
function updateIcons(t){document.querySelectorAll('#themeIcon,#themeIconLg').forEach(el=>el.className=t==='dark'?'fa-solid fa-sun':'fa-solid fa-moon');}
updateIcons(stored);
document.querySelectorAll('#themeToggle,#themeToggleLg').forEach(btn=>btn.addEventListener('click',()=>{
    const cur=html.getAttribute('data-theme'),next=cur==='dark'?'light':'dark';
    html.setAttribute('data-theme',next);localStorage.setItem('skyline-theme',next);updateIcons(next);
}));
window.addEventListener('scroll',()=>document.getElementById('mainNav').classList.toggle('scrolled',window.scrollY>20));
</script>
</body>
</html>