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
    <title><?= $pageTitle ?? 'PHP User System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <a class="navbar-brand" href="/public/index.php">My Website</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (Auth::check()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/profile.php">Profile</a>
                    </li>
                    <?php if (Auth::hasRole('admin')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/public/admin/index.php">Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($msg = Flash::get('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php elseif ($msg = Flash::get('error')): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

