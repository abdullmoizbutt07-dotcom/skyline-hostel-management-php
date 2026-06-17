<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — Skyline Hostel</title>
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
    .navbar{height:var(--nav-height);background:rgba(255,255,255,.92);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);position:fixed;top:0;width:100%;z-index:1000;transition:all .3s;}
    [data-theme="dark"] .navbar{background:rgba(7,16,31,.92);}
    .navbar.scrolled{box-shadow:0 8px 30px rgba(10,22,40,.1);}
    .navbar-brand{font-family:var(--font-d);font-size:1.45rem;font-weight:800;}
    .brand-sky{color:var(--sky-600);}.brand-line{color:var(--accent);}
    .nav-link{font-weight:500;font-size:.9rem;color:var(--muted) !important;padding:.5rem 1rem !important;border-radius:8px;}
    .nav-link:hover,.nav-link.active{color:var(--text) !important;}
    .btn-nav-login{background:transparent;border:1.5px solid var(--border);color:var(--text) !important;padding:.45rem 1.2rem !important;border-radius:100px;font-size:.875rem;font-weight:600;}
    .btn-nav-login:hover{border-color:var(--accent);color:var(--accent) !important;}
    .btn-nav-register{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff !important;border:none;padding:.45rem 1.3rem !important;border-radius:100px;font-size:.875rem;font-weight:600;}
    .btn-nav-register:hover{transform:translateY(-1px);}
    .theme-toggle{background:var(--card);border:1.5px solid var(--border);color:var(--muted);width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .25s;}
    .theme-toggle:hover{border-color:var(--accent);color:var(--accent);}
    .page-header{background:linear-gradient(160deg,var(--sky-900),var(--sky-700));padding:140px 0 80px;text-align:center;position:relative;overflow:hidden;}
    .page-header::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:40px 40px;}
    .page-header h1{font-family:var(--font-d);font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#fff;letter-spacing:-.03em;}
    .page-header p{color:rgba(255,255,255,.65);font-size:1.05rem;margin-top:.8rem;}
    .breadcrumb-item a{color:rgba(255,255,255,.6);}.breadcrumb-item.active{color:rgba(255,255,255,.9);}
    .breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4);}
    /* CONTACT INFO CARDS */
    .contact-info-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.8rem;text-align:center;transition:.3s;box-shadow:0 4px 20px rgba(10,22,40,.06);height:100%;}
    .contact-info-card:hover{transform:translateY(-5px);box-shadow:0 12px 35px rgba(10,22,40,.12);}
    .ci-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin:0 auto 1.2rem;}
    .ci-blue{background:rgba(59,130,246,.1);color:#3b82f6;}
    .ci-orange{background:rgba(232,86,42,.1);color:var(--accent);}
    .ci-green{background:rgba(52,211,153,.1);color:#34d399;}
    .ci-purple{background:rgba(139,92,246,.1);color:#8b5cf6;}
    .ci-title{font-family:var(--font-d);font-size:1rem;font-weight:700;margin-bottom:.5rem;}
    .ci-text{font-size:.88rem;color:var(--muted);line-height:1.8;}
    /* FORM */
    .form-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:2.5rem;box-shadow:0 4px 20px rgba(10,22,40,.06);}
    label{font-size:.82rem;font-weight:600;margin-bottom:.35rem;display:block;}
    .form-control,.form-select{background:var(--bg);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.7rem 1rem;font-size:.9rem;width:100%;transition:.25s;font-family:var(--font-b);}
    .form-control:focus,.form-select:focus{outline:none;border-color:var(--sky-600);box-shadow:0 0 0 3px rgba(26,58,110,.1);background:var(--card);}
    textarea.form-control{resize:vertical;min-height:130px;}
    .btn-send{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.85rem 2.5rem;border-radius:100px;font-family:var(--font-d);font-size:1rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;width:100%;justify-content:center;}
    .btn-send:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(232,86,42,.4);}
    /* SUCCESS MESSAGE */
    .success-msg{background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.25);border-radius:10px;padding:1rem 1.2rem;display:flex;align-items:center;gap:.7rem;font-size:.88rem;color:#34d399;margin-bottom:1.5rem;}
    /* MAP PLACEHOLDER */
    .map-placeholder{background:linear-gradient(135deg,var(--sky-900),var(--sky-700));border-radius:var(--radius);height:300px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
    .map-placeholder::before{content:'';position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.05) 1px,transparent 1px);background-size:20px 20px;}
    /* FOOTER */
    .footer{background:var(--sky-900);color:rgba(255,255,255,.65);padding:60px 0 30px;border-top:1px solid rgba(255,255,255,.05);}
    .footer-brand{font-family:var(--font-d);font-size:1.4rem;font-weight:800;color:#fff;}
    .footer-links li{margin-bottom:.5rem;}
    .footer-links a{color:rgba(255,255,255,.55);font-size:.88rem;transition:color .25s;}
    .footer-links a:hover{color:var(--accent2);}
    .footer-divider{border-color:rgba(255,255,255,.07);margin:2rem 0 1.5rem;}
    </style>
</head>
<body>

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
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
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
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
        <h1>Get In Touch</h1>
        <p>We'd love to hear from you. Reach out to us anytime!</p>
    </div>
</div>

<!-- CONTACT INFO CARDS -->
<section style="background:var(--bg);padding:70px 0 40px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="contact-info-card">
                    <div class="ci-icon ci-orange"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="ci-title">Our Address</div>
                    <div class="ci-text">Block 7, Near University Road,<br>Karachi, Sindh, Pakistan</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="contact-info-card">
                    <div class="ci-icon ci-blue"><i class="fa-solid fa-phone"></i></div>
                    <div class="ci-title">Phone Number</div>
                    <div class="ci-text">+92 300 123 4567<br>+92 21 3456 7890</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="contact-info-card">
                    <div class="ci-icon ci-green"><i class="fa-solid fa-envelope"></i></div>
                    <div class="ci-title">Email Address</div>
                    <div class="ci-text">info@skylinehostel.com<br>admin@skylinehostel.com</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="contact-info-card">
                    <div class="ci-icon ci-purple"><i class="fa-solid fa-clock"></i></div>
                    <div class="ci-title">Office Hours</div>
                    <div class="ci-text">Mon – Sat: 8am – 8pm<br>Sunday: 10am – 4pm</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT FORM + MAP -->
<section style="background:var(--bg);padding:20px 0 80px;">
    <div class="container">
        <div class="row g-4">

            <!-- Form -->
            <div class="col-lg-6">
                <div class="form-card">
                    <div style="margin-bottom:2rem;">
                        <div style="display:inline-flex;align-items:center;gap:.5rem;color:var(--accent);font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.5rem;">
                            <span style="width:24px;height:2px;background:var(--accent);border-radius:2px;display:inline-block;"></span> Send Message
                        </div>
                        <h2 style="font-family:var(--font-d);font-size:1.8rem;font-weight:800;letter-spacing:-.02em;">We're Here to Help</h2>
                        <p style="color:var(--muted);font-size:.9rem;margin-top:.5rem;">Fill out the form below and we'll get back to you within 24 hours.</p>
                    </div>

                    <?php
                    $sent = false;
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
                        // In a real project you would send an email here
                        $sent = true;
                    }
                    ?>

                    <?php if($sent): ?>
                    <div class="success-msg">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                        <div><strong>Message Sent!</strong> We'll get back to you within 24 hours.</div>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="send_message" value="1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Your Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="Muhammad Ahmed" required>
                            </div>
                            <div class="col-md-6">
                                <label>Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="ahmed@email.com" required>
                            </div>
                            <div class="col-md-6">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+92 300 1234567">
                            </div>
                            <div class="col-md-6">
                                <label>Subject</label>
                                <select name="subject" class="form-select">
                                    <option value="">Select Subject</option>
                                    <option>Room Inquiry</option>
                                    <option>Fee Information</option>
                                    <option>General Query</option>
                                    <option>Complaint</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label>Your Message *</label>
                                <textarea name="message" class="form-control" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-send">
                                    <i class="fa-solid fa-paper-plane"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Map + Info -->
            <div class="col-lg-6">
                <!-- Map Placeholder -->
                <div class="map-placeholder mb-4">
                    <div style="text-align:center;position:relative;z-index:1;">
                        <i class="fa-solid fa-location-dot fa-3x" style="color:var(--accent);margin-bottom:1rem;display:block;"></i>
                        <div style="font-family:var(--font-d);font-size:1.2rem;font-weight:800;color:#fff;">Skyline Hostel</div>
                        <div style="color:rgba(255,255,255,.65);font-size:.88rem;margin-top:.4rem;">Block 7, University Road, Karachi</div>
                        <a href="https://maps.google.com" target="_blank" style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;padding:.45rem 1.1rem;border-radius:100px;font-size:.82rem;font-weight:600;margin-top:1rem;">
                            <i class="fa-solid fa-map"></i> View on Google Maps
                        </a>
                    </div>
                </div>

                <!-- Quick Contact -->
                <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;box-shadow:0 4px 20px rgba(10,22,40,.06);">
                    <div style="font-family:var(--font-d);font-size:.95rem;font-weight:700;margin-bottom:1.2rem;display:flex;align-items:center;gap:.5rem;">
                        <i class="fa-solid fa-bolt" style="color:var(--accent2);"></i> Quick Contact
                    </div>
                    <?php
                    $contacts = [
                        ['fa-phone','Call Us','+92 300 123 4567','tel:+923001234567'],
                        ['fa-envelope','Email Us','info@skylinehostel.com','mailto:info@skylinehostel.com'],
                        ['fa-brands fa-whatsapp','WhatsApp','+92 300 123 4567','https://wa.me/923001234567'],
                    ];
                    foreach($contacts as $c): ?>
                    <a href="<?= $c[3] ?>" style="display:flex;align-items:center;gap:.9rem;padding:.85rem;background:var(--bg);border-radius:10px;margin-bottom:.6rem;transition:.25s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform=''">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));color:#fff;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;"><i class="<?= $c[0] ?>"></i></div>
                        <div>
                            <div style="font-size:.75rem;color:var(--muted);"><?= $c[1] ?></div>
                            <div style="font-weight:700;font-size:.88rem;color:var(--text);"><?= $c[2] ?></div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color:var(--muted);font-size:.75rem;margin-left:auto;"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section style="background:var(--card);padding:70px 0;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div style="display:inline-flex;align-items:center;gap:.5rem;color:var(--accent);font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.75rem;">
                    <span style="width:24px;height:2px;background:var(--accent);border-radius:2px;display:inline-block;"></span> FAQ
                </div>
                <h2 style="font-family:var(--font-d);font-size:2rem;font-weight:800;letter-spacing:-.03em;">Frequently Asked Questions</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <?php
                    $faqs = [
                        ['How do I apply for a room?','Simply click the "Apply Now" button, register your account, and submit your room application. Our team will review and allocate a room within 24-48 hours.'],
                        ['What is included in the monthly fee?','The monthly fee covers accommodation, WiFi, water, electricity, and basic security services. Meals are available at an additional cost.'],
                        ['Can I visit the hostel before applying?','Yes! We welcome visitors. You can visit our reception from Monday to Saturday between 10am and 5pm for a tour.'],
                        ['What documents are required for admission?','You need a valid student ID, CNIC/B-Form, two passport photos, and a guardian contact form.'],
                        ['Is there a curfew?','For security reasons, the main gate closes at 11pm. Students can request the gate to be opened for late arrivals with prior notice.'],
                    ];
                    foreach($faqs as $i => $faq): ?>
                    <div class="accordion-item" style="background:var(--bg);border:1px solid var(--border);border-radius:12px !important;margin-bottom:.6rem;overflow:hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i>0?'collapsed':'' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>" style="background:var(--bg);color:var(--text);font-family:var(--font-d);font-weight:700;font-size:.9rem;border-radius:12px !important;">
                                <?= $faq[0] ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="font-size:.88rem;color:var(--muted);line-height:1.8;"><?= $faq[1] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
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
        <p style="font-size:.82rem;text-align:center;">&copy; <?= date('Y') ?> Skyline Hostel. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const html=document.documentElement,stored=localStorage.getItem('skyline-theme')||'light';
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