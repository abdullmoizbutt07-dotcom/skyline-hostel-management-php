<?php
require_once '../includes/db.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL . '/admin/students.php');

$student = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
if (!$student) { setToast('error','Student not found.'); redirect(SITE_URL.'/admin/students.php'); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = sanitize($conn, $_POST['name'] ?? '');
    $email          = sanitize($conn, $_POST['email'] ?? '');
    $phone          = sanitize($conn, $_POST['phone'] ?? '');
    $gender         = sanitize($conn, $_POST['gender'] ?? '');
    $dob            = sanitize($conn, $_POST['dob'] ?? '');
    $cnic           = sanitize($conn, $_POST['cnic'] ?? '');
    $address        = sanitize($conn, $_POST['address'] ?? '');
    $guardian_name  = sanitize($conn, $_POST['guardian_name'] ?? '');
    $guardian_phone = sanitize($conn, $_POST['guardian_phone'] ?? '');
    $course         = sanitize($conn, $_POST['course'] ?? '');
    $year           = sanitize($conn, $_POST['year'] ?? '');
    $status         = sanitize($conn, $_POST['status'] ?? 'active');

    if (empty($name))  $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';

    // Check email duplicate (exclude current)
    $check = $conn->query("SELECT id FROM students WHERE email='$email' AND id!=$id");
    if ($check->num_rows > 0) $errors[] = 'Email already used by another student.';

    if (empty($errors)) {
        $pic = $student['profile_pic'];
        if (!empty($_FILES['profile_pic']['name'])) {
            $up = uploadProfilePic($_FILES['profile_pic'], 'student');
            if ($up['success']) {
                // Delete old pic
                if ($pic && $pic !== 'default-student.png' && file_exists(UPLOAD_PATH.$pic)) unlink(UPLOAD_PATH.$pic);
                $pic = $up['filename'];
            } else {
                $errors[] = $up['message'];
            }
        }
    }

    if (empty($errors)) {
        $passUpdate = '';
        if (!empty($_POST['password']) && strlen($_POST['password']) >= 6) {
            $hashed = hashPassword($_POST['password']);
            $passUpdate = ", password='$hashed'";
        }

        $sql = "UPDATE students SET name='$name',email='$email',phone='$phone',gender='$gender',dob='$dob',cnic='$cnic',address='$address',guardian_name='$guardian_name',guardian_phone='$guardian_phone',course='$course',year='$year',profile_pic='$pic',status='$status' $passUpdate WHERE id=$id";

        if ($conn->query($sql)) {
            setToast('success', 'Student updated successfully!');
            redirect(SITE_URL . '/admin/students.php');
        } else {
            $errors[] = 'Update failed. Please try again.';
        }
    }

    // Reload student data after failed validation
    $student = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
}

$open_complaints = getCount($conn,'complaints',"status='pending'");
$admin = $conn->query("SELECT * FROM admins WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student — Skyline Hostel Admin</title>
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
    textarea.form-control{resize:vertical;min-height:80px;}
    .error-box{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.5rem;}
    .error-box li{font-size:.85rem;color:var(--accent);}
    .btn-submit{background:linear-gradient(135deg,var(--sky-700),var(--sky-600));color:#fff;border:none;padding:.75rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(10,22,40,.2);}
    .btn-cancel{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.75rem 1.5rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;transition:.25s;}
    .pic-wrap{display:flex;align-items:center;gap:1.2rem;margin-bottom:.8rem;}
    .pic-circle{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));overflow:hidden;display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--font-d);font-size:1.5rem;font-weight:700;flex-shrink:0;}
    .pic-circle img{width:100%;height:100%;object-fit:cover;}
    .reg-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(26,58,110,.08);border:1px solid rgba(26,58,110,.15);color:var(--sky-600);padding:.3rem .9rem;border-radius:100px;font-size:.78rem;font-weight:700;}
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
        <a href="students.php" class="nav-link-item active"><i class="ni fa-solid fa-users"></i> All Students</a>
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
            <div class="page-title">Edit Student</div>
        </div>
        <div style="display:flex;gap:.8rem;">
            <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
            <a href="students.php" class="icon-btn" title="Back"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="page-content">

        <!-- Student Info Banner -->
        <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.2rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;box-shadow:var(--shadow);">
            <div class="pic-circle">
                <?php if($student['profile_pic'] && $student['profile_pic']!=='default-student.png' && file_exists(UPLOAD_PATH.$student['profile_pic'])): ?>
                <img src="<?= UPLOAD_URL.htmlspecialchars($student['profile_pic']) ?>" alt="">
                <?php else: ?><?= strtoupper(substr($student['name'],0,1)) ?><?php endif; ?>
            </div>
            <div>
                <div style="font-family:var(--font-d);font-size:1.1rem;font-weight:800;"><?= htmlspecialchars($student['name']) ?></div>
                <div style="margin-top:.3rem;"><span class="reg-badge"><i class="fa-solid fa-id-badge"></i><?= htmlspecialchars($student['reg_no']) ?></span></div>
            </div>
        </div>

        <?php if(!empty($errors)): ?>
        <div class="error-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-user" style="color:var(--sky-600);"></i> Personal Information</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required></div>
                                <div class="col-md-6"><label>Email *</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required></div>
                                <div class="col-md-6"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']??'') ?>"></div>
                                <div class="col-md-6"><label>Gender *</label>
                                    <select name="gender" class="form-select">
                                        <option value="Male"   <?= $student['gender']==='Male'?'selected':'' ?>>Male</option>
                                        <option value="Female" <?= $student['gender']==='Female'?'selected':'' ?>>Female</option>
                                        <option value="Other"  <?= $student['gender']==='Other'?'selected':'' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6"><label>Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($student['dob']??'') ?>"></div>
                                <div class="col-md-6"><label>CNIC</label><input type="text" name="cnic" class="form-control" value="<?= htmlspecialchars($student['cnic']??'') ?>"></div>
                                <div class="col-12"><label>Address</label><textarea name="address" class="form-control"><?= htmlspecialchars($student['address']??'') ?></textarea></div>
                                <div class="col-md-6"><label>Guardian Name</label><input type="text" name="guardian_name" class="form-control" value="<?= htmlspecialchars($student['guardian_name']??'') ?>"></div>
                                <div class="col-md-6"><label>Guardian Phone</label><input type="text" name="guardian_phone" class="form-control" value="<?= htmlspecialchars($student['guardian_phone']??'') ?>"></div>
                                <div class="col-md-8"><label>Course</label><input type="text" name="course" class="form-control" value="<?= htmlspecialchars($student['course']??'') ?>"></div>
                                <div class="col-md-4"><label>Year</label>
                                    <select name="year" class="form-select">
                                        <option value="">Select</option>
                                        <?php foreach(['1st Year','2nd Year','3rd Year','4th Year','1st Semester','2nd Semester','3rd Semester','4th Semester','5th Semester','6th Semester','7th Semester','8th Semester'] as $y): ?>
                                        <option value="<?= $y ?>" <?= $student['year']===$y?'selected':'' ?>><?= $y ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-image" style="color:var(--sky-600);"></i> Profile Picture</div>
                        <div class="form-card-body" style="text-align:center;">
                            <div style="width:90px;height:90px;border-radius:50%;margin:0 auto .8rem;overflow:hidden;border:3px solid var(--border);cursor:pointer;" onclick="document.getElementById('picInput').click()" id="picPreview">
                                <?php if($student['profile_pic'] && $student['profile_pic']!=='default-student.png' && file_exists(UPLOAD_PATH.$student['profile_pic'])): ?>
                                <img id="picImg" src="<?= UPLOAD_URL.htmlspecialchars($student['profile_pic']) ?>" style="width:100%;height:100%;object-fit:cover;" alt="">
                                <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--font-d);font-size:2rem;font-weight:700;"><?= strtoupper(substr($student['name'],0,1)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div style="font-size:.78rem;color:var(--muted);">Click to change photo</div>
                            <input type="file" name="profile_pic" id="picInput" accept="image/*" style="display:none;" onchange="previewPic(this)">
                        </div>
                    </div>

                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-lock" style="color:var(--sky-600);"></i> Account</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label>New Password <span style="color:var(--muted);font-weight:400;">(leave blank to keep)</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                                </div>
                                <div class="col-12"><label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active"    <?= $student['status']==='active'?'selected':'' ?>>Active</option>
                                        <option value="inactive"  <?= $student['status']==='inactive'?'selected':'' ?>>Inactive</option>
                                        <option value="suspended" <?= $student['status']==='suspended'?'selected':'' ?>>Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:.7rem;">
                        <button type="submit" class="btn-submit" style="justify-content:center;"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                        <a href="students.php" class="btn-cancel" style="justify-content:center;"><i class="fa-solid fa-xmark"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

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
function previewPic(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('picPreview');
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;" id="picImg">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>