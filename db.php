<?php
// ============================================================
// Skyline Hostel Management System
// Database Connection & Configuration
// ============================================================

define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_NAME',     'skyline_hostel');
define('SITE_NAME',   'Skyline Hostel');
define('SITE_URL', 'http://127.0.0.1/skyline-hostel-management');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/skyline-hostel-management/assets/images/uploads/');
define('UPLOAD_URL',  SITE_URL . '/assets/images/uploads/');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'status'  => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset
$conn->set_charset('utf8mb4');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Helper Functions
// ============================================================

/**
 * Sanitize input against SQL injection
 */
function sanitize($conn, $data) {
    return $conn->real_escape_string(htmlspecialchars(trim($data)));
}

/**
 * Hash a password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Set a toast message in session
 */
function setToast($type, $message) {
    $_SESSION['toast'] = ['type' => $type, 'message' => $message];
}

/**
 * Check if admin is logged in
 */
function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

/**
 * Check if student is logged in
 */
function requireStudent() {
    if (!isset($_SESSION['student_id'])) {
        redirect(SITE_URL . '/student/login.php');
    }
}

/**
 * Format money
 */
function formatMoney($amount) {
    return 'Rs. ' . number_format($amount, 2);
}

/**
 * Format date nicely
 */
function formatDate($date) {
    return date('d M, Y', strtotime($date));
}

/**
 * Get status badge HTML
 */
function statusBadge($status) {
    $map = [
        'active'      => 'success',
        'inactive'    => 'secondary',
        'suspended'   => 'danger',
        'available'   => 'success',
        'full'        => 'danger',
        'maintenance' => 'warning',
        'pending'     => 'warning',
        'approved'    => 'success',
        'rejected'    => 'danger',
        'paid'        => 'success',
        'overdue'     => 'danger',
        'resolved'    => 'success',
        'in_progress' => 'info',
        'closed'      => 'secondary',
        'vacated'     => 'secondary',
        'waived'      => 'info',
    ];
    $color = $map[strtolower($status)] ?? 'secondary';
    $label = ucfirst(str_replace('_', ' ', $status));
    return "<span class=\"badge bg-{$color}\">{$label}</span>";
}

/**
 * Upload profile picture
 */
function uploadProfilePic($file, $prefix = 'student') {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, GIF, WEBP files allowed.'];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size must be under 2MB.'];
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $destPath = UPLOAD_PATH . $filename;

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['success' => true, 'filename' => $filename];
    }
    return ['success' => false, 'message' => 'Failed to upload file.'];
}

/**
 * Get total count from a table
 */
function getCount($conn, $table, $where = '1') {
    $res = $conn->query("SELECT COUNT(*) as cnt FROM `$table` WHERE $where");
    return $res ? $res->fetch_assoc()['cnt'] : 0;
}