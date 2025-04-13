<?php
session_start();
require_once '../../app/controllers/Auth.php';
require_once '../../config/database.php';

// Restrict access to admins only
if (!Auth::check() || !Auth::hasRole('admin')) {
    header('Location: ../login.php');
    exit;
}

// Get user list
$stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>

<?php
$pageTitle = "Manage Users";
require_once '../../includes/header.php';
?>

<h1>Manage Users</h1>
<p><a href="index.php">← Admin Dashboard</a></p>

<table class="table table-bordered table-hover mt-3">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <?php if ($_SESSION['user_id'] != $user['id']): ?>
                        <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <?php else: ?>
                        <span class="text-muted">Self</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>
