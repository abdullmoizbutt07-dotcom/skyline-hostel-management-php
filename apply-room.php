<?php
// apply-room.php
require_once '../includes/db.php';
requireStudent();
$sid = $_SESSION['student_id'];

// Check existing application
$existing = $conn->query("SELECT * FROM room_applications WHERE student_id=$sid ORDER BY applied_at DESC LIMIT 1")->fetch_assoc();
$allocation = $conn->query("SELECT ra.*,r.room_number FROM room_allocations ra JOIN rooms r ON ra.room_id=r.id WHERE ra.student_id=$sid AND ra.status='active' LIMIT 1")->fetch_assoc();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ptype  = sanitize($conn, $_POST['preferred_type'] ?? '');
    $pfloor = sanitize($conn, $_POST['preferred_floor'] ?? '');
    $msg    = sanitize($conn, $_POST['message'] ?? '');

    if ($allocation) { $errors[] = 'You already have an allocated room.'; }
    elseif ($existing && $existing['status'] === 'pending') { $errors[] = 'You already have a pending application.'; }
    else {
        $conn->query("INSERT INTO room_applications (student_id,preferred_type,preferred_floor,message) VALUES ($sid,'$ptype','$pfloor','$msg')");
        setToast('success', 'Room application submitted!');
        redirect(SITE_URL . '/student/apply-room.php');
    }
}
$available_rooms = $conn->query("SELECT * FROM rooms WHERE status='available' ORDER BY monthly_fee ASC");
$student = $conn->query("SELECT name,reg_no,profile_pic FROM students WHERE id=$sid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Room — Skyline Hostel</title>
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
    .form-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .form-card-head{padding:1.1rem 1.4rem;border-bottom:1px solid var(--border);font-family:var(--font-d);font-size:.95rem;font-weight:700;display:flex;align-items:center;gap:.5rem;}
    .form-card-body{padding:1.4rem;}
    label{font-size:.82rem;font-weight:600;margin-bottom:.35rem;display:block;}
    .form-control,.form-select{background:var(--bg);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.65rem 1rem;font-size:.88rem;width:100%;transition:.25s;font-family:var(--font-b);}
    .form-control:focus,.form-select:focus{outline:none;border-color:var(--sky-600);}
    textarea.form-control{resize:vertical;min-height:80px;}
    .btn-submit{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.75rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.92rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(232,86,42,.35);}
    .error-box{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);border-radius:10px;padding:.9rem 1rem;margin-bottom:1.2rem;}
    .error-box li{font-size:.85rem;color:var(--accent);}
    .status-banner{border-radius:var(--radius);padding:1.2rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;}
    .sb-pending{background:rgba(245,166,35,.08);border:1px solid rgba(245,166,35,.25);}
    .sb-approved{background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.25);}
    .sb-rejected{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);}
    /* Room cards */
    .room-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.8rem;}
    .room-item{background:var(--bg);border:2px solid var(--border);border-radius:12px;padding:1rem;cursor:pointer;transition:.25s;text-align:center;}
    .room-item:hover{border-color:var(--sky-600);}
    .ri-num{font-family:var(--font-d);font-size:1.4rem;font-weight:800;}
    .ri-type{font-size:.75rem;color:var(--muted);}
    .ri-price{font-size:.82rem;font-weight:700;color:var(--accent);margin-top:.3rem;}
    .ri-spots{font-size:.72rem;color:#34d399;font-weight:600;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}
    @media(max-width:991px){.sidebar{left:calc(-1 * var(--sidebar-w));}.sidebar.open{left:0;}.sidebar-overlay.open{display:block;}.main-content{margin-left:0;}.menu-toggle{display:flex !important;}}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><span>Sky</span>line Hostel</div>
    <div class="sidebar-user">
        <div class="user-avatar">
            <?php if($student['profile_pic'] && $student['profile_pic']!=='default-student.png' && file_exists(UPLOAD_PATH.$student['profile_pic'])): ?>
            <img src="<?= UPLOAD_URL.htmlspecialchars($student['profile_pic']) ?>" alt="">
            <?php else: ?><?= strtoupper(substr($student['name'],0,1)) ?><?php endif; ?>
        </div>
        <div><div class="user-name"><?= htmlspecialchars($student['name']) ?></div><div class="user-reg"><?= htmlspecialchars($student['reg_no']) ?></div></div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-sec">Main</div>
        <a href="dashboard.php" class="nav-link-item"><i class="ni fa-solid fa-gauge"></i> Dashboard</a>
        <a href="profile.php" class="nav-link-item"><i class="ni fa-solid fa-user"></i> My Profile</a>
        <div class="nav-sec">Hostel</div>
        <a href="my-room.php" class="nav-link-item"><i class="ni fa-solid fa-door-open"></i> My Room</a>
        <a href="apply-room.php" class="nav-link-item active"><i class="ni fa-solid fa-file-circle-plus"></i> Apply for Room</a>
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
            <div class="page-title">Apply for Room</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>
    <div class="page-content">

        <!-- Status Banner -->
        <?php if($allocation): ?>
        <div class="status-banner sb-approved">
            <i class="fa-solid fa-circle-check fa-2x" style="color:#34d399;"></i>
            <div>
                <div style="font-family:var(--font-d);font-weight:700;color:#34d399;">Room Already Allocated!</div>
                <div style="font-size:.85rem;color:var(--muted);">You have been assigned Room <?= htmlspecialchars($allocation['room_number']) ?>. Go to <a href="my-room.php" style="color:var(--sky-600);">My Room</a> to view details.</div>
            </div>
        </div>
        <?php elseif($existing && $existing['status']==='pending'): ?>
        <div class="status-banner sb-pending">
            <i class="fa-solid fa-clock fa-2x" style="color:#f5a623;"></i>
            <div>
                <div style="font-family:var(--font-d);font-weight:700;color:#f5a623;">Application Pending!</div>
                <div style="font-size:.85rem;color:var(--muted);">Your application is under review. Please wait for admin approval.</div>
            </div>
        </div>
        <?php elseif($existing && $existing['status']==='rejected'): ?>
        <div class="status-banner sb-rejected" style="margin-bottom:1rem;">
            <i class="fa-solid fa-circle-xmark fa-2x" style="color:#e8562a;"></i>
            <div>
                <div style="font-family:var(--font-d);font-weight:700;color:#e8562a;">Previous Application Rejected</div>
                <div style="font-size:.85rem;color:var(--muted);"><?= htmlspecialchars($existing['admin_note'] ?: 'You may submit a new application.') ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($errors)): ?>
        <div class="error-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <?php if(!$allocation && (!$existing || $existing['status']==='rejected')): ?>
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="form-card">
                    <div class="form-card-head"><i class="fa-solid fa-file-circle-plus" style="color:var(--sky-600);"></i> Room Application Form</div>
                    <div class="form-card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Preferred Room Type</label>
                                    <select name="preferred_type" class="form-select">
                                        <option value="">No preference</option>
                                        <option value="Single">Single (1 bed)</option>
                                        <option value="Double">Double (2 beds)</option>
                                        <option value="Triple">Triple (3 beds)</option>
                                        <option value="Quad">Quad (4 beds)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Preferred Floor</label>
                                    <select name="preferred_floor" class="form-select">
                                        <option value="">No preference</option>
                                        <option value="Ground Floor">Ground Floor</option>
                                        <option value="First Floor">First Floor</option>
                                        <option value="Second Floor">Second Floor</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Message to Admin (Optional)</label>
                                    <textarea name="message" class="form-control" placeholder="Any special requirements or notes..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> Submit Application</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="form-card">
                    <div class="form-card-head"><i class="fa-solid fa-door-open" style="color:var(--sky-600);"></i> Available Rooms</div>
                    <div class="form-card-body">
                        <div class="room-grid">
                            <?php if($available_rooms && $available_rooms->num_rows>0):
                                while($r=$available_rooms->fetch_assoc()):
                                $spots=$r['capacity']-$r['occupied'];
                            ?>
                            <div class="room-item">
                                <div class="ri-num"><?= htmlspecialchars($r['room_number']) ?></div>
                                <div class="ri-type"><?= $r['room_type'] ?> · <?= $r['floor'] ?></div>
                                <div class="ri-price"><?= formatMoney($r['monthly_fee']) ?>/mo</div>
                                <div class="ri-spots"><?= $spots ?> spot<?= $spots!=1?'s':'' ?> left</div>
                            </div>
                            <?php endwhile; else: ?>
                            <p style="color:var(--muted);font-size:.85rem;grid-column:1/-1;">No rooms available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_SESSION['toast'])): ?>
<div style="position:fixed;bottom:24px;right:24px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:.9rem 1.3rem;box-shadow:0 20px 60px rgba(0,0,0,.15);font-size:.88rem;z-index:9999;display:flex;align-items:center;gap:.7rem;min-width:280px;border-left:4px solid #34d399;" id="toastMsg">
    ✅ <?= htmlspecialchars($_SESSION['toast']['message']) ?>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;">&times;</button>
</div>
<?php unset($_SESSION['toast']); endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const html=document.documentElement,stored=localStorage.getItem('skyline-theme')||'light';
html.setAttribute('data-theme',stored);document.getElementById('themeIcon').className=stored==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';
document.getElementById('themeBtn').addEventListener('click',()=>{const cur=html.getAttribute('data-theme'),next=cur==='dark'?'light':'dark';html.setAttribute('data-theme',next);localStorage.setItem('skyline-theme',next);document.getElementById('themeIcon').className=next==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';});
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
setTimeout(()=>{const t=document.getElementById('toastMsg');if(t)t.remove();},4000);
</script>
</body>
</html>