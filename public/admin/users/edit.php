<?php
session_start();
require_once '../../../app/controllers/Auth.php';
require_once '../../../config/database.php';

// Ensure user is logged in and has admin access
if (!Auth::check() || !Auth::hasRole('admin')) {
    header('Location: ../../../login.php');
    exit;
}

// Sanitize and validate user ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: userlist.php');
    exit;
}

// Fetch user by ID
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: userlist.php');
    exit;
}

// Initialize feedback variables
$error = '';
$success = '';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';

        // Validate input
        if (!$username || !$email) {
            $error = 'Username and email are required.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $error = 'Username must be 3–30 characters and contain only letters, numbers, or underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } elseif ($_SESSION['user_id'] == $id && $role !== $user['role']) {
            $error = 'You cannot change your own role.';
        } else {
            // Perform update
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $email, $role, $id]);

            $success = 'User updated successfully.';
            $user = ['username' => $username, 'email' => $email, 'role' => $role, 'id' => $id];
        }
    }
}
?>

<?php
$pageTitle = "Edit User";
require_once '../../../includes/header.php';
?>

<h2>Edit User</h2>
<p><a href="userlist.php">← Back to User List</a></p>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" class="mt-3">
    <!-- CSRF token -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update User</button>
</form>

<?php require_once '../../../includes/footer.php'; ?>
