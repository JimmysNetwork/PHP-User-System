<?php
session_start();
require_once '../app/controllers/Auth.php';
require_once '../includes/header.php';

// Restrict access to logged-in users only
if (!Auth::check()) {
    header('Location: login.php');
    exit();
}

$user = Auth::user();
?>

<h1>Welcome, <?= htmlspecialchars($user->username) ?>!</h1>
<p>Your role: <?= htmlspecialchars($user->role) ?></p>
<a href="logout.php">Logout</a>

<?php require_once '../includes/footer.php'; ?>
