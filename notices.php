<?php
require_once '../includes/db.php';
requireStudent();
$sid = $_SESSION['student_id'];
$student = $conn->query("SELECT name,reg_no,profile_pic,gender FROM students WHERE id=$sid")->fetch_assoc();
$gender = strtolower($student['gender']);

$notices = $conn->query("
    SELECT * FROM notices
    WHERE is_active=1
    AND (target_audience='all' OR target_audience='$gender')
    ORDER BY is_pinned DESC, created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices — Skyline Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    :root{--sky-900:#0a1628;--sky-700:#112952;--sky-600:#1a3a6e;--accent:#e8562a;--accent2:#f5a623;--bg:#f0f4ff;--card:#fff;--border:#e2e8f0;--text:#0a1628;--muted:#718096;--sidebar-w:260px;--font-d:'Syne',sans-serif;--font-b:'DM Sans',sans-serif;--radius:14px;--shadow:0 4px 20px rgba(10,22,40,.08);}
    [data-theme="dark"]{--bg:#07101f;--card:#0d1f3c;--border:#1a2d4f;--text:#f0f4ff;--muted:#718096;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-b);background:var(--bg);color:var(--text);transition:.3s;}
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);background:var(--sky-900);display:flex;flex-direction:column;z-index:100;overflow-y:auto;transition:.3s;}
    .sidebar-brand{padding:1.5rem;border-bottom:1px solid rgba(255,255,255,.07);font-family:var(--font-d);font-size:1.3rem;font-weight:800;color:#fff;}
    .sidebar-brand span{color:var(--accent2);}
    .sidebar-user{padding:1.2rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:.8rem;}
    .user-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#c0410f);display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;overflow:hidden;}
    .user-avatar img{width:100%;height:100%;object-fit:cover;}
    .user-name{font-size:.85rem;font-weight:700;color:#fff;}.user-reg{font-size:.72rem;color:rgba(255,255,255,.5);}
    .sidebar-nav{padding:1rem 0;flex:1;}
    .nav-sec{font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.3);padding:.5rem 1.5rem;margin-top:.5rem;}
    .nav-link-item{display:flex;align-items:center;gap:.8rem;padding:.7rem 1.5rem;color:rgba(255,255,255,.6);font-size:.88rem;font-weight:500;text-decoration:none;transition:.2s;border-left:3px solid transparent;}
    .nav-link-item:hover{background:rgba(255,255,255,.05);color:#fff;}
    .nav-link-item.active{background:rgba(232,86,42,.12);color:#fff;border-left-color:var(--accent);}
    .nav-link-item .ni{width:18px;text-align:center;font-size:.9rem;}
    .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.07);}
    .logout-btn{display:flex;align-items:center;gap:.7rem;color:rgba(255,255,255,.5);font-size:.85rem;text-decoration:none;transition:.2s;padding:.5rem 0;}
    .logout-btn:hover{color:var(--accent);}
    .main-content{margin-left:var(--sidebar-w);min-height:100vh;}
    .topbar{background:var(--card);border-bottom:1px solid var(--border);padding:.9rem 1.8rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:var(--shadow);}
    .topbar-left{display:flex;align-items:center;gap:.8rem;}
    .menu-toggle{background:none;border:none;color:var(--text);font-size:1.1rem;cursor:pointer;display:none;}
    .page-title{font-family:var(--font-d);font-size:1.1rem;font-weight:700;}
    .icon-btn{width:36px;height:36px;border-radius:50%;background:var(--bg);border:1.5px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.85rem;transition:.25s;}
    .icon-btn:hover{border-color:var(--accent);color:var(--accent);}
    .page-content{padding:1.8rem;}
    .notice-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.3rem;margin-bottom:1rem;box-shadow:var(--shadow);transition:.3s;position:relative;}
    .notice-card:hover{box-shadow:0 8px 25px rgba(10,22,40,.1);}
    .notice-card.pinned{border-left:4px solid var(--accent);}
    .pinned-tag{display:inline-flex;align-items:center;gap:.3rem;background:rgba(232,86,42,.1);color:var(--accent);border:1px solid rgba(232,86,42,.2);padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:700;margin-bottom:.6rem;}
    .n-title{font-family:var(--font-d);font-size:1rem;font-weight:700;margin-bottom:.5rem;}
    .n-content{font-size:.88rem;color:var(--muted);line-height:1.8;}
    .n-footer{display:flex;align-items:center;gap:1rem;margin-top:.9rem;flex-wrap:wrap;}
    .n-date{font-size:.75rem;color:var(--muted);}
    .cat-badge{padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:700;}
    .cat-General{background:rgba(59,130,246,.1);color:#3b82f6;}
    .cat-Academic{background:rgba(139,92,246,.1);color:#8b5cf6;}
    .cat-Maintenance{background:rgba(245,166,35,.1);color:#f5a623;}
    .cat-Event{background:rgba(52,211,153,.1);color:#34d399;}
    .cat-Urgent{background:rgba(232,86,42,.1);color:#e8562a;animation:pulse .8s ease-in-out infinite alternate;}
    @keyframes pulse{from{opacity:1}to{opacity:.6}}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}
    @media(max-width:991px){.sidebar{left:calc(-1 * var(--sidebar-w));}.sidebar.open{left:0;}.sidebar-overlay.open{display:block;}.main-content{margin-left:0;}.menu-toggle{display:flex !important;}}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><span>Sky</span>line Hostel</div>
    <div class="sidebar-user">
        <div class="user-avatar"><?php if($student['profile_pic']&&$student['profile_pic']!=='default-student.png'&&file_exists(UPLOAD_PATH.$student['profile_pic'])): ?><img src="<?= UPLOAD_URL.htmlspecialchars($student['profile_pic']) ?>" alt=""><?php else: ?><?= strtoupper(substr($student['name'],0,1)) ?><?php endif; ?></div>
        <div><div class="user-name"><?= htmlspecialchars($student['name']) ?></div><div class="user-reg"><?= htmlspecialchars($student['reg_no']) ?></div></div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-sec">Main</div>
        <a href="dashboard.php" class="nav-link-item"><i class="ni fa-solid fa-gauge"></i> Dashboard</a>
        <a href="profile.php" class="nav-link-item"><i class="ni fa-solid fa-user"></i> My Profile</a>
        <div class="nav-sec">Hostel</div>
        <a href="my-room.php" class="nav-link-item"><i class="ni fa-solid fa-door-open"></i> My Room</a>
        <a href="apply-room.php" class="nav-link-item"><i class="ni fa-solid fa-file-circle-plus"></i> Apply for Room</a>
        <a href="complaints.php" class="nav-link-item"><i class="ni fa-solid fa-triangle-exclamation"></i> Complaints</a>
        <a href="notices.php" class="nav-link-item active"><i class="ni fa-solid fa-bullhorn"></i> Notices</a>
        <div class="nav-sec">Finance</div>
        <a href="fees.php" class="nav-link-item"><i class="ni fa-solid fa-receipt"></i> Fee Status</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></div>
</aside>
<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">Notices</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>
    <div class="page-content">
        <?php if($notices && $notices->num_rows > 0):
            while($n = $notices->fetch_assoc()):
        ?>
        <div class="notice-card <?= $n['is_pinned']?'pinned':'' ?>">
            <?php if($n['is_pinned']): ?>
            <div class="pinned-tag"><i class="fa-solid fa-thumbtack"></i> Pinned Notice</div>
            <?php endif; ?>
            <div class="n-title"><?= htmlspecialchars($n['title']) ?></div>
            <div class="n-content"><?= nl2br(htmlspecialchars($n['content'])) ?></div>
            <div class="n-footer">
                <span class="cat-badge cat-<?= $n['category'] ?>"><?= $n['category'] ?></span>
                <span class="n-date"><i class="fa-solid fa-calendar"></i> <?= formatDate($n['created_at']) ?></span>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:4rem;text-align:center;">
            <i class="fa-solid fa-bullhorn fa-3x" style="color:var(--border);display:block;margin-bottom:1rem;"></i>
            <div style="font-family:var(--font-d);font-weight:700;">No Notices Yet</div>
            <p style="color:var(--muted);font-size:.85rem;margin-top:.4rem;">Check back later for updates.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const html=document.documentElement,stored=localStorage.getItem('skyline-theme')||'light';
html.setAttribute('data-theme',stored);document.getElementById('themeIcon').className=stored==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';
document.getElementById('themeBtn').addEventListener('click',()=>{const cur=html.getAttribute('data-theme'),next=cur==='dark'?'light':'dark';html.setAttribute('data-theme',next);localStorage.setItem('skyline-theme',next);document.getElementById('themeIcon').className=next==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';});
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
</script>
</body>
</html>