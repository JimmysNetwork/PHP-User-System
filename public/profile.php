<?php
session_start();
require_once '../app/controllers/Auth.php';
require_once '../config/database.php';
require_once '../includes/header.php';

// Only allow access if the user is logged in
if (!Auth::check()) {
    header('Location: login.php');
    exit();
}

// Generate CSRF token if missing
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user = Auth::user();
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = 'Invalid CSRF token.';
    } else {
        // Sanitize and limit input length
        $username = substr(trim($_POST['username'] ?? ''), 0, 30);
        $email = substr(trim($_POST['email'] ?? ''), 0, 255);
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Validate input fields
        if (!$username || !$email) {
            $error = 'Username and email are required.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $error =
                'Username must be 3–30 characters and only contain letters, numbers, or underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } elseif ($password && $password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            // Check for email conflict with another user
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $check->execute([$email, $user->id]);
            if ($check->fetch()) {
                $error = 'That email is already in use by another account.';
            } else {
                // Build dynamic SQL query with optional password update
                $query =
                    'UPDATE users SET username = ?, email = ?' .
                    ($password ? ', password_hash = ?' : '') .
                    ' WHERE id = ?';
                $stmt = $pdo->prepare($query);

                // Bind parameters
                $params = [$username, $email];
                if ($password) {
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
                $params[] = $user->id;
                $stmt->execute($params);

                $success = 'Profile updated successfully.';
                $user = Auth::user(); // Refresh user object
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5" style="max-width: 600px;">
    <h2 class="mb-4 text-center">My Profile</h2>

    <!-- Display success or error message -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Profile Update Form -->
    <form method="post">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="mb-3">
            <label class="form-label">Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars(
                $user->username
            ) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars(
                $user->email
            ) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">New Password: <small class="text-muted">(leave blank to keep current)</small></label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm New Password:</label>
            <input type="password" name="confirm_password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>

    <!-- Back link -->
    <div class="text-center mt-4">
        <a href="../dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
    </div>
</body>
</html>
