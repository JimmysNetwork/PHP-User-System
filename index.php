<?php
session_start();
require_once 'includes/header.php';
?>

<div class="text-center py-5">
    <h1 class="mb-4">Welcome to the PHP OOP User System</h1>

    <p class="lead mb-4">
        A clean and reusable authentication system built with modern PHP using OOP principles.
        Designed to jumpstart any PHP project that needs user management, role control, and admin tools.
    </p>

    <div class="mb-4">
        <a href="public/login.php" class="btn btn-primary me-2">Login</a>
        <a href="public/register.php" class="btn btn-outline-primary">Register</a>
    </div>

    <hr class="my-5" style="max-width: 400px; margin: auto;">

    <div class="text-muted small">
        <p class="mb-1">
            📜 <strong>License:</strong> MIT — free to use and modify for personal or commercial use.
        </p>
        <p>
            👨‍💻 Made by <a href="https://github.com/JimmysNetwork" target="_blank">JimmysNetwork</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
