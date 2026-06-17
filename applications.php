<?php
require_once '../includes/db.php';
requireAdmin();

// Update application status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aid    = (int)$_POST['app_id'];
    $status = sanitize($conn, $_POST['status']);
    $note   = sanitize($conn, $_POST['admin_note'] ?? '');
    $conn->query("UPDATE room_applications SET status='$status', admin_note='$note' WHERE id=$aid");
    setToast('success', 'Application updated!');
    redirect(SITE_URL . '/admin/applications.php');
}

$apps = $conn->query("
    SELECT ra.*, s.name as student_name, s.reg_no, s.course, s.gender
    FROM room_applications ra
    JOIN students s ON ra.student_id = s.id
    ORDER BY FIELD(ra.status,'pending','approved','rejected'), ra.applied_at DESC
");

$admin = $conn->query("SELECT * FROM admins WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
$open_complaints = getCount($conn,'complaints',"status='pending'");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications — Skyline Hostel Admin</title>
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
    .user-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--sky-600),var(--sky-700));display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;}
    .user-name{font-size:.85rem;font-weight:700;color:#fff;}.user-role{font-size:.7rem;color:rgba(255,255,255,.45);}
    .sidebar-nav{padding:1rem 0;flex:1;}
    .nav-sec{font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.3);padding:.5rem 1.5rem;margin-top:.5rem;}
    .nav-link-item{display:flex;align-items:center;gap:.8rem;padding:.7rem 1.5rem;color:rgba(255,255,255,.6);font-size:.88rem;font-weight:500;text-decoration:none;transition:.2s;border-left:3px solid transparent;}
    .nav-link-item:hover{background:rgba(255,255,255,.05);color:#fff;}
    .nav-link-item.active{background:rgba(232,86,42,.12);color:#fff;border-left-color:var(--accent);}
    .nav-link-item .ni{width:18px;text-align:center;font-size:.9rem;}
    .nav-badge{margin-left:auto;background:var(--accent);color:#fff;font-size:.65rem;font-weight:700;padding:.15rem .45rem;border-radius:100px;}
    .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.07);}
    .logout-btn{display:flex;align-items:center;gap:.7rem;color:rgba(255,255,255,.5);font-size:.85rem;text-decoration:none;transition:.2s;padding:.5rem 0;}
    .logout-btn:hover{color:var(--accent);}
    .main-content{margin-left:var(--sidebar-w);min-height:100vh;transition:.3s;}
    .topbar{background:var(--card);border-bottom:1px solid var(--border);padding:.9rem 1.8rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:var(--shadow);}
    .topbar-left{display:flex;align-items:center;gap:.8rem;}
    .menu-toggle{background:none;border:none;color:var(--text);font-size:1.1rem;cursor:pointer;padding:.3rem;display:none;}
    .page-title{font-family:var(--font-d);font-size:1.1rem;font-weight:700;}
    .icon-btn{width:36px;height:36px;border-radius:50%;background:var(--bg);border:1.5px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.85rem;transition:.25s;}
    .icon-btn:hover{border-color:var(--accent);color:var(--accent);}
    .page-content{padding:1.8rem;}
    .app-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.3rem;margin-bottom:1rem;box-shadow:var(--shadow);transition:.3s;}
    .app-card:hover{box-shadow:0 8px 25px rgba(10,22,40,.1);}
    .app-card.pending{border-left:4px solid #f5a623;}
    .app-card.approved{border-left:4px solid #34d399;}
    .app-card.rejected{border-left:4px solid #e8562a;}
    .app-top{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:.8rem;flex-wrap:wrap;}
    .app-student{display:flex;align-items:center;gap:.7rem;}
    .app-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));color:#fff;font-family:var(--font-d);font-size:.95rem;font-weight:700;display:flex;align-items:center;justify-content:center;}
    .app-name{font-family:var(--font-d);font-size:.95rem;font-weight:700;}
    .app-reg{font-size:.75rem;color:var(--muted);}
    .app-details{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.8rem;}
    .app-chip{background:var(--bg);border:1px solid var(--border);color:var(--muted);padding:.25rem .7rem;border-radius:100px;font-size:.75rem;font-weight:500;}
    .app-message{font-size:.85rem;color:var(--muted);padding:.8rem;background:var(--bg);border-radius:8px;margin-bottom:.9rem;line-height:1.7;}
    .app-note{font-size:.82rem;color:var(--sky-600);padding:.6rem;background:rgba(26,58,110,.06);border-radius:8px;border:1px solid rgba(26,58,110,.1);margin-bottom:.8rem;}
    .app-actions{display:flex;gap:.5rem;flex-wrap:wrap;}
    .btn-approve{background:rgba(52,211,153,.1);color:#34d399;border:1px solid rgba(52,211,153,.3);padding:.4rem 1rem;border-radius:100px;font-size:.8rem;font-weight:700;cursor:pointer;transition:.2s;}
    .btn-approve:hover{background:#34d399;color:#fff;}
    .btn-reject{background:rgba(232,86,42,.1);color:var(--accent);border:1px solid rgba(232,86,42,.3);padding:.4rem 1rem;border-radius:100px;font-size:.8rem;font-weight:700;cursor:pointer;transition:.2s;}
    .btn-reject:hover{background:var(--accent);color:#fff;}
    .btn-update{background:rgba(59,130,246,.1);color:#3b82f6;border:1px solid rgba(59,130,246,.3);padding:.4rem 1rem;border-radius:100px;font-size:.8rem;font-weight:700;cursor:pointer;transition:.2s;}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:1rem;}
    .modal-box{background:var(--card);border-radius:var(--radius);padding:2rem;max-width:440px;width:100%;box-shadow:0 30px 80px rgba(0,0,0,.3);animation:modal-in .3s ease;}
    @keyframes modal-in{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
    .modal-title{font-family:var(--font-d);font-size:1.1rem;font-weight:800;margin-bottom:1.2rem;}
    label{font-size:.82rem;font-weight:600;margin-bottom:.35rem;display:block;}
    .form-control,.form-select{background:var(--bg);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.65rem 1rem;font-size:.88rem;width:100%;transition:.25s;font-family:var(--font-b);}
    textarea.form-control{resize:vertical;min-height:80px;}
    .btn-save{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.7rem 1.5rem;border-radius:100px;font-family:var(--font-d);font-size:.9rem;font-weight:700;cursor:pointer;}
    .btn-close-modal{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.7rem 1.5rem;border-radius:100px;font-size:.9rem;cursor:pointer;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}
    @media(max-width:991px){.sidebar{left:calc(-1 * var(--sidebar-w));}.sidebar.open{left:0;}.sidebar-overlay.open{display:block;}.main-content{margin-left:0;}.menu-toggle{display:flex;}}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><span>Sky</span>line Hostel</div>
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($admin['name'],0,1)) ?></div>
        <div><div class="user-name"><?= htmlspecialchars($admin['name']) ?></div><div class="user-role">Administrator</div></div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-sec">Main</div>
        <a href="dashboard.php" class="nav-link-item"><i class="ni fa-solid fa-gauge"></i> Dashboard</a>
        <div class="nav-sec">Students</div>
        <a href="students.php" class="nav-link-item"><i class="ni fa-solid fa-users"></i> All Students</a>
        <a href="add-student.php" class="nav-link-item"><i class="ni fa-solid fa-user-plus"></i> Add Student</a>
        <div class="nav-sec">Rooms</div>
        <a href="rooms.php" class="nav-link-item"><i class="ni fa-solid fa-door-open"></i> Manage Rooms</a>
        <a href="allocate-room.php" class="nav-link-item"><i class="ni fa-solid fa-key"></i> Allocate Room</a>
        <a href="applications.php" class="nav-link-item active"><i class="ni fa-solid fa-file-circle-check"></i> Applications <span class="nav-badge"><?= getCount($conn,'room_applications',"status='pending'") ?></span></a>
        <div class="nav-sec">Operations</div>
        <a href="complaints.php" class="nav-link-item"><i class="ni fa-solid fa-triangle-exclamation"></i> Complaints <?php if($open_complaints>0): ?><span class="nav-badge"><?= $open_complaints ?></span><?php endif; ?></a>
        <a href="notices.php" class="nav-link-item"><i class="ni fa-solid fa-bullhorn"></i> Notices</a>
        <a href="fees.php" class="nav-link-item"><i class="ni fa-solid fa-receipt"></i> Fee Records</a>
        <div class="nav-sec">Account</div>
        <a href="profile.php" class="nav-link-item"><i class="ni fa-solid fa-user-shield"></i> My Profile</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></div>
</aside>

<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">Room Applications</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>

    <div class="page-content">
        <!-- Stats -->
        <div class="row g-3 mb-4">
            <?php
            $astats=[
                ['label'=>'Total','val'=>getCount($conn,'room_applications'),'color'=>'#3b82f6','icon'=>'fa-file'],
                ['label'=>'Pending','val'=>getCount($conn,'room_applications',"status='pending'"),'color'=>'#f5a623','icon'=>'fa-clock'],
                ['label'=>'Approved','val'=>getCount($conn,'room_applications',"status='approved'"),'color'=>'#34d399','icon'=>'fa-check'],
                ['label'=>'Rejected','val'=>getCount($conn,'room_applications',"status='rejected'"),'color'=>'#e8562a','icon'=>'fa-xmark'],
            ];
            foreach($astats as $as): ?>
            <div class="col-6 col-lg-3">
                <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.2rem;display:flex;align-items:center;gap:.9rem;box-shadow:var(--shadow);">
                    <div style="width:44px;height:44px;border-radius:11px;background:<?= $as['color'] ?>18;color:<?= $as['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;"><i class="fa-solid <?= $as['icon'] ?>"></i></div>
                    <div><div style="font-family:var(--font-d);font-size:1.4rem;font-weight:800;"><?= $as['val'] ?></div><div style="font-size:.75rem;color:var(--muted);"><?= $as['label'] ?></div></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if($apps && $apps->num_rows > 0):
            while($app = $apps->fetch_assoc()):
        ?>
        <div class="app-card <?= $app['status'] ?>">
            <div class="app-top">
                <div class="app-student">
                    <div class="app-avatar"><?= strtoupper(substr($app['student_name'],0,1)) ?></div>
                    <div>
                        <div class="app-name"><?= htmlspecialchars($app['student_name']) ?></div>
                        <div class="app-reg"><?= htmlspecialchars($app['reg_no']) ?> · <?= htmlspecialchars($app['course'] ?: 'N/A') ?></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <?= statusBadge($app['status']) ?>
                    <span style="font-size:.75rem;color:var(--muted);"><?= formatDate($app['applied_at']) ?></span>
                </div>
            </div>
            <div class="app-details">
                <?php if($app['preferred_type']): ?>
                <span class="app-chip"><i class="fa-solid fa-bed"></i> Preferred: <?= $app['preferred_type'] ?></span>
                <?php endif; ?>
                <?php if($app['preferred_floor']): ?>
                <span class="app-chip"><i class="fa-solid fa-layer-group"></i> Floor: <?= $app['preferred_floor'] ?></span>
                <?php endif; ?>
                <span class="app-chip"><i class="fa-solid fa-venus-mars"></i> <?= $app['gender'] ?></span>
            </div>
            <?php if($app['message']): ?>
            <div class="app-message"><?= nl2br(htmlspecialchars($app['message'])) ?></div>
            <?php endif; ?>
            <?php if($app['admin_note']): ?>
            <div class="app-note"><i class="fa-solid fa-reply"></i> <strong>Admin Note:</strong> <?= htmlspecialchars($app['admin_note']) ?></div>
            <?php endif; ?>
            <div class="app-actions">
                <?php if($app['status'] === 'pending'): ?>
                <button onclick="quickUpdate(<?= $app['id'] ?>, 'approved')" class="btn-approve"><i class="fa-solid fa-check"></i> Approve</button>
                <button onclick="quickUpdate(<?= $app['id'] ?>, 'rejected')" class="btn-reject"><i class="fa-solid fa-xmark"></i> Reject</button>
                <?php endif; ?>
                <button onclick="openModal(<?= $app['id'] ?>, '<?= $app['status'] ?>', `<?= addslashes($app['admin_note']) ?>`)" class="btn-update"><i class="fa-solid fa-pen"></i> Update</button>
                <a href="allocate-room.php" style="background:rgba(26,58,110,.1);color:var(--sky-600);border:1px solid rgba(26,58,110,.2);padding:.4rem 1rem;border-radius:100px;font-size:.8rem;font-weight:700;text-decoration:none;"><i class="fa-solid fa-key"></i> Allocate Room</a>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:4rem;text-align:center;">
            <i class="fa-solid fa-file-circle-check fa-3x" style="color:var(--border);display:block;margin-bottom:1rem;"></i>
            <div style="font-family:var(--font-d);font-weight:700;">No Applications Yet</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Update Modal -->
<div class="modal-overlay" id="appModal">
    <div class="modal-box">
        <div class="modal-title"><i class="fa-solid fa-file-pen" style="color:var(--sky-600);"></i> Update Application</div>
        <form method="POST">
            <input type="hidden" name="app_id" id="appId">
            <div style="margin-bottom:1rem;">
                <label>Status</label>
                <select name="status" id="appStatus" class="form-select">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div style="margin-bottom:1.2rem;">
                <label>Admin Note</label>
                <textarea name="admin_note" id="appNote" class="form-control" placeholder="Optional note to student..."></textarea>
            </div>
            <div style="display:flex;gap:.7rem;">
                <button type="button" class="btn-close-modal" onclick="document.getElementById('appModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Update Form -->
<form method="POST" id="quickForm" style="display:none;">
    <input type="hidden" name="app_id" id="qAppId">
    <input type="hidden" name="status" id="qStatus">
    <input type="hidden" name="admin_note" value="">
</form>

<?php if(isset($_SESSION['toast'])): ?>
<div style="position:fixed;bottom:24px;right:24px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:.9rem 1.3rem;box-shadow:0 20px 60px rgba(0,0,0,.15);font-size:.88rem;z-index:9999;display:flex;align-items:center;gap:.7rem;min-width:280px;border-left:4px solid #34d399;" id="toastMsg">
    ✅ <?= htmlspecialchars($_SESSION['toast']['message']) ?>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:var(--muted);">&times;</button>
</div>
<?php unset($_SESSION['toast']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const html=document.documentElement,stored=localStorage.getItem('skyline-theme')||'light';
html.setAttribute('data-theme',stored);
document.getElementById('themeIcon').className=stored==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';
document.getElementById('themeBtn').addEventListener('click',()=>{const cur=html.getAttribute('data-theme'),next=cur==='dark'?'light':'dark';html.setAttribute('data-theme',next);localStorage.setItem('skyline-theme',next);document.getElementById('themeIcon').className=next==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';});
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
function openModal(id,status,note){document.getElementById('appId').value=id;document.getElementById('appStatus').value=status;document.getElementById('appNote').value=note;document.getElementById('appModal').style.display='flex';}
function quickUpdate(id,status){document.getElementById('qAppId').value=id;document.getElementById('qStatus').value=status;document.getElementById('quickForm').submit();}
setTimeout(()=>{const t=document.getElementById('toastMsg');if(t)t.remove();},4000);
</script>
</body>
</html>