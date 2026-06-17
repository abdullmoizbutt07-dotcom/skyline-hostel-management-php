<?php
require_once '../includes/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = sanitize($conn, $_POST['name'] ?? '');
    $email          = sanitize($conn, $_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
    $confirm        = $_POST['confirm_password'] ?? '';
    $phone          = sanitize($conn, $_POST['phone'] ?? '');
    $gender         = sanitize($conn, $_POST['gender'] ?? '');
    $dob            = sanitize($conn, $_POST['dob'] ?? '');
    $cnic           = sanitize($conn, $_POST['cnic'] ?? '');
    $address        = sanitize($conn, $_POST['address'] ?? '');
    $guardian_name  = sanitize($conn, $_POST['guardian_name'] ?? '');
    $guardian_phone = sanitize($conn, $_POST['guardian_phone'] ?? '');
    $course         = sanitize($conn, $_POST['course'] ?? '');
    $year           = sanitize($conn, $_POST['year'] ?? '');

    // Validation
    if (empty($name))           $errors[] = 'Full name is required.';
    if (empty($email))          $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (strlen($password) < 6)  $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)  $errors[] = 'Passwords do not match.';
    if (empty($phone))          $errors[] = 'Phone number is required.';
    if (empty($gender))         $errors[] = 'Gender is required.';
    if (empty($course))         $errors[] = 'Course is required.';

    // Check email duplicate
    if (empty($errors)) {
        $check = $conn->query("SELECT id FROM students WHERE email='$email'");
        if ($check->num_rows > 0) $errors[] = 'This email is already registered.';
    }

    if (empty($errors)) {
        // Generate reg_no
        $year_short = date('Y');
$maxRes = $conn->query("SELECT MAX(id) as max_id FROM students");
$maxRow = $maxRes->fetch_assoc();
$nextNum = ($maxRow['max_id'] ?? 0) + 1;
$reg_no = 'SKH-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        $hashed = hashPassword($password);

        $sql = "INSERT INTO students 
                (reg_no, name, email, password, phone, gender, dob, cnic, address, guardian_name, guardian_phone, course, year)
                VALUES 
                ('$reg_no','$name','$email','$hashed','$phone','$gender','$dob','$cnic','$address','$guardian_name','$guardian_phone','$course','$year')";

        if ($conn->query($sql)) {
            setToast('success', 'Registration successful! You can now login.');
            redirect(SITE_URL . '/student/login.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration — Skyline Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --sky-900:#0a1628; --sky-700:#112952; --sky-600:#1a3a6e;
            --accent:#e8562a; --accent2:#f5a623;
            --bg:#f8f9fc; --card:#fff; --border:#e2e8f0;
            --text:#0a1628; --muted:#718096;
            --font-d:'Syne',sans-serif; --font-b:'DM Sans',sans-serif;
            --radius:16px; --shadow:0 20px 60px rgba(10,22,40,.12);
        }
        [data-theme="dark"] {
            --bg:#07101f; --card:#0d1f3c; --border:#1a2d4f;
            --text:#f0f4ff; --muted:#718096;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:var(--font-b); background:var(--bg); color:var(--text); min-height:100vh; transition:.3s; }

        /* LEFT PANEL */
        .left-panel {
            background: linear-gradient(160deg, var(--sky-900), var(--sky-700), #1e3a6e);
            min-height: 100vh;
            padding: 3rem 2.5rem;
            display: flex; flex-direction: column; justify-content: space-between;
            position: sticky; top: 0;
            overflow: hidden;
        }
        .left-panel::before {
            content:'';
            position:absolute; inset:0;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),
                              linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
            background-size:40px 40px;
        }
        .left-blob {
            position:absolute; border-radius:50%; filter:blur(70px); opacity:.3; pointer-events:none;
        }
        .blob1 { width:350px;height:350px;background:radial-gradient(circle,var(--accent),transparent);top:-100px;right:-100px; }
        .blob2 { width:250px;height:250px;background:radial-gradient(circle,#1a5cb5,transparent);bottom:-50px;left:-50px; }

        .brand { font-family:var(--font-d); font-size:1.5rem; font-weight:800; color:#fff; position:relative; z-index:1; }
        .brand span { color:var(--accent2); }

        .left-content { position:relative; z-index:1; }
        .left-title { font-family:var(--font-d); font-size:2.2rem; font-weight:800; color:#fff; line-height:1.2; margin-bottom:1rem; }
        .left-title span { color:var(--accent2); }
        .left-desc { color:rgba(255,255,255,.65); font-size:.95rem; line-height:1.8; margin-bottom:2rem; font-weight:300; }

        .perks { display:flex; flex-direction:column; gap:.9rem; }
        .perk-item { display:flex; align-items:center; gap:.85rem; }
        .perk-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;color:var(--accent2);font-size:.9rem;flex-shrink:0; }
        .perk-text { color:rgba(255,255,255,.8); font-size:.88rem; }

        .left-footer { position:relative; z-index:1; }
        .already-link { color:rgba(255,255,255,.6); font-size:.88rem; }
        .already-link a { color:var(--accent2); font-weight:600; }

        /* RIGHT PANEL */
        .right-panel { padding: 3rem 2.5rem; background:var(--bg); min-height:100vh; overflow-y:auto; }

        .form-title { font-family:var(--font-d); font-size:1.8rem; font-weight:800; margin-bottom:.3rem; }
        .form-subtitle { color:var(--muted); font-size:.9rem; margin-bottom:2rem; }

        .step-indicator { display:flex; gap:.5rem; margin-bottom:2rem; }
        .step-dot { flex:1; height:4px; border-radius:4px; background:var(--border); transition:.4s; }
        .step-dot.active { background:var(--accent); }
        .step-dot.done { background:var(--sky-600); }

        .step-label { font-family:var(--font-d); font-size:.75rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:1.2rem; }

        .form-step { display:none; }
        .form-step.active { display:block; animation:fade-in .35s ease; }
        @keyframes fade-in { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

        .form-group { margin-bottom:1.2rem; }
        label { font-size:.82rem; font-weight:600; color:var(--text); margin-bottom:.4rem; display:block; }
        .form-control, .form-select {
            background:var(--bg); border:1.5px solid var(--border); color:var(--text);
            border-radius:10px; padding:.7rem 1rem; font-size:.9rem; width:100%;
            transition:.25s; font-family:var(--font-b);
        }
        .form-control:focus, .form-select:focus {
            outline:none; border-color:var(--sky-600);
            box-shadow:0 0 0 3px rgba(26,58,110,.12);
            background:var(--card);
        }
        .input-icon-wrap { position:relative; }
        .input-icon-wrap .form-control { padding-left:2.6rem; }
        .input-icon { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.85rem; pointer-events:none; }

        .password-toggle { position:absolute; right:.9rem; top:50%; transform:translateY(-50%); color:var(--muted); cursor:pointer; font-size:.85rem; }

        .btn-next, .btn-prev, .btn-submit {
            padding:.8rem 2rem; border-radius:100px; font-family:var(--font-d);
            font-size:.95rem; font-weight:700; border:none; cursor:pointer; transition:.3s;
            display:inline-flex; align-items:center; gap:.5rem;
        }
        .btn-next, .btn-submit { background:linear-gradient(135deg,var(--accent),#c0410f); color:#fff; box-shadow:0 6px 20px rgba(232,86,42,.3); }
        .btn-next:hover, .btn-submit:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(232,86,42,.4); }
        .btn-prev { background:var(--bg); border:1.5px solid var(--border); color:var(--text); }
        .btn-prev:hover { border-color:var(--sky-600); color:var(--sky-600); }

        .error-box { background:rgba(232,86,42,.08); border:1px solid rgba(232,86,42,.25); border-radius:10px; padding:1rem 1.2rem; margin-bottom:1.5rem; }
        .error-box ul { margin:0; padding-left:1.2rem; }
        .error-box li { font-size:.85rem; color:var(--accent); margin-bottom:.25rem; }

        .strength-bar { height:4px; border-radius:4px; background:var(--border); margin-top:.5rem; overflow:hidden; }
        .strength-fill { height:100%; border-radius:4px; width:0; transition:.4s; }

        .theme-btn { position:fixed; top:1.2rem; right:1.2rem; width:40px;height:40px;border-radius:50%;background:var(--card);border:1.5px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:999;transition:.25s; }
        .theme-btn:hover { border-color:var(--accent); color:var(--accent); }

        @media(max-width:991px) {
            .left-panel { min-height:auto; position:relative; padding:2rem 1.5rem; }
            .right-panel { padding:2rem 1.5rem; }
        }
    </style>
</head>
<body>

<button class="theme-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>

<div class="container-fluid p-0">
<div class="row g-0">

    <!-- LEFT PANEL -->
    <div class="col-lg-5">
        <div class="left-panel">
            <div class="left-blob blob1"></div>
            <div class="left-blob blob2"></div>

            <div class="brand"><span>Sky</span>line Hostel</div>

            <div class="left-content">
                <h2 class="left-title">Join Our<br>Community<br><span>Today.</span></h2>
                <p class="left-desc">Register now and take the first step towards a comfortable, secure, and inspiring student life at Skyline Hostel.</p>

                <div class="perks">
                    <div class="perk-item">
                        <div class="perk-icon"><i class="fa-solid fa-wifi"></i></div>
                        <div class="perk-text">High-speed WiFi in every room</div>
                    </div>
                    <div class="perk-item">
                        <div class="perk-icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <div class="perk-text">24/7 Security & CCTV coverage</div>
                    </div>
                    <div class="perk-item">
                        <div class="perk-icon"><i class="fa-solid fa-book-open"></i></div>
                        <div class="perk-text">Dedicated study rooms available</div>
                    </div>
                    <div class="perk-item">
                        <div class="perk-icon"><i class="fa-solid fa-bolt"></i></div>
                        <div class="perk-text">Uninterrupted power backup</div>
                    </div>
                </div>
            </div>

            <div class="left-footer">
                <p class="already-link">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="col-lg-7">
        <div class="right-panel">

            <div class="form-title">Create Account</div>
            <div class="form-subtitle">Fill in the details below — takes less than 2 minutes.</div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-dot active" id="dot1"></div>
                <div class="step-dot" id="dot2"></div>
                <div class="step-dot" id="dot3"></div>
            </div>

            <!-- Errors -->
            <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" id="regForm">

                <!-- STEP 1: Account Info -->
                <div class="form-step active" id="step1">
                    <div class="step-label"><i class="fa-solid fa-user"></i> Step 1 of 3 — Account Info</div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <div class="input-icon-wrap">
                                    <i class="input-icon fa-solid fa-user"></i>
                                    <input type="text" name="name" class="form-control" placeholder="Muhammad Ahmed" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Address *</label>
                                <div class="input-icon-wrap">
                                    <i class="input-icon fa-solid fa-envelope"></i>
                                    <input type="email" name="email" class="form-control" placeholder="ahmed@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <div class="input-icon-wrap">
                                    <i class="input-icon fa-solid fa-phone"></i>
                                    <input type="text" name="phone" class="form-control" placeholder="03001234567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password *</label>
                                <div class="input-icon-wrap" style="position:relative">
                                    <i class="input-icon fa-solid fa-lock"></i>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Min. 6 characters" required>
                                    <span class="password-toggle" onclick="togglePass('password','eyePass')"><i class="fa-solid fa-eye" id="eyePass"></i></span>
                                </div>
                                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                                <div id="strengthText" style="font-size:.75rem;color:var(--muted);margin-top:.3rem;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password *</label>
                                <div class="input-icon-wrap" style="position:relative">
                                    <i class="input-icon fa-solid fa-lock"></i>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter password" required>
                                    <span class="password-toggle" onclick="togglePass('confirm_password','eyeConfirm')"><i class="fa-solid fa-eye" id="eyeConfirm"></i></span>
                                </div>
                                <div id="matchMsg" style="font-size:.75rem;margin-top:.3rem;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="button" class="btn-next" onclick="goStep(2)">
                            Next <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Personal Info -->
                <div class="form-step" id="step2">
                    <div class="step-label"><i class="fa-solid fa-id-card"></i> Step 2 of 3 — Personal Info</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender *</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male"   <?= (($_POST['gender']??'')==='Male')?'selected':'' ?>>Male</option>
                                    <option value="Female" <?= (($_POST['gender']??'')==='Female')?'selected':'' ?>>Female</option>
                                    <option value="Other"  <?= (($_POST['gender']??'')==='Other')?'selected':'' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CNIC / B-Form</label>
                                <input type="text" name="cnic" class="form-control" placeholder="42101-1234567-1" value="<?= htmlspecialchars($_POST['cnic'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Home Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Street, City, Province"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Guardian Name</label>
                                <div class="input-icon-wrap">
                                    <i class="input-icon fa-solid fa-user-tie"></i>
                                    <input type="text" name="guardian_name" class="form-control" placeholder="Father/Guardian Name" value="<?= htmlspecialchars($_POST['guardian_name'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Guardian Phone</label>
                                <div class="input-icon-wrap">
                                    <i class="input-icon fa-solid fa-phone"></i>
                                    <input type="text" name="guardian_phone" class="form-control" placeholder="03001234567" value="<?= htmlspecialchars($_POST['guardian_phone'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <button type="button" class="btn-prev" onclick="goStep(1)">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn-next" onclick="goStep(3)">
                            Next <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Academic Info -->
                <div class="form-step" id="step3">
                    <div class="step-label"><i class="fa-solid fa-graduation-cap"></i> Step 3 of 3 — Academic Info</div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Course / Program *</label>
                                <div class="input-icon-wrap">
                                    <i class="input-icon fa-solid fa-graduation-cap"></i>
                                    <input type="text" name="course" class="form-control" placeholder="e.g. BS Computer Science" value="<?= htmlspecialchars($_POST['course'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Year / Semester *</label>
                                <select name="year" class="form-select" required>
                                    <option value="">Select Year</option>
                                    <?php foreach(['1st Year','2nd Year','3rd Year','4th Year','1st Semester','2nd Semester','3rd Semester','4th Semester','5th Semester','6th Semester','7th Semester','8th Semester'] as $y): ?>
                                    <option value="<?= $y ?>" <?= (($_POST['year']??'')===$y)?'selected':'' ?>><?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div style="background:rgba(26,58,110,.06);border:1px solid rgba(26,58,110,.15);border-radius:12px;padding:1.2rem;">
                                <div style="font-size:.82rem;font-weight:700;margin-bottom:.5rem;color:var(--sky-600);">
                                    <i class="fa-solid fa-circle-info"></i> What Happens Next?
                                </div>
                                <ul style="margin:0;padding-left:1.2rem;font-size:.83rem;color:var(--muted);line-height:1.9;">
                                    <li>Your registration number will be auto-generated</li>
                                    <li>Admin will review and allocate a room</li>
                                    <li>You'll receive confirmation on your email</li>
                                    <li>Login to your dashboard to apply for a specific room</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check" style="margin-top:.5rem;">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms" style="font-size:.85rem;color:var(--muted);">
                                    I agree to the <a href="../index.php" style="color:var(--accent);">Terms & Conditions</a> and Hostel Rules & Regulations.
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn-prev" onclick="goStep(2)">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fa-solid fa-rocket"></i> Create Account
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>
</div>

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

// Step navigation
let currentStep = 1;

function goStep(step) {
    if (step > currentStep && !validateStep(currentStep)) return;
    document.getElementById('step' + currentStep).classList.remove('active');
    document.getElementById('dot' + currentStep).classList.remove('active');
    document.getElementById('dot' + currentStep).classList.add('done');
    currentStep = step;
    document.getElementById('step' + currentStep).classList.add('active');
    document.getElementById('dot' + currentStep).classList.add('active');
    if (step < 3) { document.getElementById('dot' + currentStep).classList.remove('done'); }
}

function validateStep(step) {
    if (step === 1) {
        const name  = document.querySelector('[name=name]').value.trim();
        const email = document.querySelector('[name=email]').value.trim();
        const pass  = document.querySelector('[name=password]').value;
        const conf  = document.querySelector('[name=confirm_password]').value;
        if (!name)        { alert('Please enter your full name.'); return false; }
        if (!email)       { alert('Please enter your email.'); return false; }
        if (pass.length < 6) { alert('Password must be at least 6 characters.'); return false; }
        if (pass !== conf)   { alert('Passwords do not match.'); return false; }
    }
    if (step === 2) {
        const gender = document.querySelector('[name=gender]').value;
        if (!gender) { alert('Please select your gender.'); return false; }
    }
    return true;
}

// Password strength
document.getElementById('password').addEventListener('input', function() {
    const val = this.value;
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');
    let strength = 0;
    if (val.length >= 6)  strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const colors = ['','#e8562a','#f5a623','#3b82f6','#34d399','#10b981'];
    const labels = ['','Weak','Fair','Good','Strong','Very Strong'];
    fill.style.width = (strength * 20) + '%';
    fill.style.background = colors[strength];
    text.textContent = labels[strength];
    text.style.color = colors[strength];
});

// Password match
document.getElementById('confirm_password').addEventListener('input', function() {
    const pass = document.getElementById('password').value;
    const msg = document.getElementById('matchMsg');
    if (this.value === pass) {
        msg.textContent = '✓ Passwords match'; msg.style.color = '#34d399';
    } else {
        msg.textContent = '✗ Passwords do not match'; msg.style.color = '#e8562a';
    }
});

// Toggle password visibility
function togglePass(id, iconId) {
    const el = document.getElementById(id);
    const icon = document.getElementById(iconId);
    if (el.type === 'password') {
        el.type = 'text'; icon.className = 'fa-solid fa-eye-slash';
    } else {
        el.type = 'password'; icon.className = 'fa-solid fa-eye';
    }
}

// Submit loading state
document.getElementById('regForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating Account...';
    btn.disabled = true;
});

<?php if (!empty($errors)): ?>
// Jump to step 3 if errors on submission
document.getElementById('dot1').classList.add('done');
document.getElementById('dot2').classList.add('done');
document.getElementById('step1').classList.remove('active');
document.getElementById('step3').classList.add('active');
document.getElementById('dot3').classList.add('active');
currentStep = 3;
<?php endif; ?>
</script>
</body>
</html>