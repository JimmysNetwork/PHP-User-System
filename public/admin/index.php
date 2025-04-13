<?php
session_start();
require_once '../../app/controllers/Auth.php';
require_once '../../config/database.php';

// Only allow admins
if (!Auth::check() || !Auth::hasRole('admin')) {
    header("Location: ../login.php");
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h1 class="mb-4">Admin Dashboard</h1>

    <p><a href="../dashboard.php">← Back to Dashboard</a> | <a href="../logout.php">Logout</a></p>

    <h2 class="mt-4">User List</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
