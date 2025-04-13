<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/Auth.php';
require_once __DIR__ . '/../app/helpers/Flash.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'My Website' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <nav class="mb-4">
        <a href="/public/index.php">Home</a>
        <?php if (Auth::check()): ?>
            <a href="/public/dashboard.php">Dashboard</a>
            <?php if (Auth::hasRole('admin')): ?>
                <a href="/public/admin/index.php">Admin</a>
            <?php endif; ?>
            <a href="/public/logout.php">Logout</a>
        <?php else: ?>
            <a href="/public/login.php">Login</a>
            <a href="/public/register.php">Register</a>
        <?php endif; ?>
    </nav>

    <?php if ($msg = Flash::get('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php elseif ($msg = Flash::get('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
