<?php
require_once __DIR__.'/../includes/bootstrap.php';
$user = requireLogin($pdo); $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST);
    try {
        changeOwnPassword($pdo, $user['user_id'], $_POST);
        establishLogin(findUserById($pdo, $user['user_id']));
        header('Location: change-password.php?saved=1', true, 303); exit;
    } catch (Throwable $e) {
        if (!($e instanceof InvalidArgumentException)) error_log($e->getMessage());
        $error = showPageError($e);
    }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Change password</h1>
<?php if (isset($_GET['saved'])): ?><p class="alert alert-success">Password updated.</p><?php endif; ?>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= escapeHtml($error) ?></p><?php endif; ?>
<form class="account-form" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
<label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
<label for="password">New password</label><input id="password" name="password" type="password" minlength="12" autocomplete="new-password" aria-describedby="password-help" required><p id="password-help">At least 12 characters and no more than 72 bytes.</p>
<label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required>
<button type="submit">Update password</button></form></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
