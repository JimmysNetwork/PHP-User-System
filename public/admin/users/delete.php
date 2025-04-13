<?php
session_start();
require_once '../../app/controllers/Auth.php';
require_once '../../config/database.php';

if (!Auth::check() || !Auth::hasRole('admin')) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id || $_SESSION['user_id'] == $id) {
    // Don't allow deleting self or invalid IDs
    header('Location: userlist.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header('Location: userlist.php');
exit;
