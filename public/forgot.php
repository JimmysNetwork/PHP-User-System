<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';

if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Create token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // Expires in 1 hour

            // Delete existing resets
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

            // Insert new reset
            $insert = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $insert->execute([$email, $token, $expires]);

            // Send email (placeholder — replace with Mailgun, PHPMailer, etc.)
            $resetLink = "http://yourdomain.com/public/reset.php?token=$token";
            mail($email, "Password Reset", "Click here to reset your password: $resetLink");

            $success = 'If your email is registered, a password reset link has been sent.';
        } else {
            $error = 'Email not found.';
        }
    }
}
?>

<h2>Forgot Password</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label>Email Address:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Send Reset Link</button>
</form>

<?php require_once '../includes/footer.php'; ?>
