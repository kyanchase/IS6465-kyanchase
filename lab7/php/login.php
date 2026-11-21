<?php
require_once __DIR__.'/../includes/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST);
    try {
        $account = authenticateUser($pdo, (string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''));
        establishLogin($account);
        header('Location: profile.php', true, 303); exit;
    } catch (Throwable $e) {
        if (!($e instanceof InvalidArgumentException)) error_log($e->getMessage());
        $error = showPageError($e);
    }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Log in</h1>
<?php if (isset($_GET['registered'])): ?><p class="alert alert-success">Account created. You can now log in.</p><?php endif; ?>
<?php if (isset($_GET['logged_out'])): ?><p class="alert alert-success">You have logged out.</p><?php endif; ?>
<?php if (isset($_GET['expired'])): ?><p class="alert alert-warning">Your session expired. Log in again.</p><?php endif; ?>
<?php if (isset($_GET['inactive'])): ?><p class="alert alert-warning">Your account is unavailable. Contact the administrator.</p><?php endif; ?>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= escapeHtml($error) ?></p><?php endif; ?>
<form class="account-form" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
<label for="email">Email</label><input id="email" name="email" type="email" autocomplete="username" required>
<label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
<button type="submit">Log in</button><a href="register.php">Create account</a></form></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
