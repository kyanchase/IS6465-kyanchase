<?php
require_once __DIR__.'/../includes/bootstrap.php';
$user = requireLogin($pdo); $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST);
    try {
        saveOwnProfile($pdo, (int)$user['user_id'], $_POST);
        header('Location: profile.php?saved=1', true, 303); exit;
    } catch (Throwable $e) {
        if (!($e instanceof InvalidArgumentException)) error_log($e->getMessage());
        $error = showPageError($e);
    }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>My profile</h1>
<?php if (isset($_GET['saved'])): ?><p class="alert alert-success">Profile updated.</p><?php endif; ?>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= escapeHtml($error) ?></p><?php endif; ?>
<p>Role: <strong><?= escapeHtml($user['role']) ?></strong> · Status: <?= escapeHtml($user['status']) ?></p>
<?php if ($user['role'] === 'Tutor'): ?><p>Tutor accounts can maintain their profiles. Tutor request workflows are outside this lab.</p><?php endif; ?>
<form class="account-form" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
<label for="full_name">Full name</label><input id="full_name" name="full_name" maxlength="120" value="<?= escapeHtml($_POST['full_name'] ?? $user['full_name']) ?>" required>
<label for="email">Email</label><input id="email" name="email" type="email" maxlength="254" value="<?= escapeHtml($_POST['email'] ?? $user['email']) ?>" required>
<button type="submit">Save changes</button></form></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
