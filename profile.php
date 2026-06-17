<?php
require_once '../includes/db.php';
requireStudent();

$sid = $_SESSION['student_id'];
$errors = [];

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name           = sanitize($conn, $_POST['name'] ?? '');
    $phone          = sanitize($conn, $_POST['phone'] ?? '');
    $dob            = sanitize($conn, $_POST['dob'] ?? '');
    $cnic           = sanitize($conn, $_POST['cnic'] ?? '');
    $address        = sanitize($conn, $_POST['address'] ?? '');
    $guardian_name  = sanitize($conn, $_POST['guardian_name'] ?? '');
    $guardian_phone = sanitize($conn, $_POST['guardian_phone'] ?? '');
    $course         = sanitize($conn, $_POST['course'] ?? '');
    $year           = sanitize($conn, $_POST['year'] ?? '');

    if (empty($name)) $errors[] = 'Name is required.';

    $student_cur = $conn->query("SELECT profile_pic FROM students WHERE id=$sid")->fetch_assoc();
    $pic = $student_cur['profile_pic'];

    if (!empty($_FILES['profile_pic']['name'])) {
        $up = uploadProfilePic($_FILES['profile_pic'], 'student');
        if ($up['success']) {
            if ($pic && $pic !== 'default-student.png' && file_exists(UPLOAD_PATH.$pic)) unlink(UPLOAD_PATH.$pic);
            $pic = $up['filename'];
        } else {
            $errors[] = $up['message'];
        }
    }

    if (empty($errors)) {
        $conn->query("UPDATE students SET name='$name',phone='$phone',dob='$dob',cnic='$cnic',address='$address',guardian_name='$guardian_name',guardian_phone='$guardian_phone',course='$course',year='$year',profile_pic='$pic' WHERE id=$sid");
        $_SESSION['student_name'] = $name;
        $_SESSION['student_pic']  = $pic;
        setToast('success', 'Profile updated successfully!');
        redirect(SITE_URL . '/student/profile.php');
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $student = $conn->query("SELECT password FROM students WHERE id=$sid")->fetch_assoc();
    if (!verifyPassword($current, $student['password'])) {
        $errors[] = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        $hashed = hashPassword($new);
        $conn->query("UPDATE students SET password='$hashed' WHERE id=$sid");
        setToast('success', 'Password changed successfully!');
        redirect(SITE_URL . '/student/profile.php');
    }
}

$student = $conn->query("SELECT * FROM students WHERE id=$sid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — Skyline Hostel</title>
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
    .user-name{font-size:.85rem;font-weight:700;color:#fff;line-height:1.3;}
    .user-reg{font-size:.72rem;color:rgba(255,255,255,.5);}
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
    .menu-toggle{background:none;border:none;color:var(--text);font-size:1.1rem;cursor:pointer;padding:.3rem;display:none;}
    .page-title{font-family:var(--font-d);font-size:1.1rem;font-weight:700;}
    .icon-btn{width:36px;height:36px;border-radius:50%;background:var(--bg);border:1.5px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.85rem;transition:.25s;}
    .icon-btn:hover{border-color:var(--accent);color:var(--accent);}
    .page-content{padding:1.8rem;}
    .form-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .form-card-head{padding:1.1rem 1.4rem;border-bottom:1px solid var(--border);font-family:var(--font-d);font-size:.95rem;font-weight:700;display:flex;align-items:center;gap:.5rem;}
    .form-card-body{padding:1.4rem;}
    label{font-size:.82rem;font-weight:600;margin-bottom:.35rem;display:block;}
    .form-control,.form-select{background:var(--bg);border:1.5px solid var(--border);color:var(--text);border-radius:10px;padding:.65rem 1rem;font-size:.88rem;width:100%;transition:.25s;font-family:var(--font-b);}
    .form-control:focus,.form-select:focus{outline:none;border-color:var(--sky-600);box-shadow:0 0 0 3px rgba(26,58,110,.1);background:var(--card);}
    textarea.form-control{resize:vertical;min-height:75px;}
    .btn-save{background:linear-gradient(135deg,var(--sky-700),var(--sky-600));color:#fff;border:none;padding:.7rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.92rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;}
    .btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(10,22,40,.2);}
    .error-box{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);border-radius:10px;padding:.9rem 1rem;margin-bottom:1.2rem;}
    .error-box li{font-size:.85rem;color:var(--accent);}
    /* Profile pic */
    .pic-wrap{display:flex;align-items:center;gap:1.5rem;margin-bottom:1.5rem;}
    .pic-circle{width:90px;height:90px;border-radius:50%;overflow:hidden;border:3px solid var(--border);cursor:pointer;flex-shrink:0;background:linear-gradient(135deg,var(--sky-700),var(--sky-600));display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--font-d);font-size:2rem;font-weight:700;}
    .pic-circle img{width:100%;height:100%;object-fit:cover;}
    .pic-change-btn{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.45rem 1rem;border-radius:100px;font-size:.82rem;font-weight:600;cursor:pointer;transition:.25s;display:inline-flex;align-items:center;gap:.4rem;}
    .pic-change-btn:hover{border-color:var(--sky-600);color:var(--sky-600);}
    .profile-header{background:linear-gradient(135deg,var(--sky-900),var(--sky-700));border-radius:var(--radius);padding:1.8rem;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;}
    .ph-avatar{width:80px;height:80px;border-radius:50%;border:3px solid rgba(255,255,255,.2);overflow:hidden;background:linear-gradient(135deg,var(--accent),#c0410f);display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:1.8rem;font-weight:800;color:#fff;flex-shrink:0;}
    .ph-avatar img{width:100%;height:100%;object-fit:cover;}
    .ph-name{font-family:var(--font-d);font-size:1.3rem;font-weight:800;}
    .ph-reg{font-size:.82rem;color:rgba(255,255,255,.6);margin:.2rem 0;}
    .ph-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);padding:.25rem .8rem;border-radius:100px;font-size:.75rem;color:rgba(255,255,255,.8);}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}
    @media(max-width:991px){.sidebar{left:calc(-1 * var(--sidebar-w));}.sidebar.open{left:0;}.sidebar-overlay.open{display:block;}.main-content{margin-left:0;}.menu-toggle{display:flex;}}
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
        <a href="profile.php" class="nav-link-item active"><i class="ni fa-solid fa-user"></i> My Profile</a>
        <div class="nav-sec">Hostel</div>
        <a href="my-room.php" class="nav-link-item"><i class="ni fa-solid fa-door-open"></i> My Room</a>
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
            <div class="page-title">My Profile</div>
        </div>
        <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    </div>

    <div class="page-content">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="ph-avatar">
                <?php if($student['profile_pic'] && $student['profile_pic']!=='default-student.png' && file_exists(UPLOAD_PATH.$student['profile_pic'])): ?>
                <img src="<?= UPLOAD_URL.htmlspecialchars($student['profile_pic']) ?>" alt="">
                <?php else: ?><?= strtoupper(substr($student['name'],0,1)) ?><?php endif; ?>
            </div>
            <div>
                <div class="ph-name"><?= htmlspecialchars($student['name']) ?></div>
                <div class="ph-reg"><?= htmlspecialchars($student['email']) ?></div>
                <div style="display:flex;gap:.5rem;margin-top:.5rem;flex-wrap:wrap;">
                    <span class="ph-badge"><i class="fa-solid fa-id-badge"></i> <?= htmlspecialchars($student['reg_no']) ?></span>
                    <span class="ph-badge"><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($student['course'] ?: 'N/A') ?></span>
                </div>
            </div>
        </div>

        <?php if(!empty($errors)): ?>
        <div class="error-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="row g-3">
            <!-- Edit Profile Form -->
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="form-card-head"><i class="fa-solid fa-user-pen" style="color:var(--sky-600);"></i> Edit Profile</div>
                    <div class="form-card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">

                            <!-- Profile Pic -->
                            <div class="pic-wrap">
                                <div class="pic-circle" id="picCircle" onclick="document.getElementById('picInput').click()">
                                    <?php if($student['profile_pic'] && $student['profile_pic']!=='default-student.png' && file_exists(UPLOAD_PATH.$student['profile_pic'])): ?>
                                    <img src="<?= UPLOAD_URL.htmlspecialchars($student['profile_pic']) ?>" id="picImg" alt="">
                                    <?php else: ?>
                                    <span id="picLetter"><?= strtoupper(substr($student['name'],0,1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <button type="button" class="pic-change-btn" onclick="document.getElementById('picInput').click()"><i class="fa-solid fa-camera"></i> Change Photo</button>
                                    <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">JPG, PNG — Max 2MB</div>
                                </div>
                                <input type="file" name="profile_pic" id="picInput" accept="image/*" style="display:none;" onchange="previewPic(this)">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required></div>
                                <div class="col-md-6"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']??'') ?>"></div>
                                <div class="col-md-6"><label>Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($student['dob']??'') ?>"></div>
                                <div class="col-md-6"><label>CNIC / B-Form</label><input type="text" name="cnic" class="form-control" value="<?= htmlspecialchars($student['cnic']??'') ?>"></div>
                                <div class="col-12"><label>Home Address</label><textarea name="address" class="form-control"><?= htmlspecialchars($student['address']??'') ?></textarea></div>
                                <div class="col-md-6"><label>Guardian Name</label><input type="text" name="guardian_name" class="form-control" value="<?= htmlspecialchars($student['guardian_name']??'') ?>"></div>
                                <div class="col-md-6"><label>Guardian Phone</label><input type="text" name="guardian_phone" class="form-control" value="<?= htmlspecialchars($student['guardian_phone']??'') ?>"></div>
                                <div class="col-md-8"><label>Course / Program</label><input type="text" name="course" class="form-control" value="<?= htmlspecialchars($student['course']??'') ?>"></div>
                                <div class="col-md-4"><label>Year</label>
                                    <select name="year" class="form-select">
                                        <option value="">Select</option>
                                        <?php foreach(['1st Year','2nd Year','3rd Year','4th Year','1st Semester','2nd Semester','3rd Semester','4th Semester','5th Semester','6th Semester','7th Semester','8th Semester'] as $y): ?>
                                        <option value="<?= $y ?>" <?= $student['year']===$y?'selected':'' ?>><?= $y ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="col-lg-4">
                <!-- Account Info -->
                <div class="form-card mb-3">
                    <div class="form-card-head"><i class="fa-solid fa-circle-info" style="color:var(--sky-600);"></i> Account Info</div>
                    <div class="form-card-body">
                        <?php
                        $info = [
                            ['icon'=>'fa-id-badge','label'=>'Reg No','val'=>$student['reg_no']],
                            ['icon'=>'fa-envelope','label'=>'Email','val'=>$student['email']],
                            ['icon'=>'fa-venus-mars','label'=>'Gender','val'=>$student['gender']],
                            ['icon'=>'fa-calendar','label'=>'Joined','val'=>formatDate($student['created_at'])],
                        ];
                        foreach($info as $inf): ?>
                        <div style="display:flex;align-items:center;gap:.8rem;padding:.7rem 0;border-bottom:1px solid var(--border);">
                            <div style="width:30px;height:30px;border-radius:8px;background:rgba(26,58,110,.08);color:var(--sky-600);display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;"><i class="fa-solid <?= $inf['icon'] ?>"></i></div>
                            <div>
                                <div style="font-size:.7rem;color:var(--muted);"><?= $inf['label'] ?></div>
                                <div style="font-size:.85rem;font-weight:600;"><?= htmlspecialchars($inf['val']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="form-card">
                    <div class="form-card-head"><i class="fa-solid fa-lock" style="color:var(--sky-600);"></i> Change Password</div>
                    <div class="form-card-body">
                        <form method="POST">
                            <input type="hidden" name="change_password" value="1">
                            <div style="margin-bottom:.9rem;"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
                            <div style="margin-bottom:.9rem;"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
                            <div style="margin-bottom:1.2rem;"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
                            <button type="submit" class="btn-save" style="width:100%;justify-content:center;background:linear-gradient(135deg,var(--accent),#c0410f);"><i class="fa-solid fa-key"></i> Change Password</button>
                        </form>
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
document.getElementById('themeBtn').addEventListener('click',()=>{const cur=html.getAttribute('data-theme'),next=cur==='dark'?'light':'dark';html.setAttribute('data-theme',next);localStorage.setItem('skyline-theme',next);document.getElementById('themeIcon').className=next==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';});
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
function previewPic(input){
    if(input.files&&input.files[0]){
        const reader=new FileReader();
        reader.onload=e=>{
            const c=document.getElementById('picCircle');
            c.innerHTML='<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
setTimeout(()=>{const t=document.getElementById('toastMsg');if(t)t.remove();},4000);
</script>
</body>
</html>