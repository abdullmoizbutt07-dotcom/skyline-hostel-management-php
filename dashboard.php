<?php
require_once '../includes/db.php';
requireStudent();

$sid = $_SESSION['student_id'];

// Get student data
$student = $conn->query("SELECT * FROM students WHERE id=$sid")->fetch_assoc();

// Get room allocation
$allocation = $conn->query("
    SELECT ra.*, r.room_number, r.room_type, r.floor, r.monthly_fee, r.amenities
    FROM room_allocations ra
    JOIN rooms r ON ra.room_id = r.id
    WHERE ra.student_id=$sid AND ra.status='active'
    LIMIT 1
")->fetch_assoc();

// Get pending fees
$fees_pending = $conn->query("SELECT SUM(amount) as total FROM fees WHERE student_id=$sid AND status='pending'")->fetch_assoc()['total'] ?? 0;

// Get complaints count
$complaints_total  = getCount($conn, 'complaints', "student_id=$sid");
$complaints_open   = getCount($conn, 'complaints', "student_id=$sid AND status='pending'");

// Get latest notices
$notices = $conn->query("SELECT * FROM notices WHERE is_active=1 ORDER BY is_pinned DESC, created_at DESC LIMIT 5");

// Get recent fees
$fees = $conn->query("SELECT * FROM fees WHERE student_id=$sid ORDER BY created_at DESC LIMIT 5");

// Get room application status
$application = $conn->query("SELECT * FROM room_applications WHERE student_id=$sid ORDER BY applied_at DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Skyline Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    :root {
        --sky-900:#0a1628; --sky-800:#0d1f3c; --sky-700:#112952; --sky-600:#1a3a6e;
        --accent:#e8562a; --accent2:#f5a623;
        --bg:#f0f4ff; --card:#fff; --border:#e2e8f0;
        --text:#0a1628; --muted:#718096; --sidebar-w:260px;
        --font-d:'Syne',sans-serif; --font-b:'DM Sans',sans-serif;
        --radius:14px; --shadow:0 4px 20px rgba(10,22,40,.08);
    }
    [data-theme="dark"] {
        --bg:#07101f; --card:#0d1f3c; --border:#1a2d4f;
        --text:#f0f4ff; --muted:#718096;
    }
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-b);background:var(--bg);color:var(--text);transition:.3s;}

    /* SIDEBAR */
    .sidebar {
        position:fixed; top:0; left:0; bottom:0;
        width:var(--sidebar-w);
        background:var(--sky-900);
        display:flex; flex-direction:column;
        z-index:100; transition:.3s;
        overflow-y:auto;
    }
    .sidebar-brand {
        padding:1.5rem;
        border-bottom:1px solid rgba(255,255,255,.07);
        font-family:var(--font-d); font-size:1.3rem; font-weight:800; color:#fff;
    }
    .sidebar-brand span{color:var(--accent2);}

    .sidebar-user {
        padding:1.2rem 1.5rem;
        border-bottom:1px solid rgba(255,255,255,.07);
        display:flex; align-items:center; gap:.8rem;
    }
    .user-avatar {
        width:44px; height:44px; border-radius:50%;
        background:linear-gradient(135deg,var(--accent),#c0410f);
        display:flex; align-items:center; justify-content:center;
        font-family:var(--font-d); font-size:1rem; font-weight:700; color:#fff;
        flex-shrink:0; overflow:hidden;
    }
    .user-avatar img{width:100%;height:100%;object-fit:cover;}
    .user-name{font-size:.85rem;font-weight:700;color:#fff;line-height:1.3;}
    .user-reg{font-size:.72rem;color:rgba(255,255,255,.5);}

    .sidebar-nav{padding:1rem 0;flex:1;}
    .nav-section-label{
        font-size:.65rem; font-weight:700; letter-spacing:.1em;
        text-transform:uppercase; color:rgba(255,255,255,.3);
        padding:.5rem 1.5rem; margin-top:.5rem;
    }
    .nav-item-link {
        display:flex; align-items:center; gap:.8rem;
        padding:.7rem 1.5rem; color:rgba(255,255,255,.6);
        font-size:.88rem; font-weight:500; text-decoration:none;
        transition:.2s; border-left:3px solid transparent;
        position:relative;
    }
    .nav-item-link:hover{background:rgba(255,255,255,.05);color:#fff;}
    .nav-item-link.active{
        background:rgba(232,86,42,.12);
        color:#fff; border-left-color:var(--accent);
    }
    .nav-item-link .nav-icon{width:18px;text-align:center;font-size:.9rem;}
    .nav-badge{
        margin-left:auto; background:var(--accent);
        color:#fff; font-size:.65rem; font-weight:700;
        padding:.15rem .45rem; border-radius:100px;
    }

    .sidebar-footer{
        padding:1rem 1.5rem;
        border-top:1px solid rgba(255,255,255,.07);
    }
    .logout-btn{
        display:flex; align-items:center; gap:.7rem;
        color:rgba(255,255,255,.5); font-size:.85rem;
        text-decoration:none; transition:.2s;
        padding:.5rem 0;
    }
    .logout-btn:hover{color:#e8562a;}

    /* MAIN CONTENT */
    .main-content{
        margin-left:var(--sidebar-w);
        min-height:100vh;
        transition:.3s;
    }

    /* TOPBAR */
    .topbar{
        background:var(--card);
        border-bottom:1px solid var(--border);
        padding:.9rem 1.8rem;
        display:flex; align-items:center; justify-content:space-between;
        position:sticky; top:0; z-index:50;
        box-shadow:var(--shadow);
    }
    .topbar-left{display:flex;align-items:center;gap:.8rem;}
    .menu-toggle{
        background:none;border:none;color:var(--text);
        font-size:1.1rem;cursor:pointer;padding:.3rem;
        display:none;
    }
    .page-title{font-family:var(--font-d);font-size:1.1rem;font-weight:700;}
    .topbar-right{display:flex;align-items:center;gap:.8rem;}

    .theme-btn{
        width:36px;height:36px;border-radius:50%;
        background:var(--bg);border:1.5px solid var(--border);
        color:var(--muted);cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        font-size:.85rem;transition:.25s;
    }
    .theme-btn:hover{border-color:var(--accent);color:var(--accent);}

    .notif-btn{
        width:36px;height:36px;border-radius:50%;
        background:var(--bg);border:1.5px solid var(--border);
        color:var(--muted);cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        font-size:.85rem;transition:.25s;position:relative;
    }
    .notif-dot{
        position:absolute;top:6px;right:6px;
        width:7px;height:7px;background:var(--accent);
        border-radius:50%;border:1.5px solid var(--card);
    }

    /* PAGE CONTENT */
    .page-content{padding:1.8rem;}

    /* WELCOME BANNER */
    .welcome-banner{
        background:linear-gradient(135deg,var(--sky-900),var(--sky-700));
        border-radius:var(--radius);
        padding:1.8rem 2rem;
        color:#fff;
        position:relative;
        overflow:hidden;
        margin-bottom:1.8rem;
    }
    .welcome-banner::before{
        content:'';position:absolute;inset:0;
        background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);
        background-size:24px 24px;
    }
    .welcome-banner::after{
        content:'\f015';
        font-family:'Font Awesome 6 Free';font-weight:900;
        position:absolute;right:2rem;top:50%;transform:translateY(-50%);
        font-size:5rem;color:rgba(255,255,255,.06);
    }
    .welcome-title{font-family:var(--font-d);font-size:1.4rem;font-weight:800;margin-bottom:.3rem;}
    .welcome-sub{color:rgba(255,255,255,.65);font-size:.88rem;}
    .welcome-reg{
        display:inline-flex;align-items:center;gap:.4rem;
        background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
        color:rgba(255,255,255,.8);padding:.3rem .9rem;border-radius:100px;
        font-size:.78rem;font-weight:600;margin-top:.8rem;
    }

    /* STAT CARDS */
    .stat-card{
        background:var(--card);border:1px solid var(--border);
        border-radius:var(--radius);padding:1.3rem 1.5rem;
        display:flex;align-items:center;gap:1rem;
        transition:.3s;box-shadow:var(--shadow);
    }
    .stat-card:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(10,22,40,.1);}
    .stat-icon-wrap{
        width:52px;height:52px;border-radius:12px;
        display:flex;align-items:center;justify-content:center;
        font-size:1.2rem;flex-shrink:0;
    }
    .si-blue{background:rgba(59,130,246,.1);color:#3b82f6;}
    .si-orange{background:rgba(232,86,42,.1);color:var(--accent);}
    .si-green{background:rgba(52,211,153,.1);color:#34d399;}
    .si-purple{background:rgba(139,92,246,.1);color:#8b5cf6;}
    .stat-value{font-family:var(--font-d);font-size:1.5rem;font-weight:800;line-height:1;}
    .stat-label{font-size:.78rem;color:var(--muted);margin-top:.2rem;}

    /* ROOM CARD */
    .room-info-card{
        background:linear-gradient(135deg,var(--sky-800),var(--sky-600));
        border-radius:var(--radius);padding:1.5rem;color:#fff;
        box-shadow:var(--shadow);
    }
    .room-card-label{font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:.3rem;}
    .room-card-number{font-family:var(--font-d);font-size:2.2rem;font-weight:800;line-height:1;}
    .room-card-type{font-size:.85rem;color:rgba(255,255,255,.7);margin-top:.2rem;}
    .room-chip{display:inline-flex;align-items:center;gap:.3rem;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.8);padding:.25rem .7rem;border-radius:100px;font-size:.72rem;margin:.2rem .2rem 0 0;}

    /* NO ROOM CARD */
    .no-room-card{
        background:var(--card);border:2px dashed var(--border);
        border-radius:var(--radius);padding:2rem;text-align:center;
    }
    .no-room-card i{font-size:2.5rem;color:var(--border);margin-bottom:1rem;display:block;}
    .btn-apply{
        background:linear-gradient(135deg,var(--accent),#c0410f);
        color:#fff;border:none;padding:.65rem 1.5rem;border-radius:100px;
        font-family:var(--font-d);font-size:.88rem;font-weight:700;
        cursor:pointer;transition:.3s;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;
    }
    .btn-apply:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(232,86,42,.3);color:#fff;}

    /* SECTION CARD */
    .section-card{
        background:var(--card);border:1px solid var(--border);
        border-radius:var(--radius);overflow:hidden;
        box-shadow:var(--shadow);
    }
    .section-header{
        padding:1rem 1.3rem;border-bottom:1px solid var(--border);
        display:flex;align-items:center;justify-content:space-between;
    }
    .section-header-title{font-family:var(--font-d);font-size:.95rem;font-weight:700;}
    .view-all{font-size:.78rem;color:var(--sky-600);font-weight:600;text-decoration:none;}
    .view-all:hover{color:var(--accent);}

    /* NOTICE LIST */
    .notice-item{
        padding:.9rem 1.3rem;border-bottom:1px solid var(--border);
        display:flex;align-items:flex-start;gap:.8rem;
        transition:.2s;
    }
    .notice-item:last-child{border-bottom:none;}
    .notice-item:hover{background:var(--bg);}
    .notice-dot{width:8px;height:8px;border-radius:50%;background:var(--sky-600);margin-top:.4rem;flex-shrink:0;}
    .notice-dot.pinned{background:var(--accent);}
    .notice-title{font-size:.88rem;font-weight:600;margin-bottom:.15rem;}
    .notice-meta{font-size:.75rem;color:var(--muted);}
    .notice-cat{display:inline-block;padding:.1rem .5rem;border-radius:100px;font-size:.68rem;font-weight:600;margin-left:.4rem;}
    .cat-general{background:rgba(59,130,246,.1);color:#3b82f6;}
    .cat-urgent{background:rgba(232,86,42,.1);color:var(--accent);}
    .cat-maintenance{background:rgba(245,166,35,.1);color:var(--accent2);}
    .cat-event{background:rgba(139,92,246,.1);color:#8b5cf6;}

    /* FEE TABLE */
    .fee-table{width:100%;border-collapse:collapse;}
    .fee-table th{font-size:.75rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);padding:.7rem 1.3rem;border-bottom:1px solid var(--border);text-align:left;}
    .fee-table td{font-size:.85rem;padding:.8rem 1.3rem;border-bottom:1px solid var(--border);}
    .fee-table tr:last-child td{border-bottom:none;}
    .fee-table tr:hover td{background:var(--bg);}

    /* QUICK ACTIONS */
    .quick-action{
        background:var(--card);border:1px solid var(--border);
        border-radius:var(--radius);padding:1.2rem;
        text-align:center;text-decoration:none;color:var(--text);
        transition:.3s;display:block;
    }
    .quick-action:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(10,22,40,.1);border-color:var(--sky-600);color:var(--text);}
    .qa-icon{font-size:1.5rem;margin-bottom:.5rem;display:block;}
    .qa-label{font-size:.8rem;font-weight:600;}

    /* SIDEBAR OVERLAY */
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}

    @media(max-width:991px){
        .sidebar{left:calc(-1 * var(--sidebar-w));}
        .sidebar.open{left:0;}
        .sidebar-overlay.open{display:block;}
        .main-content{margin-left:0;}
        .menu-toggle{display:flex;}
    }
    </style>
</head>
<body>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><span>Sky</span>line Hostel</div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?php if ($student['profile_pic'] && $student['profile_pic'] !== 'default-student.png' && file_exists(UPLOAD_PATH . $student['profile_pic'])): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($student['profile_pic']) ?>" alt="pic">
            <?php else: ?>
                <?= strtoupper(substr($student['name'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <div class="user-name"><?= htmlspecialchars($student['name']) ?></div>
            <div class="user-reg"><?= htmlspecialchars($student['reg_no']) ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="dashboard.php" class="nav-item-link active">
            <i class="nav-icon fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="profile.php" class="nav-item-link">
            <i class="nav-icon fa-solid fa-user"></i> My Profile
        </a>

        <div class="nav-section-label">Hostel</div>
        <a href="my-room.php" class="nav-item-link">
            <i class="nav-icon fa-solid fa-door-open"></i> My Room
        </a>
        <a href="apply-room.php" class="nav-item-link">
            <i class="nav-icon fa-solid fa-file-circle-plus"></i> Apply for Room
        </a>
        <a href="complaints.php" class="nav-item-link">
            <i class="nav-icon fa-solid fa-triangle-exclamation"></i> Complaints
            <?php if ($complaints_open > 0): ?>
            <span class="nav-badge"><?= $complaints_open ?></span>
            <?php endif; ?>
        </a>
        <a href="notices.php" class="nav-item-link">
            <i class="nav-icon fa-solid fa-bullhorn"></i> Notices
        </a>

        <div class="nav-section-label">Finance</div>
        <a href="fees.php" class="nav-item-link">
            <i class="nav-icon fa-solid fa-receipt"></i> Fee Status
            <?php if ($fees_pending > 0): ?>
            <span class="nav-badge">!</span>
            <?php endif; ?>
        </a>

        <div class="nav-section-label">Account</div>
        <a href="profile.php" class="nav-link-item">
            <i class="nav-icon fa-solid fa-key"></i> Change Password
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="page-title">Dashboard</div>
        </div>
        <div class="topbar-right">
            <button class="theme-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
            <div class="notif-btn"><i class="fa-solid fa-bell"></i><div class="notif-dot"></div></div>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
            <div class="welcome-title">Welcome back, <?= htmlspecialchars(explode(' ', $student['name'])[0]) ?>! 👋</div>
            <div class="welcome-sub">Here's what's happening with your hostel account today.</div>
            <div class="welcome-reg"><i class="fa-solid fa-id-badge"></i> <?= htmlspecialchars($student['reg_no']) ?></div>
        </div>

        <!-- STAT CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon-wrap si-blue"><i class="fa-solid fa-door-open"></i></div>
                    <div>
                        <div class="stat-value"><?= $allocation ? 'Room '.$allocation['room_number'] : 'None' ?></div>
                        <div class="stat-label">Allocated Room</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon-wrap si-orange"><i class="fa-solid fa-receipt"></i></div>
                    <div>
                        <div class="stat-value">Rs.<?= number_format($fees_pending) ?></div>
                        <div class="stat-label">Pending Fees</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon-wrap si-purple"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="stat-value"><?= $complaints_total ?></div>
                        <div class="stat-label">Total Complaints</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon-wrap si-green"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div class="stat-value"><?= ucfirst($student['status']) ?></div>
                        <div class="stat-label">Account Status</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- ROOM INFO -->
            <div class="col-lg-4">
                <?php if ($allocation): ?>
                <div class="room-info-card h-100">
                    <div class="room-card-label">Your Room</div>
                    <div class="room-card-number"><?= htmlspecialchars($allocation['room_number']) ?></div>
                    <div class="room-card-type"><?= htmlspecialchars($allocation['room_type']) ?> · <?= htmlspecialchars($allocation['floor']) ?></div>
                    <div style="margin-top:1rem;">
                        <?php foreach (explode(',', $allocation['amenities']) as $am): ?>
                        <span class="room-chip"><i class="fa-solid fa-check" style="font-size:.6rem;"></i><?= trim(htmlspecialchars($am)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top:1.2rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.1);display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-size:.72rem;color:rgba(255,255,255,.5);">Monthly Fee</div>
                            <div style="font-family:var(--font-d);font-size:1.2rem;font-weight:800;color:var(--accent2);"><?= formatMoney($allocation['monthly_fee']) ?></div>
                        </div>
                        <span style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.3);color:#34d399;padding:.3rem .8rem;border-radius:100px;font-size:.75rem;font-weight:600;">Active</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-room-card h-100">
                    <i class="fa-solid fa-door-open"></i>
                    <div style="font-family:var(--font-d);font-weight:700;margin-bottom:.4rem;">No Room Assigned</div>
                    <div style="font-size:.85rem;color:var(--muted);margin-bottom:1.2rem;">
                        <?php if ($application): ?>
                            Your application is <strong><?= ucfirst($application['status']) ?></strong>. Please wait for admin approval.
                        <?php else: ?>
                            Apply for a room to get started!
                        <?php endif; ?>
                    </div>
                    <?php if (!$application || $application['status'] === 'rejected'): ?>
                    <a href="apply-room.php" class="btn-apply">
                        <i class="fa-solid fa-file-circle-plus"></i> Apply for Room
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="col-lg-8">
                <div class="section-card h-100">
                    <div class="section-header">
                        <div class="section-header-title"><i class="fa-solid fa-bolt" style="color:var(--accent2);"></i> Quick Actions</div>
                    </div>
                    <div style="padding:1.2rem;">
                        <div class="row g-2">
                            <?php
                            $actions = [
                                ['icon'=>'fa-user-pen',         'color'=>'#3b82f6', 'label'=>'Edit Profile',    'href'=>'profile.php'],
                                ['icon'=>'fa-file-circle-plus', 'color'=>'#e8562a', 'label'=>'Apply Room',      'href'=>'apply-room.php'],
                                ['icon'=>'fa-triangle-exclamation','color'=>'#f5a623','label'=>'Complaint',     'href'=>'complaints.php'],
                                ['icon'=>'fa-receipt',          'color'=>'#8b5cf6', 'label'=>'View Fees',       'href'=>'fees.php'],
                                ['icon'=>'fa-bullhorn',         'color'=>'#34d399', 'label'=>'Notices',         'href'=>'notices.php'],
                                ['icon'=>'fa-key', 'color'=>'#ec4899', 'label'=>'Change Pass', 'href'=>'profile.php'],
                            ];
                            foreach ($actions as $a): ?>
                            <div class="col-4 col-md-2">
                                <a href="<?= $a['href'] ?>" class="quick-action">
                                    <span class="qa-icon" style="color:<?= $a['color'] ?>"><i class="fa-solid <?= $a['icon'] ?>"></i></span>
                                    <span class="qa-label"><?= $a['label'] ?></span>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- NOTICES -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-title"><i class="fa-solid fa-bullhorn" style="color:var(--sky-600);"></i> Latest Notices</div>
                        <a href="notices.php" class="view-all">View All →</a>
                    </div>
                    <?php if ($notices && $notices->num_rows > 0):
                        while ($n = $notices->fetch_assoc()):
                        $catClass = 'cat-' . strtolower($n['category']);
                    ?>
                    <div class="notice-item">
                        <div class="notice-dot <?= $n['is_pinned'] ? 'pinned' : '' ?>"></div>
                        <div style="flex:1;min-width:0;">
                            <div class="notice-title">
                                <?= htmlspecialchars($n['title']) ?>
                                <span class="notice-cat <?= $catClass ?>"><?= $n['category'] ?></span>
                            </div>
                            <div class="notice-meta"><?= formatDate($n['created_at']) ?></div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem;">No notices yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FEE STATUS -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-title"><i class="fa-solid fa-receipt" style="color:var(--sky-600);"></i> Fee Records</div>
                        <a href="fees.php" class="view-all">View All →</a>
                    </div>
                    <?php if ($fees && $fees->num_rows > 0): ?>
                    <table class="fee-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($f = $fees->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['fee_month']) ?> <?= $f['fee_year'] ?></td>
                            <td><?= formatMoney($f['amount']) ?></td>
                            <td><?= statusBadge($f['status']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem;">
                        <i class="fa-solid fa-receipt fa-2x" style="color:var(--border);display:block;margin-bottom:.8rem;"></i>
                        No fee records yet.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /page-content -->
</div><!-- /main-content -->

<!-- Toast -->
<?php if (isset($_SESSION['toast'])): ?>
<div style="position:fixed;bottom:24px;right:24px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:.9rem 1.3rem;box-shadow:0 20px 60px rgba(0,0,0,.15);font-size:.88rem;z-index:9999;display:flex;align-items:center;gap:.7rem;min-width:280px;border-left:4px solid #34d399;" id="toastMsg">
    ✅ <?= htmlspecialchars($_SESSION['toast']['message']) ?>
    <button onclick="document.getElementById('toastMsg').remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:var(--muted);">&times;</button>
</div>
<?php unset($_SESSION['toast']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Theme
const html = document.documentElement;
const stored = localStorage.getItem('skyline-theme') || 'light';
html.setAttribute('data-theme', stored);
document.getElementById('themeIcon').className = stored === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
document.getElementById('themeBtn').addEventListener('click', () => {
    const cur = html.getAttribute('data-theme');
    const next = cur === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('skyline-theme', next);
    document.getElementById('themeIcon').className = next === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
});

// Sidebar toggle
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('open');
}

// Auto hide toast
setTimeout(() => {
    const t = document.getElementById('toastMsg');
    if (t) t.remove();
}, 4000);
</script>
</body>
</html>