<?php
require_once __DIR__.'/../includes/bootstrap.php';
$user = requireLogin($pdo); requireRole($user, ['Administrator']);
$id = filter_var($_GET['user_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Invalid user ID.'); }
$target = findUserById($pdo, $id);
if (!$target) { http_response_code(404); exit('User not found.'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST);
    try {
        if (($_POST['confirmation'] ?? '') !== 'yes') throw new InvalidArgumentException('Confirm the change before continuing.');
        if (($_POST['action'] ?? '') === 'delete') {
            deleteUserAccount($pdo, $user['user_id'], $id);
        } elseif (($_POST['action'] ?? '') === 'access') {
            manageUserAccess($pdo, $user['user_id'], $id, (string)($_POST['role'] ?? ''), (string)($_POST['status'] ?? ''));
            header('Location: manage-user.php?user_id='.$id.'&saved=1', true, 303); exit;
        } else { throw new InvalidArgumentException('Invalid action.'); }
    } catch (Throwable $e) {
        if (!($e instanceof InvalidArgumentException)) error_log($e->getMessage());
        $error = showPageError($e);
    }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Manage account</h1><p><?= escapeHtml($target['full_name']) ?> · <?= escapeHtml($target['email']) ?></p>
<?php if (isset($_GET['saved'])): ?><p class="alert alert-success">Account access updated.</p><?php endif; ?>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= escapeHtml($error) ?></p><?php endif; ?>
<form class="account-form" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><input type="hidden" name="action" value="access">
<label for="role">Role</label><select id="role" name="role"><?php foreach (['Student','Tutor','Administrator'] as $r): ?><option <?= $r === $target['role'] ? 'selected' : '' ?>><?= escapeHtml($r) ?></option><?php endforeach; ?></select>
<label for="status">Status</label><select id="status" name="status"><?php foreach (['Active','Inactive'] as $s): ?><option <?= $s === $target['status'] ? 'selected' : '' ?>><?= escapeHtml($s) ?></option><?php endforeach; ?></select>
<label><input type="checkbox" name="confirmation" value="yes" required> Confirm this role or status change</label>
<button type="submit">Save account access</button></form>
<h2>Delete account</h2><p>Permanent deletion is blocked until a retention policy is approved. Related tutoring records are retained. You can deactivate this account above.</p>
<form class="account-form" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><input type="hidden" name="action" value="delete">
<label><input type="checkbox" name="confirmation" value="yes" required> Confirm deletion request and policy check</label><button type="submit">Request deletion</button><a href="users.php">Cancel and return to users</a></form></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
