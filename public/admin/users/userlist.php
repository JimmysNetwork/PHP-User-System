<?php
session_start();
require_once '../../../app/controllers/Auth.php';
require_once '../../../config/database.php';

// Only allow access if the user is an authenticated admin
if (!Auth::check() || !Auth::hasRole('admin')) {
    header('Location: ../../../login.php');
    exit();
}

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Get and sanitize filter inputs
$search = trim($_GET['search'] ?? '');
$role = $_GET['role'] ?? '';
$allowedRoles = ['admin', 'user'];
$role = in_array($role, $allowedRoles) ? $role : '';

// Cap search length for performance
if (strlen($search) > 100) {
    $search = substr($search, 0, 100);
}

// Construct SQL WHERE clause based on filters
$where = 'WHERE 1';
$params = [];

if ($search) {
    $where .= ' AND (username LIKE :search OR email LIKE :search)';
    $params[':search'] = "%$search%";
}

if ($role) {
    $where .= ' AND role = :role';
    $params[':role'] = $role;
}

// Get total number of filtered users
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM users $where");
$countStmt->execute($params);
$totalUsers = (int) $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $perPage);

// Prepare paginated query
$params[':limit'] = $perPage;
$params[':offset'] = $offset;
$stmt = $pdo->prepare(
    "SELECT id, username, email, role FROM users $where ORDER BY id ASC LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Bind search/role filters
foreach ($params as $key => $value) {
    if (!in_array($key, [':limit', ':offset'])) {
        $stmt->bindValue($key, $value);
    }
}

$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php
$pageTitle = 'Manage Users';
require_once '../../../includes/header.php';
?>

<h1>Manage Users</h1>
<p><a href="../index.php">← Admin Dashboard</a></p>

<!-- Filter/Search Form -->
<form method="get" class="mb-3 row g-2">
    <div class="col-sm-4">
        <input type="text" name="search" value="<?= htmlspecialchars(
            $search
        ) ?>" class="form-control" placeholder="Search by username or email">
    </div>
    <div class="col-sm-3">
        <select name="role" class="form-select">
            <option value="">All Roles</option>
            <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<!-- Users Table -->
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
                        <!-- Add CSRF token to delete link -->
                        <a href="delete.php?id=<?= $user['id'] ?>&csrf_token=<?= $_SESSION[
    'csrf_token'
] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <?php else: ?>
                        <span class="text-muted">Self</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination -->
<?php
$queryBase = $_GET;
unset($queryBase['page']);
$queryStr = http_build_query($queryBase);
$queryPrefix = $queryStr ? $queryStr . '&' : '';
?>

<nav aria-label="User pagination">
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= $queryPrefix ?>page=<?= $page - 1 ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= $queryPrefix ?>page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= $queryPrefix ?>page=<?= $page + 1 ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<?php require_once '../../../includes/footer.php'; ?>
