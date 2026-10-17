<?php
/**
 * Oxionex Web Solutions - Admin Logout
 */

require_once '../includes/form-security.php';
oxionex_start_secure_session();
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: dashboard.php');
	exit;
}

require_admin_csrf();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
