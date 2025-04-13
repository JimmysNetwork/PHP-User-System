<?php
session_start();
require_once '../config/database.php';
require_once '../app/controllers/Auth.php';
require_once '../app/helpers/Flash.php';

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirect if already logged in
if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// --- Login Rate Limiting (based on IP) ---
$ip = $_SERVER['REMOTE_ADDR'];
$maxAttempts = 5;
$attemptWindow = 900; // 15 minutes (in seconds)

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

// Remove old attempts
$_SESSION['login_attempts'] = array_filter(
    $_SESSION['login_attempts'],
    fn($timestamp) => $timestamp > time() - $attemptWindow
);

// Count attempts from this IP
$attempts = $_SESSION['login_attempts'][$ip] ?? [];

// Block login if too many attempts
if (count($attempts) >= $maxAttempts) {
    $error = 'Too many failed attempts. Please wait before trying again.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    // CSRF token validation
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        // Attempt login using Auth class
        if (Auth::login($email, $password)) {
            // Successful login
            session_regenerate_id(true); // Prevent session fixation
            unset($_SESSION['csrf_token']); // Refresh CSRF token
            unset($_SESSION['login_attempts'][$ip]); // Clear failed attempts

            Flash::set('success', 'Logged in successfully.');
            header('Location: dashboard.php');
            exit;
        } else {
            // Failed login: store timestamp of this attempt
            $_SESSION['login_attempts'][$ip][] = time();
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Login</h2>

    <!-- Flash success message -->
    <?php if ($msg = Flash::get('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Error feedback -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="post">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <div class="text-center mt-3">
            <a href="forgot.php">Forgot your password?</a>
        </div>

        <div class="text-center mt-2">
            <small>No account? <a href="register.php">Register</a></small>
        </div>
    </form>
</body>
</html>
