<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms — Skyline Hostel</title>
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
    /* FILTER BAR */
    .filter-bar{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.2rem 1.5rem;margin-bottom:2rem;display:flex;flex-wrap:wrap;gap:.7rem;align-items:center;box-shadow:0 4px 20px rgba(10,22,40,.06);}
    .filter-btn{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.45rem 1.1rem;border-radius:100px;font-size:.85rem;font-weight:600;cursor:pointer;transition:.25s;}
    .filter-btn.active,.filter-btn:hover{background:var(--sky-700);border-color:var(--sky-700);color:#fff;}
    /* ROOM CARDS */
    .room-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:.35s;height:100%;box-shadow:0 4px 20px rgba(10,22,40,.06);}
    .room-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(10,22,40,.12);border-color:transparent;}
    .room-img{height:180px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
    .ri-single{background:linear-gradient(135deg,#0a1628,#1a3a6e);}
    .ri-double{background:linear-gradient(135deg,#1a1a2e,#16213e);}
    .ri-triple{background:linear-gradient(135deg,#0f3460,#533483);}
    .ri-quad{background:linear-gradient(135deg,#162447,#1f4068);}
    .room-num-bg{position:absolute;right:10px;bottom:-10px;font-family:var(--font-d);font-size:6rem;font-weight:800;color:rgba(255,255,255,.07);line-height:1;}
    .room-type-tag{position:absolute;top:.8rem;left:.8rem;background:rgba(255,255,255,.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);color:#fff;padding:.25rem .8rem;border-radius:100px;font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;}
    .avail-dot{position:absolute;top:.8rem;right:.8rem;width:10px;height:10px;border-radius:50%;border:2px solid rgba(255,255,255,.3);}
    .dot-available{background:#34d399;}
    .dot-full{background:#e8562a;}
    .dot-maintenance{background:#f5a623;}
    .room-num-display{font-family:var(--font-d);font-size:2.2rem;font-weight:800;color:#fff;position:relative;z-index:1;}
    .room-body{padding:1.3rem;}
    .room-title{font-family:var(--font-d);font-size:1rem;font-weight:700;margin-bottom:.2rem;}
    .room-floor{font-size:.78rem;color:var(--muted);margin-bottom:.8rem;}
    .amenity-chips{display:flex;flex-wrap:wrap;gap:.3rem;margin-bottom:.9rem;}
    .amenity-chip{background:var(--bg);border:1px solid var(--border);color:var(--muted);padding:.18rem .55rem;border-radius:100px;font-size:.7rem;}
    .room-footer{display:flex;justify-content:space-between;align-items:center;padding-top:.9rem;border-top:1px solid var(--border);}
    .room-price{font-family:var(--font-d);font-size:1.2rem;font-weight:800;color:var(--accent);}
    .room-price small{font-family:var(--font-b);font-size:.72rem;color:var(--muted);font-weight:400;}
    .btn-apply{background:var(--sky-700);color:#fff;border:none;padding:.45rem 1.1rem;border-radius:100px;font-size:.82rem;font-weight:700;cursor:pointer;transition:.25s;text-decoration:none;}
    .btn-apply:hover{background:var(--accent);color:#fff;transform:translateY(-1px);}
    .cap-bar{margin-bottom:.8rem;}
    .cap-label{display:flex;justify-content:space-between;font-size:.7rem;color:var(--muted);margin-bottom:.25rem;}
    .cap-track{height:4px;background:var(--border);border-radius:4px;overflow:hidden;}
    .cap-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#34d399,#10b981);}
    .cap-fill.warn{background:linear-gradient(90deg,#f5a623,#e8562a);}
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
<?php require_once 'includes/db.php'; ?>

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
                <li class="nav-item"><a class="nav-link active" href="rooms.php">Rooms</a></li>
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
                <li class="breadcrumb-item active">Rooms</li>
            </ol>
        </nav>
        <h1>Our Rooms</h1>
        <p>Find the perfect room that matches your needs and budget.</p>
    </div>
</div>

<!-- ROOMS SECTION -->
<section style="background:var(--bg);padding:60px 0;">
    <div class="container">

        <!-- Filter -->
        <div class="filter-bar">
            <span style="font-size:.82rem;font-weight:700;color:var(--muted);">Filter by:</span>
            <button class="filter-btn active" onclick="filterRooms('all',this)">All Rooms</button>
            <button class="filter-btn" onclick="filterRooms('Single',this)">Single</button>
            <button class="filter-btn" onclick="filterRooms('Double',this)">Double</button>
            <button class="filter-btn" onclick="filterRooms('Triple',this)">Triple</button>
            <button class="filter-btn" onclick="filterRooms('Quad',this)">Quad</button>
            <div style="margin-left:auto;font-size:.82rem;color:var(--muted);" id="roomCount"></div>
        </div>

        <?php
        $rooms = $conn->query("SELECT * FROM rooms ORDER BY room_type, monthly_fee ASC");
        $colorMap = ['Single'=>'ri-single','Double'=>'ri-double','Triple'=>'ri-triple','Quad'=>'ri-quad'];
        ?>

        <div class="row g-4" id="roomsGrid">
            <?php if($rooms && $rooms->num_rows > 0):
                while($room = $rooms->fetch_assoc()):
                $pct = $room['capacity'] > 0 ? round(($room['occupied']/$room['capacity'])*100) : 0;
                $bgClass = $colorMap[$room['room_type']] ?? 'ri-single';
                $dotClass = 'dot-'.$room['status'];
                $amenities = explode(',', $room['amenities']);
                $spots = $room['capacity'] - $room['occupied'];
            ?>
            <div class="col-md-6 col-lg-4 room-item" data-type="<?= $room['room_type'] ?>">
                <div class="room-card">
                    <div class="room-img <?= $bgClass ?>">
                        <span class="room-type-tag"><?= $room['room_type'] ?></span>
                        <div class="avail-dot <?= $dotClass ?>"></div>
                        <div class="room-num-bg"><?= htmlspecialchars($room['room_number']) ?></div>
                        <div class="room-num-display"><?= htmlspecialchars($room['room_number']) ?></div>
                    </div>
                    <div class="room-body">
                        <div class="room-title">Room <?= htmlspecialchars($room['room_number']) ?></div>
                        <div class="room-floor"><i class="fa-solid fa-layer-group" style="font-size:.7rem;"></i> <?= htmlspecialchars($room['floor']) ?> · <?= $room['capacity'] ?> bed<?= $room['capacity']>1?'s':'' ?></div>
                        <div class="cap-bar">
                            <div class="cap-label"><span>Occupancy</span><span><?= $room['occupied'] ?>/<?= $room['capacity'] ?> (<?= $spots ?> left)</span></div>
                            <div class="cap-track"><div class="cap-fill <?= $pct>=80?'warn':'' ?>" style="width:<?= $pct ?>%"></div></div>
                        </div>
                        <div class="amenity-chips">
                            <?php foreach(array_slice($amenities,0,4) as $am): ?>
                            <span class="amenity-chip"><?= trim(htmlspecialchars($am)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="room-footer">
                            <div class="room-price">Rs.<?= number_format($room['monthly_fee']) ?><br><small>/month</small></div>
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                <?php if($room['status']==='available'): ?>
                                <span style="background:rgba(52,211,153,.1);color:#34d399;border:1px solid rgba(52,211,153,.2);padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:700;">Available</span>
                                <?php elseif($room['status']==='full'): ?>
                                <span style="background:rgba(232,86,42,.1);color:#e8562a;border:1px solid rgba(232,86,42,.2);padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:700;">Full</span>
                                <?php else: ?>
                                <span style="background:rgba(245,166,35,.1);color:#f5a623;border:1px solid rgba(245,166,35,.2);padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:700;">Maintenance</span>
                                <?php endif; ?>
                                <?php if($room['status']==='available'): ?>
                                <a href="student/register.php" class="btn-apply">Apply</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-door-open fa-3x" style="color:var(--border);"></i>
                <p style="color:var(--muted);margin-top:1rem;">No rooms available at this time.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- CTA Banner -->
        <div style="background:linear-gradient(135deg,var(--sky-900),var(--sky-700));border-radius:var(--radius);padding:2rem;text-align:center;margin-top:3rem;">
            <h3 style="font-family:var(--font-d);font-size:1.4rem;font-weight:800;color:#fff;margin-bottom:.5rem;">Can't find the right room?</h3>
            <p style="color:rgba(255,255,255,.65);font-size:.9rem;margin-bottom:1.2rem;">Submit your preferences and our team will find the best match for you.</p>
            <a href="student/register.php" style="background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;padding:.75rem 2rem;border-radius:100px;font-family:var(--font-d);font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;"><i class="fa-solid fa-rocket"></i> Apply Now — It's Free</a>
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

function filterRooms(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const items = document.querySelectorAll('.room-item');
    let count = 0;
    items.forEach(item => {
        const show = type==='all' || item.dataset.type===type;
        item.style.display = show ? '' : 'none';
        if(show) count++;
    });
    document.getElementById('roomCount').textContent = count + ' room' + (count!==1?'s':'') + ' found';
}
// Initial count
document.getElementById('roomCount').textContent = document.querySelectorAll('.room-item').length + ' rooms found';
</script>
</body>
</html>