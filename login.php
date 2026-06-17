<?php
require_once '../includes/db.php';

// Already logged in
if (isset($_SESSION['student_id'])) {
    redirect(SITE_URL . '/student/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $res = $conn->query("SELECT * FROM students WHERE email='$email' LIMIT 1");
        if ($res && $res->num_rows === 1) {
            $student = $res->fetch_assoc();
            if (verifyPassword($password, $student['password'])) {
                if ($student['status'] === 'suspended') {
                    $error = 'Your account has been suspended. Contact admin.';
                } elseif ($student['status'] === 'inactive') {
                    $error = 'Your account is inactive. Please contact the admin.';
                } else {
                    $_SESSION['student_id']   = $student['id'];
                    $_SESSION['student_name'] = $student['name'];
                    $_SESSION['student_email']= $student['email'];
                    $_SESSION['student_reg']  = $student['reg_no'];
                    $_SESSION['student_pic']  = $student['profile_pic'];
                    setToast('success', 'Welcome back, ' . $student['name'] . '!');
                    redirect(SITE_URL . '/student/dashboard.php');
                }
            } else {
                $error = 'Incorrect password. Please try again.';
            }
        } else {
            $error = 'No account found with this email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login — Skyline Hostel</title>
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
        }
        [data-theme="dark"] {
            --bg:#07101f; --card:#0d1f3c; --border:#1a2d4f;
            --text:#f0f4ff; --muted:#718096;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:var(--font-b); background:var(--bg); color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem; transition:.3s; position:relative; overflow:hidden; }

        /* Background */
        .bg-gradient {
            position:fixed; inset:0;
            background:linear-gradient(160deg, var(--sky-900) 0%, var(--sky-700) 40%, #1e3a6e 100%);
            z-index:-2;
        }
        .bg-grid {
            position:fixed; inset:0;
            background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),
                             linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
            background-size:40px 40px;
            z-index:-1;
        }
        .bg-blob {
            position:fixed; border-radius:50%; filter:blur(90px); opacity:.25; pointer-events:none; z-index:-1;
        }
        .blob1 { width:500px;height:500px;background:radial-gradient(circle,var(--accent),transparent);top:-150px;right:-150px; }
        .blob2 { width:400px;height:400px;background:radial-gradient(circle,#1a5cb5,transparent);bottom:-100px;left:-100px; }

        /* Card */
        .login-card {
            background:var(--card);
            border-radius:24px;
            padding:3rem;
            width:100%;
            max-width:460px;
            box-shadow:0 30px 80px rgba(0,0,0,.3);
            position:relative;
            animation:card-in .5s cubic-bezier(.175,.885,.32,1.275);
        }
        @keyframes card-in { from{opacity:0;transform:translateY(30px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }

        .card-top-bar {
            height:4px;
            background:linear-gradient(90deg,var(--accent),var(--accent2));
            border-radius:4px 4px 0 0;
            position:absolute; top:0; left:0; right:0;
        }

        .brand { font-family:var(--font-d); font-size:1.3rem; font-weight:800; text-align:center; margin-bottom:2rem; }
        .brand span { color:var(--accent); }

        .login-icon-wrap {
            width:70px; height:70px; border-radius:50%;
            background:linear-gradient(135deg,var(--sky-700),var(--sky-600));
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 1.5rem;
            box-shadow:0 8px 25px rgba(10,22,40,.2);
        }
        .login-icon-wrap i { font-size:1.6rem; color:#fff; }

        h2 { font-family:var(--font-d); font-size:1.6rem; font-weight:800; text-align:center; margin-bottom:.3rem; }
        .subtitle { text-align:center; color:var(--muted); font-size:.88rem; margin-bottom:2rem; }

        .form-group { margin-bottom:1.2rem; }
        label { font-size:.82rem; font-weight:600; margin-bottom:.4rem; display:block; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.85rem; pointer-events:none; }
        .form-control {
            background:var(--bg); border:1.5px solid var(--border); color:var(--text);
            border-radius:10px; padding:.75rem 1rem .75rem 2.6rem;
            font-size:.9rem; width:100%; transition:.25s; font-family:var(--font-b);
        }
        .form-control:focus {
            outline:none; border-color:var(--sky-600);
            box-shadow:0 0 0 3px rgba(26,58,110,.12);
            background:var(--card);
        }
        .toggle-pass { position:absolute; right:.9rem; top:50%; transform:translateY(-50%); color:var(--muted); cursor:pointer; font-size:.85rem; }

        .error-alert {
            background:rgba(232,86,42,.08); border:1px solid rgba(232,86,42,.25);
            border-radius:10px; padding:.85rem 1rem;
            display:flex; align-items:center; gap:.7rem;
            font-size:.85rem; color:var(--accent);
            margin-bottom:1.5rem;
            animation:shake .4s ease;
        }
        @keyframes shake {
            0%,100%{transform:translateX(0)}
            20%,60%{transform:translateX(-6px)}
            40%,80%{transform:translateX(6px)}
        }

        .remember-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .remember-row label { font-size:.82rem; color:var(--muted); cursor:pointer; margin:0; }
        .forgot-link { font-size:.82rem; color:var(--sky-600); font-weight:600; }
        .forgot-link:hover { color:var(--accent); }

        .btn-login {
            width:100%; padding:.85rem; border:none; border-radius:100px;
            background:linear-gradient(135deg,var(--accent),#c0410f);
            color:#fff; font-family:var(--font-d); font-size:1rem; font-weight:700;
            cursor:pointer; transition:.3s;
            box-shadow:0 6px 20px rgba(232,86,42,.3);
            display:flex; align-items:center; justify-content:center; gap:.5rem;
        }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(232,86,42,.4); }

        .divider { display:flex; align-items:center; gap:1rem; margin:1.5rem 0; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background:var(--border); }
        .divider span { font-size:.78rem; color:var(--muted); white-space:nowrap; }

        .register-link {
            display:block; text-align:center;
            background:var(--bg); border:1.5px solid var(--border);
            border-radius:100px; padding:.75rem;
            font-family:var(--font-d); font-size:.9rem; font-weight:700;
            color:var(--text); transition:.3s;
        }
        .register-link:hover { border-color:var(--sky-600); color:var(--sky-600); }

        .admin-link {
            display:block; text-align:center;
            margin-top:1.2rem; font-size:.8rem; color:var(--muted);
        }
        .admin-link a { color:var(--muted); font-weight:600; }
        .admin-link a:hover { color:var(--accent); }

        .back-home {
            position:fixed; top:1.2rem; left:1.2rem;
            display:flex; align-items:center; gap:.5rem;
            color:rgba(255,255,255,.7); font-size:.85rem; font-weight:600;
            transition:.25s;
        }
        .back-home:hover { color:#fff; }

        .theme-btn { position:fixed; top:1.2rem; right:1.2rem; width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.8);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.25s; }
        .theme-btn:hover { background:rgba(255,255,255,.2); }
    </style>
</head>
<body>

<div class="bg-gradient"></div>
<div class="bg-grid"></div>
<div class="bg-blob blob1"></div>
<div class="bg-blob blob2"></div>

<a href="../index.php" class="back-home">
    <i class="fa-solid fa-arrow-left"></i> Back to Home
</a>

<button class="theme-btn" id="themeBtn">
    <i class="fa-solid fa-moon" id="themeIcon"></i>
</button>

<div class="login-card">
    <div class="card-top-bar"></div>

    <div class="brand"><span>Sky</span>line Hostel</div>

    <div class="login-icon-wrap">
        <i class="fa-solid fa-user-graduate"></i>
    </div>

    <h2>Student Login</h2>
    <p class="subtitle">Access your hostel dashboard</p>

    <?php if ($error): ?>
    <div class="error-alert">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
        <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="input-icon fa-solid fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
                <i class="input-icon fa-solid fa-lock"></i>
                <input type="password" name="password" id="passInput" class="form-control" placeholder="Enter your password" required>
                <span class="toggle-pass" onclick="togglePass()">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <div class="remember-row">
            <label>
                <input type="checkbox" name="remember" style="margin-right:.4rem;">
                Remember me
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn-login" id="loginBtn">
            <i class="fa-solid fa-right-to-bracket"></i>
            Login to Dashboard
        </button>
    </form>

    <div class="divider"><span>Don't have an account?</span></div>

    <a href="register.php" class="register-link">
        <i class="fa-solid fa-user-plus"></i> Register as Student
    </a>

    <div class="admin-link">
        Are you an admin? <a href="../admin/login.php">Admin Login →</a>
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

// Toggle password
function togglePass() {
    const inp = document.getElementById('passInput');
    const ico = document.getElementById('eyeIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
}

// Loading state on submit
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Logging in...';
    btn.disabled = true;
});

<?php if (isset($_SESSION['toast'])): ?>
// Show toast
console.log('<?= $_SESSION['toast']['message'] ?>');
<?php unset($_SESSION['toast']); endif; ?>
</script>
</body>
</html>