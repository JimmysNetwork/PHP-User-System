<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../app/helpers/Flash.php';

if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (!$token) {
    echo "<div class='alert alert-danger'>Invalid or missing token.</div>";
    echo "<a href='forgot.php' class='btn btn-secondary'>Request a New Link</a>";
    require_once '../includes/footer.php';
    exit;
}

// Validate token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at >= NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    echo "<div class='alert alert-danger'>This reset link is invalid or has expired.</div>";
    echo "<a href='forgot.php' class='btn btn-secondary'>Request a New Link</a>";
    require_once '../includes/footer.php';
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$password || !$confirm) {
        $error = "Please enter and confirm your new password.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Update user password
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $update->execute([$hash, $reset['email']]);

        // Delete reset token
        $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$reset['email']]);

        // Set flash message and redirect to login
        Flash::set('success', 'Your password has been reset. You can now log in.');
        header('Location: login.php');
        exit;
    }
}
?>

<h2>Reset Your Password</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label>New Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Reset Password</button>
</form>

<?php require_once '../includes/footer.php'; ?>
