<?php
require_once '../includes/db.php';
requireAdmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = sanitize($conn, $_POST['name'] ?? '');
    $email          = sanitize($conn, $_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
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

    if (empty($name))           $errors[] = 'Full name is required.';
    if (empty($email))          $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
    if (strlen($password) < 6)  $errors[] = 'Password must be at least 6 characters.';
    if (empty($gender))         $errors[] = 'Gender is required.';

    if (empty($errors)) {
        $check = $conn->query("SELECT id FROM students WHERE email='$email'");
        if ($check->num_rows > 0) $errors[] = 'Email already registered.';
    }

    if (empty($errors)) {
$maxRes = $conn->query("SELECT MAX(id) as max_id FROM students");
$maxRow = $maxRes->fetch_assoc();
$nextNum = ($maxRow['max_id'] ?? 0) + 1;
$reg_no = 'SKH-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        $hashed = hashPassword($password);

        // Profile pic upload
        $pic = 'default-student.png';
        if (!empty($_FILES['profile_pic']['name'])) {
            $up = uploadProfilePic($_FILES['profile_pic'], 'student');
            if ($up['success']) $pic = $up['filename'];
            else $errors[] = $up['message'];
        }

        if (empty($errors)) {
            $sql = "INSERT INTO students (reg_no,name,email,password,phone,gender,dob,cnic,address,guardian_name,guardian_phone,course,year,profile_pic,status)
                    VALUES ('$reg_no','$name','$email','$hashed','$phone','$gender','$dob','$cnic','$address','$guardian_name','$guardian_phone','$course','$year','$pic','$status')";
            if ($conn->query($sql)) {
                setToast('success', "Student $name added successfully! (Reg: $reg_no)");
                redirect(SITE_URL . '/admin/students.php');
            } else {
                $errors[] = 'Failed to add student. Please try again.';
            }
        }
    }
}
$open_complaints = getCount($conn,'complaints',"status='pending'");
$admin = $conn->query("SELECT * FROM admins WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student — Skyline Hostel Admin</title>
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
    .form-section-title{font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid var(--border);}
    label{font-size:.82rem;font-weight:600;margin-bottom:.35rem;display:block;}
    .form-control,.form-select{background:var(--bg);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.65rem 1rem;font-size:.88rem;width:100%;transition:.25s;font-family:var(--font-b);}
    .form-control:focus,.form-select:focus{outline:none;border-color:var(--sky-600);box-shadow:0 0 0 3px rgba(26,58,110,.1);background:var(--card);}
    textarea.form-control{resize:vertical;min-height:80px;}
    .error-box{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.5rem;}
    .error-box ul{margin:0;padding-left:1.2rem;}
    .error-box li{font-size:.85rem;color:var(--accent);margin-bottom:.2rem;}
    .btn-submit{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.75rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(232,86,42,.35);}
    .btn-cancel{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.75rem 1.5rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;transition:.25s;}
    .btn-cancel:hover{border-color:var(--muted);color:var(--text);}
    .pic-preview{width:90px;height:90px;border-radius:50%;background:var(--bg);border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:.25s;}
    .pic-preview:hover{border-color:var(--sky-600);}
    .pic-preview img{width:100%;height:100%;object-fit:cover;}
    .pic-preview i{font-size:1.8rem;color:var(--border);}
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
        <a href="add-student.php" class="nav-link-item active"><i class="ni fa-solid fa-user-plus"></i> Add Student</a>
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
            <div class="page-title">Add New Student</div>
        </div>
        <div style="display:flex;gap:.8rem;">
            <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
            <a href="students.php" class="icon-btn" title="Back"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="page-content">
        <?php if(!empty($errors)): ?>
        <div class="error-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">

                <!-- LEFT -->
                <div class="col-lg-8">
                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-user" style="color:var(--sky-600);"></i> Personal Information</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name']??'') ?>" required></div>
                                <div class="col-md-6"><label>Email *</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email']??'') ?>" required></div>
                                <div class="col-md-6"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone']??'') ?>"></div>
                                <div class="col-md-6"><label>Gender *</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Male" <?= ($_POST['gender']??'')==='Male'?'selected':'' ?>>Male</option>
                                        <option value="Female" <?= ($_POST['gender']??'')==='Female'?'selected':'' ?>>Female</option>
                                        <option value="Other" <?= ($_POST['gender']??'')==='Other'?'selected':'' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6"><label>Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($_POST['dob']??'') ?>"></div>
                                <div class="col-md-6"><label>CNIC / B-Form</label><input type="text" name="cnic" class="form-control" placeholder="42101-1234567-1" value="<?= htmlspecialchars($_POST['cnic']??'') ?>"></div>
                                <div class="col-12"><label>Home Address</label><textarea name="address" class="form-control"><?= htmlspecialchars($_POST['address']??'') ?></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-graduation-cap" style="color:var(--sky-600);"></i> Academic Information</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-md-8"><label>Course / Program</label><input type="text" name="course" class="form-control" placeholder="BS Computer Science" value="<?= htmlspecialchars($_POST['course']??'') ?>"></div>
                                <div class="col-md-4"><label>Year / Semester</label>
                                    <select name="year" class="form-select">
                                        <option value="">Select</option>
                                        <?php foreach(['1st Year','2nd Year','3rd Year','4th Year','1st Semester','2nd Semester','3rd Semester','4th Semester','5th Semester','6th Semester','7th Semester','8th Semester'] as $y): ?>
                                        <option value="<?= $y ?>" <?= ($_POST['year']??'')===$y?'selected':'' ?>><?= $y ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card-head"><i class="fa-solid fa-user-tie" style="color:var(--sky-600);"></i> Guardian Information</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label>Guardian Name</label><input type="text" name="guardian_name" class="form-control" value="<?= htmlspecialchars($_POST['guardian_name']??'') ?>"></div>
                                <div class="col-md-6"><label>Guardian Phone</label><input type="text" name="guardian_phone" class="form-control" value="<?= htmlspecialchars($_POST['guardian_phone']??'') ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="col-lg-4">
                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-image" style="color:var(--sky-600);"></i> Profile Picture</div>
                        <div class="form-card-body" style="text-align:center;">
                            <div class="pic-preview" id="picPreview" onclick="document.getElementById('picInput').click()">
                                <img id="picImg" src="" alt="" style="display:none;">
                                <i class="fa-solid fa-camera" id="picIcon"></i>
                            </div>
                            <div style="font-size:.78rem;color:var(--muted);margin-top:.7rem;">Click to upload photo<br>JPG, PNG — Max 2MB</div>
                            <input type="file" name="profile_pic" id="picInput" accept="image/*" style="display:none;" onchange="previewPic(this)">
                        </div>
                    </div>

                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-lock" style="color:var(--sky-600);"></i> Account Setup</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-12"><label>Password *</label><input type="text" name="password" class="form-control" placeholder="Min 6 characters" value="<?= htmlspecialchars($_POST['password']??'') ?>" required></div>
                                <div class="col-12"><label>Account Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active"   <?= ($_POST['status']??'active')==='active'?'selected':'' ?>>Active</option>
                                        <option value="inactive" <?= ($_POST['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:.7rem;">
                        <button type="submit" class="btn-submit" style="justify-content:center;"><i class="fa-solid fa-user-plus"></i> Add Student</button>
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
            document.getElementById('picImg').src = e.target.result;
            document.getElementById('picImg').style.display = 'block';
            document.getElementById('picIcon').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>