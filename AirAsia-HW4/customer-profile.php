<?php
require_once __DIR__ . '/includes/authorization.php';
$user = requireRole('Customer');
require_once __DIR__ . '/includes/db.php';

$statement = $pdo->prepare(
    'SELECT u.userName, u.firstName, u.lastName, a.accountId, a.accountType, a.points
     FROM USER u LEFT JOIN ACCOUNT a ON a.userId = u.userId
     WHERE u.userId = :id ORDER BY a.accountId LIMIT 1'
);
$statement->execute(['id' => $user['userId']]);
$profile = $statement->fetch();
if (!$profile || $profile['accountId'] === null) {
    http_response_code(404);
}
$pageTitle = 'My Account';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main>
    <h1>My Account</h1>
    <?php if (!$profile || $profile['accountId'] === null): ?>
        <p>No rewards account is linked to your user.</p>
    <?php else: ?>
        <dl>
            <dt>Name</dt><dd><?= escapeHtml($profile['firstName'] . ' ' . $profile['lastName']) ?></dd>
            <dt>Username</dt><dd><?= escapeHtml($profile['userName']) ?></dd>
            <dt>Account type</dt><dd><?= escapeHtml($profile['accountType']) ?></dd>
            <dt>Available reward points</dt><dd><?= number_format((int) $profile['points']) ?></dd>
        </dl>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
