<?php
require_once '../includes/db.php';
session_destroy();
setcookie('PHPSESSID', '', time() - 3600, '/');
header('Location: ' . SITE_URL . '/student/login.php');
exit();