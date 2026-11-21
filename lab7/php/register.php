<?php
require_once __DIR__.'/../includes/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST);
    try {
        registerUser($pdo, $_POST);
        header('Location: login.php?registered=1', true, 303); exit;
    } catch (Throwable $e) {
        if (!($e instanceof InvalidArgumentException)) error_log($e->getMessage());
        $error = showPageError($e);
    }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Create account</h1>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= escapeHtml($error) ?></p><?php endif; ?>
<form class="account-form" method="post">
<input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
<label for="full_name">Full name</label><input id="full_name" name="full_name" maxlength="120" autocomplete="name" value="<?= escapeHtml($_POST['full_name'] ?? '') ?>" required>
<label for="email">Email</label><input id="email" name="email" type="email" maxlength="254" autocomplete="email" value="<?= escapeHtml($_POST['email'] ?? '') ?>" required>
<label for="password">Password</label><input id="password" name="password" type="password" minlength="12" autocomplete="new-password" aria-describedby="password-help" required>
<p id="password-help">Use a fictional test password of at least 12 characters, up to 72 bytes.</p>
<label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required>
<label for="role">Account type</label><select id="role" name="role"><option>Student</option><option <?= ($_POST['role'] ?? '') === 'Tutor' ? 'selected' : '' ?>>Tutor</option></select>
<button type="submit">Create account</button></form></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
