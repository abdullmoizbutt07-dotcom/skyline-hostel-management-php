<?php
require_once '../includes/db.php';
requireAdmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = sanitize($conn, $_POST['room_number'] ?? '');
    $room_type   = sanitize($conn, $_POST['room_type'] ?? '');
    $floor       = sanitize($conn, $_POST['floor'] ?? '');
    $capacity    = (int)($_POST['capacity'] ?? 1);
    $monthly_fee = (float)($_POST['monthly_fee'] ?? 0);
    $amenities   = sanitize($conn, $_POST['amenities'] ?? '');
    $status      = sanitize($conn, $_POST['status'] ?? 'available');

    if (empty($room_number)) $errors[] = 'Room number is required.';
    if (empty($room_type))   $errors[] = 'Room type is required.';
    if ($capacity < 1)       $errors[] = 'Capacity must be at least 1.';
    if ($monthly_fee <= 0)   $errors[] = 'Monthly fee must be greater than 0.';

    if (empty($errors)) {
        $check = $conn->query("SELECT id FROM rooms WHERE room_number='$room_number'");
        if ($check->num_rows > 0) $errors[] = 'Room number already exists.';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO rooms (room_number,room_type,floor,capacity,monthly_fee,amenities,status)
                VALUES ('$room_number','$room_type','$floor',$capacity,$monthly_fee,'$amenities','$status')";
        if ($conn->query($sql)) {
            setToast('success', "Room $room_number added successfully!");
            redirect(SITE_URL . '/admin/rooms.php');
        } else {
            $errors[] = 'Failed to add room.';
        }
    }
}

$admin = $conn->query("SELECT * FROM admins WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
$open_complaints = getCount($conn,'complaints',"status='pending'");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room — Skyline Hostel Admin</title>
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
    .error-box{background:rgba(232,86,42,.08);border:1px solid rgba(232,86,42,.25);border-radius:10px;padding:1rem;margin-bottom:1.5rem;}
    .error-box li{font-size:.85rem;color:var(--accent);}
    .btn-submit{background:linear-gradient(135deg,var(--accent),#c0410f);color:#fff;border:none;padding:.75rem 2rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:700;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(232,86,42,.35);}
    .btn-cancel{background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:.75rem 1.5rem;border-radius:100px;font-family:var(--font-d);font-size:.95rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;transition:.25s;}
    /* Amenity checkboxes */
    .amenity-grid{display:flex;flex-wrap:wrap;gap:.5rem;}
    .amenity-check{display:none;}
    .amenity-label{background:var(--bg);border:1.5px solid var(--border);color:var(--muted);padding:.35rem .8rem;border-radius:100px;font-size:.8rem;font-weight:500;cursor:pointer;transition:.25s;user-select:none;}
    .amenity-check:checked+.amenity-label{background:rgba(26,58,110,.1);border-color:var(--sky-600);color:var(--sky-600);font-weight:700;}
    /* Room preview */
    .room-preview{background:linear-gradient(135deg,var(--sky-900),var(--sky-700));border-radius:var(--radius);padding:1.5rem;color:#fff;text-align:center;}
    .preview-num{font-family:var(--font-d);font-size:3rem;font-weight:800;line-height:1;}
    .preview-type{font-size:.85rem;color:rgba(255,255,255,.6);margin:.3rem 0;}
    .preview-price{font-family:var(--font-d);font-size:1.3rem;font-weight:700;color:var(--accent2);margin-top:.8rem;}
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
        <a href="rooms.php" class="nav-link-item active"><i class="ni fa-solid fa-door-open"></i> Manage Rooms</a>
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
            <div class="page-title">Add New Room</div>
        </div>
        <div style="display:flex;gap:.8rem;">
            <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
            <a href="rooms.php" class="icon-btn"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="page-content">
        <?php if(!empty($errors)): ?>
        <div class="error-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-door-open" style="color:var(--sky-600);"></i> Room Details</div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label>Room Number *</label>
                                    <input type="text" name="room_number" class="form-control" placeholder="e.g. 101" value="<?= htmlspecialchars($_POST['room_number']??'') ?>" required oninput="updatePreview()">
                                </div>
                                <div class="col-md-4">
                                    <label>Room Type *</label>
                                    <select name="room_type" class="form-select" required onchange="updateCapacity(this.value);updatePreview()">
                                        <option value="">Select Type</option>
                                        <option value="Single" <?= ($_POST['room_type']??'')==='Single'?'selected':'' ?>>Single (1 bed)</option>
                                        <option value="Double" <?= ($_POST['room_type']??'')==='Double'?'selected':'' ?>>Double (2 beds)</option>
                                        <option value="Triple" <?= ($_POST['room_type']??'')==='Triple'?'selected':'' ?>>Triple (3 beds)</option>
                                        <option value="Quad"   <?= ($_POST['room_type']??'')==='Quad'?'selected':'' ?>>Quad (4 beds)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Floor</label>
                                    <select name="floor" class="form-select">
                                        <option value="Ground Floor" <?= ($_POST['floor']??'')==='Ground Floor'?'selected':'' ?>>Ground Floor</option>
                                        <option value="First Floor"  <?= ($_POST['floor']??'')==='First Floor'?'selected':'' ?>>First Floor</option>
                                        <option value="Second Floor" <?= ($_POST['floor']??'')==='Second Floor'?'selected':'' ?>>Second Floor</option>
                                        <option value="Third Floor"  <?= ($_POST['floor']??'')==='Third Floor'?'selected':'' ?>>Third Floor</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Capacity (Beds) *</label>
                                    <input type="number" name="capacity" id="capacityInput" class="form-control" min="1" max="10" value="<?= htmlspecialchars($_POST['capacity']??'1') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Monthly Fee (Rs.) *</label>
                                    <input type="number" name="monthly_fee" class="form-control" min="0" step="100" placeholder="e.g. 8000" value="<?= htmlspecialchars($_POST['monthly_fee']??'') ?>" required oninput="updatePreview()">
                                </div>
                                <div class="col-md-4">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="available"   <?= ($_POST['status']??'available')==='available'?'selected':'' ?>>Available</option>
                                        <option value="maintenance" <?= ($_POST['status']??'')==='maintenance'?'selected':'' ?>>Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card-head"><i class="fa-solid fa-star" style="color:var(--sky-600);"></i> Amenities</div>
                        <div class="form-card-body">
                            <div class="amenity-grid mb-3">
                                <?php
                                $amenityList = ['AC','Fan','WiFi','Attached Bath','Shared Bath','Balcony','Study Table','Wardrobe','TV','Geyser','Water Cooler'];
                                $selected = isset($_POST['amenity_check']) ? $_POST['amenity_check'] : [];
                                foreach($amenityList as $am):
                                ?>
                                <div>
                                    <input type="checkbox" name="amenity_check[]" id="am_<?= str_replace(' ','_',$am) ?>" value="<?= $am ?>" class="amenity-check" <?= in_array($am,$selected)?'checked':'' ?> onchange="updateAmenities()">
                                    <label for="am_<?= str_replace(' ','_',$am) ?>" class="amenity-label"><?= $am ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <label>Additional Amenities</label>
                            <input type="text" name="amenities" id="amenitiesInput" class="form-control" placeholder="Comma-separated: Parking, Locker..." value="<?= htmlspecialchars($_POST['amenities']??'') ?>">
                            <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Click above buttons or type manually</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Live Preview -->
                    <div class="form-card mb-3">
                        <div class="form-card-head"><i class="fa-solid fa-eye" style="color:var(--sky-600);"></i> Live Preview</div>
                        <div class="form-card-body">
                            <div class="room-preview">
                                <div class="preview-num" id="previewNum">---</div>
                                <div class="preview-type" id="previewType">Select type</div>
                                <div class="preview-price" id="previewPrice">Rs. 0</div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:.7rem;">
                        <button type="submit" class="btn-submit" style="justify-content:center;"><i class="fa-solid fa-plus"></i> Add Room</button>
                        <a href="rooms.php" class="btn-cancel" style="justify-content:center;"><i class="fa-solid fa-xmark"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

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

function updateCapacity(type){
    const map={Single:1,Double:2,Triple:3,Quad:4};
    const cap=document.getElementById('capacityInput');
    if(map[type]) cap.value=map[type];
    updatePreview();
}

function updatePreview(){
    const num=document.querySelector('[name=room_number]').value||'---';
    const type=document.querySelector('[name=room_type]').value||'';
    const fee=document.querySelector('[name=monthly_fee]').value||0;
    document.getElementById('previewNum').textContent=num;
    document.getElementById('previewType').textContent=type?type+' Room':'Select type';
    document.getElementById('previewPrice').textContent='Rs. '+Number(fee).toLocaleString();
}

function updateAmenities(){
    const checked=[...document.querySelectorAll('.amenity-check:checked')].map(c=>c.value);
    const extra=document.getElementById('amenitiesInput');
    const manual=extra.value.split(',').map(s=>s.trim()).filter(s=>s&&!checked.includes(s));
    extra.value=[...checked,...manual].join(', ');
}
</script>
</body>
</html>