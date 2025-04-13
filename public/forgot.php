<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../app/controllers/Auth.php'; // Ensure Auth is available

// Redirect logged-in users to dashboard
if (Auth::check()) {
    header('Location: dashboard.php');
    exit();
}

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = 'Invalid CSRF token.';
    } else {
        $email = trim($_POST['email']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Check if a user with this email exists
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Generate token and expiration regardless of whether user exists (to avoid enumeration)
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

            // Remove any existing password reset requests for this email
            $pdo->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);

            // Insert new password reset entry
            $insert = $pdo->prepare(
                'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)'
            );
            $insert->execute([$email, $token, $expires]);

            // Construct reset URL dynamically based on server
            $baseURL = 'https://' . $_SERVER['HTTP_HOST'] . '/public';
            $resetLink = "$baseURL/reset.php?token=$token";

            // TODO: Replace with PHPMailer, Mailgun, etc. in production
            mail($email, 'Password Reset', "Click here to reset your password: $resetLink");

            // Always show success message — no email enumeration
            $success = 'If your email is registered, a password reset link has been sent.';
        }
    }
}
?>

<!-- Page Content -->
<h2>Forgot Password</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label>Email Address:</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Send Reset Link</button>
</form>

<?php require_once '../includes/footer.php'; ?>
