<?php
require_once '../includes/db.php';
requireAdmin();

// Delete student
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$did");
    setToast('success', 'Student deleted successfully.');
    redirect(SITE_URL . '/admin/students.php');
}

// Filters
$search = sanitize($conn, $_GET['search'] ?? '');
$status = sanitize($conn, $_GET['status'] ?? '');
$gender = sanitize($conn, $_GET['gender'] ?? '');

$where = "1";
if ($search) $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reg_no LIKE '%$search%' OR phone LIKE '%$search%')";
if ($status) $where .= " AND status='$status'";
if ($gender) $where .= " AND gender='$gender'";

$total = getCount($conn, 'students', $where);

// Pagination
$per_page = 10;
$page     = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($page - 1) * $per_page;
$pages    = ceil($total / $per_page);

$students = $conn->query("SELECT * FROM students WHERE $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students — Skyline Hostel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    :root{--sky-900:#0a1628;--sky-800:#0d1f3c;--sky-700:#112952;--sky-600:#1a3a6e;--accent:#e8562a;--accent2:#f5a623;--bg:#f0f4ff;--card:#fff;--border:#e2e8f0;--text:#0a1628;--muted:#718096;--sidebar-w:260px;--font-d:'Syne',sans-serif;--font-b:'DM Sans',sans-serif;--radius:14px;--shadow:0 4px 20px rgba(10,22,40,.08);}
    [data-theme="dark"]{--bg:#07101f;--card:#0d1f3c;--border:#1a2d4f;--text:#f0f4ff;--muted:#718096;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-b);background:var(--bg);color:var(--text);transition:.3s;}
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);background:var(--sky-900);display:flex;flex-direction:column;z-index:100;overflow-y:auto;transition:.3s;}
    .sidebar-brand{padding:1.5rem;border-bottom:1px solid rgba(255,255,255,.07);font-family:var(--font-d);font-size:1.3rem;font-weight:800;color:#fff;}
    .sidebar-brand span{color:var(--accent2);}
    .sidebar-user{padding:1.2rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:.8rem;}
    .user-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--sky-600),var(--sky-700));display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:1rem;font-weight:700;color:#fff;flex-shrink:0;}
    .user-name{font-size:.85rem;font-weight:700;color:#fff;}
    .user-role{font-size:.7rem;color:rgba(255,255,255,.45);}
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
    .topbar-right{display:flex;align-items:center;gap:.8rem;}
    .icon-btn{width:36px;height:36px;border-radius:50%;background:var(--bg);border:1.5px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.85rem;transition:.25s;text-decoration:none;}
    .icon-btn:hover{border-color:var(--accent);color:var(--accent);}
    .page-content{padding:1.8rem;}
    .sec-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .sec-head{padding:1rem 1.3rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem;}
    .sec-title{font-family:var(--font-d);font-size:.95rem;font-weight:700;display:flex;align-items:center;gap:.5rem;}
    .btn-add{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.55rem 1.2rem;border-radius:100px;font-family:var(--font-d);font-size:.85rem;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;transition:.3s;}
    .btn-add:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(232,86,42,.3);color:#fff;}
    .filter-bar{padding:1rem 1.3rem;border-bottom:1px solid var(--border);background:var(--bg);display:flex;flex-wrap:wrap;gap:.7rem;align-items:center;}
    .filter-input{background:var(--card);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.5rem .9rem;font-size:.85rem;font-family:var(--font-b);transition:.25s;min-width:200px;}
    .filter-input:focus{outline:none;border-color:var(--sky-600);}
    .filter-select{background:var(--card);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.5rem .9rem;font-size:.85rem;font-family:var(--font-b);}
    .btn-filter{background:var(--sky-700);color:#fff;border:none;padding:.5rem 1.2rem;border-radius:10px;font-size:.85rem;font-weight:600;cursor:pointer;}
    .btn-reset{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.5rem 1rem;border-radius:10px;font-size:.85rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;}
    .data-table{width:100%;border-collapse:collapse;}
    .data-table th{font-size:.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);padding:.8rem 1.1rem;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap;background:var(--bg);}
    .data-table td{font-size:.84rem;padding:.85rem 1.1rem;border-bottom:1px solid var(--border);vertical-align:middle;}
    .data-table tr:last-child td{border-bottom:none;}
    .data-table tbody tr:hover td{background:var(--bg);}
    .s-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));color:#fff;font-family:var(--font-d);font-size:.9rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;}
    .s-avatar img{width:100%;height:100%;object-fit:cover;}
    .s-name{font-weight:600;font-size:.85rem;margin-bottom:.1rem;}
    .s-reg{font-size:.72rem;color:var(--muted);}
    .action-btn{width:30px;height:30px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;text-decoration:none;transition:.2s;border:none;cursor:pointer;}
    .btn-view{background:rgba(59,130,246,.1);color:#3b82f6;}
    .btn-edit{background:rgba(245,166,35,.1);color:var(--accent2);}
    .btn-del{background:rgba(232,86,42,.1);color:var(--accent);}
    .action-btn:hover{transform:scale(1.1);}
    .result-info{font-size:.82rem;color:var(--muted);padding:.7rem 1.3rem;border-bottom:1px solid var(--border);background:var(--bg);}
    .pagination-wrap{padding:1rem 1.3rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;}
    .page-btn{width:34px;height:34px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:600;text-decoration:none;border:1.5px solid var(--border);color:var(--text);transition:.2s;}
    .page-btn:hover,.page-btn.active{background:var(--sky-700);color:#fff;border-color:var(--sky-700);}
    .empty-state{padding:4rem 2rem;text-align:center;}
    .empty-state i{font-size:3rem;color:var(--border);display:block;margin-bottom:1rem;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}
    @media(max-width:991px){.sidebar{left:calc(-1 * var(--sidebar-w));}.sidebar.open{left:0;}.sidebar-overlay.open{display:block;}.main-content{margin-left:0;}.menu-toggle{display:flex;}}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<?php
$open_complaints = getCount($conn,'complaints',"status='pending'");
$admin = $conn->query("SELECT * FROM admins WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
?>

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
        <a href="students.php" class="nav-link-item active"><i class="ni fa-solid fa-users"></i> All Students <span class="nav-badge"><?= getCount($conn,'students') ?></span></a>
        <a href="add-student.php" class="nav-link-item"><i class="ni fa-solid fa-user-plus"></i> Add Student</a>
        <div class="nav-sec">Rooms</div>
        <a href="rooms.php" class="nav-link-item"><i class="ni fa-solid fa-door-open"></i> Manage Rooms</a>
        <a href="allocate-room.php" class="nav-link-item"><i class="ni fa-solid fa-key"></i> Allocate Room</a>
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
            <div class="page-title">Students Management</div>
        </div>
        <div class="topbar-right">
            <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
            <a href="add-student.php" class="btn-add"><i class="fa-solid fa-user-plus"></i> Add Student</a>
        </div>
    </div>

    <div class="page-content">

        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <?php
            $sts = [
                ['label'=>'Total Students',    'val'=>getCount($conn,'students'),                        'icon'=>'fa-users',        'color'=>'#3b82f6'],
                ['label'=>'Active',            'val'=>getCount($conn,'students',"status='active'"),       'icon'=>'fa-circle-check', 'color'=>'#34d399'],
                ['label'=>'Inactive',          'val'=>getCount($conn,'students',"status='inactive'"),     'icon'=>'fa-circle-xmark', 'color'=>'#718096'],
                ['label'=>'Suspended',         'val'=>getCount($conn,'students',"status='suspended'"),    'icon'=>'fa-ban',          'color'=>'#e8562a'],
            ];
            foreach($sts as $st): ?>
            <div class="col-6 col-lg-3">
                <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.2rem;display:flex;align-items:center;gap:.9rem;box-shadow:var(--shadow);">
                    <div style="width:44px;height:44px;border-radius:11px;background:<?= $st['color'] ?>18;color:<?= $st['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;"><i class="fa-solid <?= $st['icon'] ?>"></i></div>
                    <div>
                        <div style="font-family:var(--font-d);font-size:1.4rem;font-weight:800;line-height:1;"><?= $st['val'] ?></div>
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.15rem;"><?= $st['label'] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="sec-card">
            <div class="sec-head">
                <div class="sec-title"><i class="fa-solid fa-users" style="color:var(--sky-600);"></i> All Students</div>
                <a href="add-student.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New</a>
            </div>

            <!-- Filter Bar -->
            <form method="GET" class="filter-bar">
                <input type="text" name="search" class="filter-input" placeholder="🔍  Search name, email, reg no..." value="<?= htmlspecialchars($search) ?>">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active"    <?= $status==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive"  <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
                    <option value="suspended" <?= $status==='suspended'?'selected':'' ?>>Suspended</option>
                </select>
                <select name="gender" class="filter-select">
                    <option value="">All Gender</option>
                    <option value="Male"   <?= $gender==='Male'?'selected':'' ?>>Male</option>
                    <option value="Female" <?= $gender==='Female'?'selected':'' ?>>Female</option>
                </select>
                <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="students.php" class="btn-reset"><i class="fa-solid fa-rotate"></i> Reset</a>
            </form>

            <div class="result-info">
                Showing <strong><?= min($offset+1,$total) ?>–<?= min($offset+$per_page,$total) ?></strong> of <strong><?= $total ?></strong> students
                <?php if($search||$status||$gender): ?>
                — filtered results
                <?php endif; ?>
            </div>

            <?php if($students && $students->num_rows > 0): ?>
            <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Course</th>
                        <th>Gender</th>
                        <th>Room</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = $offset + 1;
                while ($s = $students->fetch_assoc()):
                    // Get room
                    $room = $conn->query("SELECT r.room_number FROM room_allocations ra JOIN rooms r ON ra.room_id=r.id WHERE ra.student_id={$s['id']} AND ra.status='active' LIMIT 1")->fetch_assoc();
                ?>
                <tr>
                    <td style="color:var(--muted);font-size:.78rem;"><?= $i++ ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.7rem;">
                            <div class="s-avatar">
                                <?php if($s['profile_pic'] && $s['profile_pic']!=='default-student.png' && file_exists(UPLOAD_PATH.$s['profile_pic'])): ?>
                                <img src="<?= UPLOAD_URL.htmlspecialchars($s['profile_pic']) ?>" alt="">
                                <?php else: ?>
                                <?= strtoupper(substr($s['name'],0,1)) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="s-name"><?= htmlspecialchars($s['name']) ?></div>
                                <div class="s-reg"><?= htmlspecialchars($s['reg_no']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.82rem;"><?= htmlspecialchars($s['email']) ?></div>
                        <div style="font-size:.75rem;color:var(--muted);"><?= htmlspecialchars($s['phone'] ?: 'N/A') ?></div>
                    </td>
                    <td style="font-size:.82rem;"><?= htmlspecialchars($s['course'] ?: '—') ?><br><span style="font-size:.72rem;color:var(--muted);"><?= htmlspecialchars($s['year'] ?: '') ?></span></td>
                    <td style="font-size:.82rem;"><?= htmlspecialchars($s['gender']) ?></td>
                    <td>
                        <?php if($room): ?>
                        <span style="background:rgba(26,58,110,.1);color:var(--sky-600);padding:.2rem .65rem;border-radius:100px;font-size:.75rem;font-weight:600;">Room <?= htmlspecialchars($room['room_number']) ?></span>
                        <?php else: ?>
                        <span style="color:var(--muted);font-size:.78rem;">Not assigned</span>
                        <?php endif; ?>
                    </td>
                    <td><?= statusBadge($s['status']) ?></td>
                    <td style="font-size:.78rem;color:var(--muted);"><?= formatDate($s['created_at']) ?></td>
                    <td>
                        <div style="display:flex;gap:.3rem;">
                            <a href="view-student.php?id=<?= $s['id'] ?>" class="action-btn btn-view" title="View"><i class="fa-solid fa-eye"></i></a>
                            <a href="edit-student.php?id=<?= $s['id'] ?>" class="action-btn btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <button onclick="confirmDelete(<?= $s['id'] ?>, '<?= htmlspecialchars($s['name'],ENT_QUOTES) ?>')" class="action-btn btn-del" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>

            <!-- Pagination -->
            <?php if($pages > 1): ?>
            <div class="pagination-wrap">
                <div style="font-size:.82rem;color:var(--muted);">Page <?= $page ?> of <?= $pages ?></div>
                <div style="display:flex;gap:.3rem;flex-wrap:wrap;">
                    <?php
                    $params = http_build_query(['search'=>$search,'status'=>$status,'gender'=>$gender]);
                    for($p=1;$p<=$pages;$p++): ?>
                    <a href="?<?= $params ?>&page=<?= $p ?>" class="page-btn <?= $p==$page?'active':'' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-users"></i>
                <div style="font-family:var(--font-d);font-weight:700;margin-bottom:.4rem;">No Students Found</div>
                <div style="font-size:.88rem;color:var(--muted);margin-bottom:1.2rem;">
                    <?= ($search||$status||$gender) ? 'No results match your filters.' : 'No students registered yet.' ?>
                </div>
                <a href="add-student.php" class="btn-add">
                    <i class="fa-solid fa-user-plus"></i> Add First Student
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;" id="deleteModal">
    <div style="background:var(--card);border-radius:var(--radius);padding:2rem;max-width:400px;width:90%;box-shadow:0 30px 80px rgba(0,0,0,.3);">
        <div style="width:56px;height:56px;border-radius:50%;background:rgba(232,86,42,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="fa-solid fa-trash" style="color:var(--accent);font-size:1.3rem;"></i></div>
        <h5 style="font-family:var(--font-d);text-align:center;margin-bottom:.5rem;">Delete Student?</h5>
        <p style="text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:1.5rem;" id="deleteMsg">This action cannot be undone.</p>
        <div style="display:flex;gap:.7rem;">
            <button onclick="closeDelete()" style="flex:1;padding:.7rem;border-radius:100px;border:1.5px solid var(--border);background:var(--bg);color:var(--text);font-weight:600;cursor:pointer;">Cancel</button>
            <a id="deleteConfirmBtn" href="#" style="flex:1;padding:.7rem;border-radius:100px;background:var(--accent);color:#fff;font-weight:700;text-align:center;text-decoration:none;">Delete</a>
        </div>
    </div>
</div>

<!-- Toast -->
<?php if(isset($_SESSION['toast'])): ?>
<div style="position:fixed;bottom:24px;right:24px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:.9rem 1.3rem;box-shadow:0 20px 60px rgba(0,0,0,.15);font-size:.88rem;z-index:9999;display:flex;align-items:center;gap:.7rem;min-width:280px;border-left:4px solid #34d399;" id="toastMsg">
    ✅ <?= htmlspecialchars($_SESSION['toast']['message']) ?>
    <button onclick="document.getElementById('toastMsg').remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:var(--muted);">&times;</button>
</div>
<?php unset($_SESSION['toast']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('open');
}
function confirmDelete(id, name) {
    document.getElementById('deleteMsg').textContent = 'Are you sure you want to delete "' + name + '"? This cannot be undone.';
    document.getElementById('deleteConfirmBtn').href = 'students.php?delete=' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDelete() { document.getElementById('deleteModal').style.display = 'none'; }
setTimeout(() => { const t = document.getElementById('toastMsg'); if(t) t.remove(); }, 4000);
</script>
</body>
</html>