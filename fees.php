<?php
require_once '../includes/db.php';
requireStudent();
$sid = $_SESSION['student_id'];

$fees = $conn->query("SELECT f.*,r.room_number FROM fees f LEFT JOIN rooms r ON f.room_id=r.id WHERE f.student_id=$sid ORDER BY f.created_at DESC");
$total_paid    = $conn->query("SELECT SUM(amount) as t FROM fees WHERE student_id=$sid AND status='paid'")->fetch_assoc()['t'] ?? 0;
$total_pending = $conn->query("SELECT SUM(amount) as t FROM fees WHERE student_id=$sid AND status='pending'")->fetch_assoc()['t'] ?? 0;
$total_overdue = $conn->query("SELECT SUM(amount) as t FROM fees WHERE student_id=$sid AND status='overdue'")->fetch_assoc()['t'] ?? 0;
$student = $conn->query("SELECT name,reg_no,profile_pic FROM students WHERE id=$sid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Status — Skyline Hostel</title>
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
    .sec-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .data-table{width:100%;border-collapse:collapse;}
    .data-table th{font-size:.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);padding:.8rem 1rem;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap;background:var(--bg);}
    .data-table td{font-size:.84rem;padding:.85rem 1rem;border-bottom:1px solid var(--border);vertical-align:middle;}
    .data-table tr:last-child td{border-bottom:none;}
    .data-table tbody tr:hover td{background:var(--bg);}
    .stat-box{border-radius:var(--radius);padding:1.2rem 1.4rem;box-shadow:var(--shadow);}
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
        <a href="notices.php" class="nav-link-item"><i class="ni fa-solid fa-bullhorn"></i> Notices</a>
        <div class="nav-sec">Finance</div>
        <a href="fees.php" class="nav-link-item active"><i class="ni fa-solid fa-receipt"></i> Fee Status</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></div>
</aside>
<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">Fee Status</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>
    <div class="page-content">
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="stat-box" style="background:linear-gradient(135deg,rgba(52,211,153,.1),rgba(16,185,129,.05));border:1px solid rgba(52,211,153,.2);">
                    <div style="font-size:.75rem;color:#34d399;font-weight:700;margin-bottom:.3rem;">Total Paid</div>
                    <div style="font-family:var(--font-d);font-size:1.4rem;font-weight:800;color:#34d399;">Rs.<?= number_format($total_paid) ?></div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="stat-box" style="background:linear-gradient(135deg,rgba(245,166,35,.1),rgba(245,166,35,.05));border:1px solid rgba(245,166,35,.2);">
                    <div style="font-size:.75rem;color:#f5a623;font-weight:700;margin-bottom:.3rem;">Pending</div>
                    <div style="font-family:var(--font-d);font-size:1.4rem;font-weight:800;color:#f5a623;">Rs.<?= number_format($total_pending) ?></div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="stat-box" style="background:linear-gradient(135deg,rgba(232,86,42,.1),rgba(232,86,42,.05));border:1px solid rgba(232,86,42,.2);">
                    <div style="font-size:.75rem;color:var(--accent);font-weight:700;margin-bottom:.3rem;">Overdue</div>
                    <div style="font-family:var(--font-d);font-size:1.4rem;font-weight:800;color:var(--accent);">Rs.<?= number_format($total_overdue) ?></div>
                </div>
            </div>
        </div>

        <!-- Fee Records Table -->
        <div class="sec-card">
            <div style="padding:1rem 1.3rem;border-bottom:1px solid var(--border);font-family:var(--font-d);font-size:.95rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                <i class="fa-solid fa-receipt" style="color:var(--sky-600);"></i> My Fee Records
            </div>
            <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Month / Year</th>
                        <th>Room</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Paid Date</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($fees && $fees->num_rows > 0):
                    $i=1;
                    while($f=$fees->fetch_assoc()):
                ?>
                <tr>
                    <td style="color:var(--muted);font-size:.78rem;"><?= $i++ ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($f['fee_month']) ?> <?= $f['fee_year'] ?></td>
                    <td style="font-size:.82rem;"><?= $f['room_number'] ? 'Room '.$f['room_number'] : '—' ?></td>
                    <td style="font-family:var(--font-d);font-weight:800;color:var(--accent);"><?= formatMoney($f['amount']) ?></td>
                    <td style="font-size:.78rem;color:var(--muted);"><?= formatDate($f['due_date']) ?></td>
                    <td style="font-size:.78rem;color:var(--muted);"><?= $f['paid_date'] ? formatDate($f['paid_date']) : '—' ?></td>
                    <td style="font-size:.78rem;"><?= htmlspecialchars($f['payment_method']) ?></td>
                    <td><?= statusBadge($f['status']) ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:3rem;">
                    <i class="fa-solid fa-receipt fa-2x" style="display:block;margin-bottom:.8rem;color:var(--border);"></i>
                    No fee records found.
                </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Note -->
        <div style="background:rgba(26,58,110,.06);border:1px solid rgba(26,58,110,.12);border-radius:var(--radius);padding:1rem 1.2rem;margin-top:1rem;font-size:.84rem;color:var(--muted);">
            <i class="fa-solid fa-circle-info" style="color:var(--sky-600);"></i>
            For any fee-related queries, please contact the hostel administration. Monthly fees are due by the <strong>10th of each month</strong>.
        </div>
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