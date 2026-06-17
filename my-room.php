<?php
require_once '../includes/db.php';
requireStudent();
$sid = $_SESSION['student_id'];

$allocation = $conn->query("
    SELECT ra.*, r.room_number, r.room_type, r.floor, r.monthly_fee, r.amenities, r.capacity, r.occupied
    FROM room_allocations ra
    JOIN rooms r ON ra.room_id = r.id
    WHERE ra.student_id=$sid AND ra.status='active'
    LIMIT 1
")->fetch_assoc();

// Get roommates
$roommates = [];
if ($allocation) {
    $rm = $conn->query("
        SELECT s.name, s.reg_no, s.course, s.profile_pic
        FROM room_allocations ra
        JOIN students s ON ra.student_id = s.id
        WHERE ra.room_id={$allocation['room_id']} AND ra.status='active' AND ra.student_id != $sid
    ");
    while ($r = $rm->fetch_assoc()) $roommates[] = $r;
}

$student = $conn->query("SELECT name,reg_no,profile_pic FROM students WHERE id=$sid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Room — Skyline Hostel</title>
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
    .room-hero{background:linear-gradient(135deg,var(--sky-900),var(--sky-700));border-radius:var(--radius);padding:2rem;color:#fff;position:relative;overflow:hidden;margin-bottom:1.5rem;}
    .room-hero::before{content:'';position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:24px 24px;}
    .room-num-bg{position:absolute;right:2rem;top:50%;transform:translateY(-50%);font-family:var(--font-d);font-size:8rem;font-weight:800;color:rgba(255,255,255,.06);line-height:1;}
    .room-num{font-family:var(--font-d);font-size:3rem;font-weight:800;line-height:1;position:relative;z-index:1;}
    .room-type{color:rgba(255,255,255,.65);font-size:.95rem;margin:.3rem 0;position:relative;z-index:1;}
    .room-chips{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:1rem;position:relative;z-index:1;}
    .room-chip{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.8);padding:.25rem .7rem;border-radius:100px;font-size:.75rem;}
    .info-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.3rem;box-shadow:var(--shadow);}
    .info-card-title{font-family:var(--font-d);font-size:.92rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
    .info-row{display:flex;align-items:center;gap:.8rem;padding:.7rem 0;border-bottom:1px solid var(--border);}
    .info-row:last-child{border-bottom:none;}
    .info-icon{width:32px;height:32px;border-radius:8px;background:rgba(26,58,110,.08);color:var(--sky-600);display:flex;align-items:center;justify-content:center;font-size:.82rem;flex-shrink:0;}
    .info-label{font-size:.75rem;color:var(--muted);}
    .info-val{font-size:.88rem;font-weight:600;}
    .roommate-card{display:flex;align-items:center;gap:.8rem;padding:.8rem;background:var(--bg);border-radius:10px;margin-bottom:.6rem;}
    .rm-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));color:#fff;font-family:var(--font-d);font-size:.95rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .no-room{background:var(--card);border:2px dashed var(--border);border-radius:var(--radius);padding:3rem;text-align:center;}
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
        <a href="my-room.php" class="nav-link-item active"><i class="ni fa-solid fa-door-open"></i> My Room</a>
        <a href="apply-room.php" class="nav-link-item"><i class="ni fa-solid fa-file-circle-plus"></i> Apply for Room</a>
        <a href="complaints.php" class="nav-link-item"><i class="ni fa-solid fa-triangle-exclamation"></i> Complaints</a>
        <a href="notices.php" class="nav-link-item"><i class="ni fa-solid fa-bullhorn"></i> Notices</a>
        <div class="nav-sec">Finance</div>
        <a href="fees.php" class="nav-link-item"><i class="ni fa-solid fa-receipt"></i> Fee Status</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></div>
</aside>
<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">My Room</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>
    <div class="page-content">
        <?php if($allocation): ?>
        <div class="room-hero">
            <div class="room-num-bg"><?= htmlspecialchars($allocation['room_number']) ?></div>
            <div style="font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:.3rem;">Your Allocated Room</div>
            <div class="room-num">Room <?= htmlspecialchars($allocation['room_number']) ?></div>
            <div class="room-type"><?= htmlspecialchars($allocation['room_type']) ?> Room · <?= htmlspecialchars($allocation['floor']) ?></div>
            <div class="room-chips">
                <?php foreach(explode(',',$allocation['amenities']) as $am): ?>
                <span class="room-chip"><?= trim(htmlspecialchars($am)) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="info-card mb-3">
                    <div class="info-card-title"><i class="fa-solid fa-circle-info" style="color:var(--sky-600);"></i> Room Details</div>
                    <?php $details=[['fa-door-open','Room Number','Room '.$allocation['room_number']],['fa-bed','Room Type',$allocation['room_type']],['fa-layer-group','Floor',$allocation['floor']],['fa-users','Capacity',$allocation['capacity'].' beds'],['fa-money-bill','Monthly Fee',formatMoney($allocation['monthly_fee'])],['fa-calendar','Allocated On',formatDate($allocation['allocation_date'])],['fa-circle-check','Status',ucfirst($allocation['status'])]];
                    foreach($details as $d): ?>
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-solid <?= $d[0] ?>"></i></div>
                        <div><div class="info-label"><?= $d[1] ?></div><div class="info-val"><?= htmlspecialchars($d[2]) ?></div></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="info-card mb-3">
                    <div class="info-card-title"><i class="fa-solid fa-users" style="color:var(--sky-600);"></i> Roommates (<?= count($roommates) ?>)</div>
                    <?php if(count($roommates)>0): foreach($roommates as $rm): ?>
                    <div class="roommate-card">
                        <div class="rm-avatar"><?= strtoupper(substr($rm['name'],0,1)) ?></div>
                        <div><div style="font-weight:700;font-size:.88rem;"><?= htmlspecialchars($rm['name']) ?></div><div style="font-size:.75rem;color:var(--muted);"><?= htmlspecialchars($rm['reg_no']) ?> · <?= htmlspecialchars($rm['course']??'N/A') ?></div></div>
                    </div>
                    <?php endforeach; else: ?>
                    <div style="text-align:center;padding:1.5rem;color:var(--muted);font-size:.88rem;">You are the only occupant in this room.</div>
                    <?php endif; ?>
                </div>
                <div class="info-card" style="background:linear-gradient(135deg,rgba(52,211,153,.06),rgba(16,185,129,.03));border-color:rgba(52,211,153,.2);">
                    <div class="info-card-title"><i class="fa-solid fa-circle-check" style="color:#34d399;"></i> Room Facilities</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                        <?php foreach(explode(',',$allocation['amenities']) as $am): ?>
                        <span style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);color:#34d399;padding:.3rem .8rem;border-radius:100px;font-size:.78rem;font-weight:600;"><?= trim(htmlspecialchars($am)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="no-room">
            <i class="fa-solid fa-door-open fa-3x" style="color:var(--border);display:block;margin-bottom:1rem;"></i>
            <div style="font-family:var(--font-d);font-weight:700;font-size:1.1rem;margin-bottom:.4rem;">No Room Assigned Yet</div>
            <p style="color:var(--muted);font-size:.88rem;margin-bottom:1.2rem;">Apply for a room and wait for admin approval.</p>
            <a href="apply-room.php" style="background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.7rem 1.8rem;border-radius:100px;font-family:var(--font-d);font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;"><i class="fa-solid fa-file-circle-plus"></i> Apply for Room</a>
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