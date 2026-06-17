<?php
require_once '../includes/db.php';
requireAdmin();

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $room_id    = (int)($_POST['room_id'] ?? 0);
    $alloc_date = sanitize($conn, $_POST['allocation_date'] ?? date('Y-m-d'));
    $notes      = sanitize($conn, $_POST['notes'] ?? '');

    if (!$student_id) $errors[] = 'Please select a student.';
    if (!$room_id)    $errors[] = 'Please select a room.';

    if (empty($errors)) {
        // Check student already has active room
        $existing = $conn->query("SELECT id FROM room_allocations WHERE student_id=$student_id AND status='active'")->fetch_assoc();
        if ($existing) $errors[] = 'This student already has an active room allocation.';

        // Check room capacity
        $room = $conn->query("SELECT * FROM rooms WHERE id=$room_id")->fetch_assoc();
        if ($room && $room['occupied'] >= $room['capacity']) $errors[] = 'This room is already full.';
    }

    if (empty($errors)) {
        $admin_id = $_SESSION['admin_id'];
        $sql = "INSERT INTO room_allocations (student_id,room_id,allocated_by,allocation_date,status,notes)
                VALUES ($student_id,$room_id,$admin_id,'$alloc_date','active','$notes')";
        if ($conn->query($sql)) {
            // Update room occupied count
            $conn->query("UPDATE rooms SET occupied=occupied+1 WHERE id=$room_id");
            // Update room status if full
            $conn->query("UPDATE rooms SET status='full' WHERE id=$room_id AND occupied>=capacity");
            // Update any pending application
            $conn->query("UPDATE room_applications SET status='approved' WHERE student_id=$student_id AND status='pending'");

            setToast('success', 'Room allocated successfully!');
            redirect(SITE_URL . '/admin/allocate-room.php');
        } else {
            $errors[] = 'Allocation failed. Please try again.';
        }
    }
}

// Get students without active room
$students = $conn->query("
    SELECT s.* FROM students s
    WHERE s.status='active'
    AND s.id NOT IN (SELECT student_id FROM room_allocations WHERE status='active')
    ORDER BY s.name ASC
");

// Get available rooms
$rooms = $conn->query("SELECT * FROM rooms WHERE status='available' ORDER BY room_number ASC");

// Recent allocations
$recent = $conn->query("
    SELECT ra.*, s.name as student_name, s.reg_no, r.room_number, r.room_type, r.floor
    FROM room_allocations ra
    JOIN students s ON ra.student_id=s.id
    JOIN rooms r ON ra.room_id=r.id
    ORDER BY ra.created_at DESC LIMIT 8
");

$admin = $conn->query("SELECT * FROM admins WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
$open_complaints = getCount($conn,'complaints',"status='pending'");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocate Room — Skyline Hostel Admin</title>
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
    .main-content{margin-left:var(--sidebar-w);min-height:100vh;}
    .topbar{background:var(--card);border-bottom:1px solid var(--border);padding:.9rem 1.8rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:var(--shadow);}
    .topbar-left{display:flex;align-items:center;gap:.8rem;}
    .menu-toggle{background:none;border:none;color:var(--text);font-size:1.1rem;cursor:pointer;padding:.3rem;display:none;}
    .page-title{font-family:var(--font-d);font-size:1.1rem;font-weight:700;}
    .icon-btn{width:36px;height:36px;border-radius:50%;background:var(--bg);border:1.5px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.85rem;transition:.25s;text-decoration:none;}
    .icon-btn:hover{border-color:var(--accent);color:var(--accent);}
    .page-content{padding:1.8rem;}
    .form-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .form-card-head{padding:1.2rem 1.5rem;border-bottom:1px solid var(--border);font-family:var(--font-d);font-size:1rem;font-weight:700;display:flex;align-items:center;gap:.5rem;}
    .form-card-body{padding:1.5rem;}
    label{font-size:.82rem;font-weight:600;margin-bottom:.35rem;display:block;}
    .form-control,.form-select{background:var(--bg);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.65rem 1rem;font-size:.88rem;width:100%;transition:.25s;font-family:var(--font-b);}
    .form-control:focus,.form-select:focus{outline:none;border-color:var(--sky-600);box-shadow:0 0 0 3px rgba(26,58,110,.1);background:var(--card);}
    textarea.form-control{resize:vertical;min-height:70px;}
    .error-box{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);border-radius:10px;padding:1rem;margin-bottom:1.5rem;}
    .error-box li{font-size:.85rem;color:var(--accent);}
    .btn-submit{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.75rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;width:100%;justify-content:center;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(232,86,42,.35);}
    /* Room selector */
    .room-options{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.6rem;max-height:300px;overflow-y:auto;}
    .room-radio{display:none;}
    .room-option-label{display:block;background:var(--bg);border:2px solid var(--border);border-radius:12px;padding:.8rem;cursor:pointer;transition:.25s;text-align:center;}
    .room-option-label:hover{border-color:var(--sky-600);}
    .room-radio:checked+.room-option-label{border-color:var(--accent);background:rgba(232,86,42,.06);}
    .ro-num{font-family:var(--font-d);font-size:1.2rem;font-weight:800;}
    .ro-type{font-size:.72rem;color:var(--muted);margin:.1rem 0;}
    .ro-spots{font-size:.72rem;font-weight:700;color:#34d399;}
    .ro-price{font-size:.78rem;font-weight:700;color:var(--accent);margin-top:.3rem;}
    /* Recent table */
    .data-table{width:100%;border-collapse:collapse;}
    .data-table th{font-size:.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);padding:.7rem 1rem;border-bottom:1px solid var(--border);text-align:left;background:var(--bg);}
    .data-table td{font-size:.83rem;padding:.8rem 1rem;border-bottom:1px solid var(--border);vertical-align:middle;}
    .data-table tr:last-child td{border-bottom:none;}
    .data-table tbody tr:hover td{background:var(--bg);}
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
        <a href="allocate-room.php" class="nav-link-item active"><i class="ni fa-solid fa-key"></i> Allocate Room</a>
        <a href="applications.php" class="nav-link-item"><i class="ni fa-solid fa-file-circle-check"></i> Applications</a>
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
            <div class="page-title">Allocate Room</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>

    <div class="page-content">
        <?php if(!empty($errors)): ?>
        <div class="error-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="row g-3">
            <!-- Allocation Form -->
            <div class="col-lg-5">
                <div class="form-card">
                    <div class="form-card-head"><i class="fa-solid fa-key" style="color:var(--sky-600);"></i> New Allocation</div>
                    <div class="form-card-body">
                        <form method="POST">
                            <div style="margin-bottom:1.1rem;">
                                <label>Select Student *</label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">-- Choose Student --</option>
                                    <?php
                                    if($students && $students->num_rows>0):
                                        while($s=$students->fetch_assoc()):
                                    ?>
                                    <option value="<?= $s['id'] ?>" <?= (($_POST['student_id']??0)==$s['id'])?'selected':'' ?>>
                                        <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['reg_no']) ?>)
                                    </option>
                                    <?php endwhile; else: ?>
                                    <option disabled>No unallocated students</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div style="margin-bottom:1.1rem;">
                                <label>Select Room *</label>
                                <div class="room-options">
                                    <?php
                                    if($rooms && $rooms->num_rows>0):
                                        while($r=$rooms->fetch_assoc()):
                                        $spots = $r['capacity'] - $r['occupied'];
                                    ?>
                                    <div>
                                        <input type="radio" name="room_id" id="room_<?= $r['id'] ?>" value="<?= $r['id'] ?>" class="room-radio" required <?= (($_POST['room_id']??0)==$r['id'])?'checked':'' ?>>
                                        <label for="room_<?= $r['id'] ?>" class="room-option-label">
                                            <div class="ro-num"><?= htmlspecialchars($r['room_number']) ?></div>
                                            <div class="ro-type"><?= $r['room_type'] ?> · <?= $r['floor'] ?></div>
                                            <div class="ro-spots"><?= $spots ?> spot<?= $spots!=1?'s':'' ?> left</div>
                                            <div class="ro-price"><?= formatMoney($r['monthly_fee']) ?></div>
                                        </label>
                                    </div>
                                    <?php endwhile; else: ?>
                                    <p style="color:var(--muted);font-size:.85rem;grid-column:1/-1;">No available rooms.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="margin-bottom:1.1rem;">
                                <label>Allocation Date *</label>
                                <input type="date" name="allocation_date" class="form-control" value="<?= htmlspecialchars($_POST['allocation_date']??date('Y-m-d')) ?>" required>
                            </div>

                            <div style="margin-bottom:1.5rem;">
                                <label>Notes (Optional)</label>
                                <textarea name="notes" class="form-control" placeholder="Any additional notes..."><?= htmlspecialchars($_POST['notes']??'') ?></textarea>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-key"></i> Allocate Room
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Allocations -->
            <div class="col-lg-7">
                <div class="form-card">
                    <div class="form-card-head"><i class="fa-solid fa-clock-rotate-left" style="color:var(--sky-600);"></i> Recent Allocations</div>
                    <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($recent && $recent->num_rows>0):
                            while($ra=$recent->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:.85rem;"><?= htmlspecialchars($ra['student_name']) ?></div>
                                <div style="font-size:.72rem;color:var(--muted);"><?= htmlspecialchars($ra['reg_no']) ?></div>
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.9rem;">Room <?= htmlspecialchars($ra['room_number']) ?></div>
                                <div style="font-size:.72rem;color:var(--muted);"><?= $ra['room_type'] ?> · <?= $ra['floor'] ?></div>
                            </td>
                            <td style="font-size:.78rem;color:var(--muted);"><?= formatDate($ra['allocation_date']) ?></td>
                            <td><?= statusBadge($ra['status']) ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:2rem;">No allocations yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
document.getElementById('themeBtn').addEventListener('click',()=>{
    const cur=html.getAttribute('data-theme'),next=cur==='dark'?'light':'dark';
    html.setAttribute('data-theme',next);localStorage.setItem('skyline-theme',next);
    document.getElementById('themeIcon').className=next==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';
});
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
setTimeout(()=>{const t=document.getElementById('toastMsg');if(t)t.remove();},4000);
</script>
</body>
</html>