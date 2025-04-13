<?php
session_start();
require_once '../../../app/controllers/Auth.php';
require_once '../../../config/database.php';

// Only allow access if the user is authenticated and is an admin
if (!Auth::check() || !Auth::hasRole('admin')) {
    header('Location: ../../../login.php');
    exit;
}

// Validate CSRF token
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token.');
}

// Sanitize and cast the ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Prevent deleting yourself or invalid ID
if ($id <= 0 || $_SESSION['user_id'] == $id) {
    header('Location: userlist.php');
    exit;
}

// Delete the user securely
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Redirect back to the user list
header('Location: userlist.php');
exit;
