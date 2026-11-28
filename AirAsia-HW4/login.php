<?php
require_once __DIR__ . '/includes/auth.php';
if (currentUser() !== null) {
    header('Location: card-list.php', true, 303);
    exit;
}
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
$pageTitle = 'Log In';
require __DIR__ . '/includes/header.php';
?>
<main>
    <h1>Log In</h1>
    <?php if ($error !== null): ?>
        <p role="alert"><?= escapeHtml($error) ?></p>
    <?php endif; ?>
    <form method="post" action="authenticate.php">
        <?= csrfField() ?>
        <label for="userName">Username</label>
        <input id="userName" name="userName" autocomplete="username" maxlength="100" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Log In</button>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
