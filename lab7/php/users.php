<?php
require_once __DIR__.'/../includes/bootstrap.php';
$user = requireLogin($pdo); requireRole($user, ['Administrator']);
$search = trim((string)($_GET['search'] ?? ''));
$role = (string)($_GET['role'] ?? ''); $status = (string)($_GET['status'] ?? '');
if (!in_array($role, ['', 'Student','Tutor','Administrator'], true) || !in_array($status, ['', 'Active','Inactive'], true)) {
    http_response_code(400); exit('Invalid filter.');
}
$rows = listUsers($pdo, $search, $role, $status);
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Manage users</h1><form method="get" class="filter-form">
<label for="search">Name or email</label><input id="search" name="search" value="<?= escapeHtml($search) ?>">
<label for="role">Role</label><select id="role" name="role"><option value="">All roles</option><?php foreach (['Student','Tutor','Administrator'] as $r): ?><option <?= $r === $role ? 'selected' : '' ?>><?= escapeHtml($r) ?></option><?php endforeach; ?></select>
<label for="status">Status</label><select id="status" name="status"><option value="">All statuses</option><?php foreach (['Active','Inactive'] as $s): ?><option <?= $s === $status ? 'selected' : '' ?>><?= escapeHtml($s) ?></option><?php endforeach; ?></select>
<button type="submit">Search</button></form>
<?php if (!$rows): ?><p>No matching accounts.</p><?php else: ?><div class="table-responsive"><table><caption>User accounts</caption><thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= escapeHtml($row['full_name']) ?></td><td><?= escapeHtml($row['email']) ?></td><td><?= escapeHtml($row['role']) ?></td><td><?= escapeHtml($row['status']) ?></td><td><a href="manage-user.php?user_id=<?= (int)$row['user_id'] ?>">Manage <?= escapeHtml($row['full_name']) ?></a></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
