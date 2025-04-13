<?php
session_start();
require_once '../app/controllers/Auth.php';
require_once '../config/database.php';
require_once '../includes/header.php';

if (!Auth::check()) {
    header('Location: login.php');
    exit;
}

$user = Auth::user();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$username || !$email) {
        $error = 'Username and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password && $password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $query = "UPDATE users SET username = ?, email = ?" . ($password ? ", password_hash = ?" : "") . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $params = [$username, $email];
        if ($password) {
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        $params[] = $user->id;
        $stmt->execute($params);

        $success = 'Profile updated successfully.';
        $user = Auth::user(); // Refresh user data
    }
}
?>

<h1>My Profile</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user->username) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user->email) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>New Password: <small class="text-muted">(leave blank to keep current)</small></label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

<?php require_once '../includes/footer.php'; ?>
