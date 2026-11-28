<?php
require_once __DIR__ . '/includes/authorization.php';
$user = requireRole('Customer', 'Admin');
require_once __DIR__ . '/includes/db.php';

$account = $pdo->prepare('SELECT points FROM ACCOUNT WHERE userId = :userId ORDER BY accountId LIMIT 1');
$account->execute(['userId' => $user['userId']]);
$balance = $account->fetchColumn();
$cards = $pdo->query('SELECT cardId, cardName, cardValue, points FROM GIFTCARD ORDER BY cardName, cardId')->fetchAll();
$result = $_SESSION['redemption_result'] ?? null;
unset($_SESSION['redemption_result']);

$selected = null;
if (isset($_GET['card_id'])) {
    $id = filter_var($_GET['card_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    foreach ($cards as $card) {
        if ((int) $card['cardId'] === $id) {
            $selected = $card;
            break;
        }
    }
    if ($selected === null) {
        http_response_code(404);
        $result = ['error' => 'Gift card not found.'];
    }
}

$pageTitle = 'Redeem Gift Cards';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main>
    <h1>Redeem Gift Cards</h1>
    <?php if (isset($result['error'])): ?>
        <p role="alert"><?= escapeHtml($result['error']) ?></p>
    <?php elseif ($result !== null): ?>
        <p role="status">Redeemed <?= escapeHtml($result['cardName']) ?>.
            Your updated balance is <?= number_format($result['balance']) ?> points.</p>
    <?php endif; ?>
    <?php if ($balance === false): ?>
        <p>No rewards account is linked to your user.</p>
    <?php else: ?>
        <p>Available points: <?= number_format((int) $balance) ?></p>
        <?php if ($selected !== null): ?>
            <h2>Confirm <?= escapeHtml($selected['cardName']) ?></h2>
            <p>Value: $<?= number_format((float) $selected['cardValue'], 2) ?>.
                Required points: <?= number_format((int) $selected['points']) ?>.</p>
            <form method="post" action="process-redemption.php">
                <?= csrfField() ?>
                <input type="hidden" name="card_id" value="<?= (int) $selected['cardId'] ?>">
                <button type="submit">Confirm Redemption</button>
                <a href="redeem.php">Cancel</a>
            </form>
        <?php elseif ($cards === []): ?>
            <p>There are currently no gift cards available.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($cards as $card): ?>
                    <li><a href="redeem.php?card_id=<?= (int) $card['cardId'] ?>"><?= escapeHtml($card['cardName']) ?></a>
                        ($<?= number_format((float) $card['cardValue'], 2) ?>,
                        <?= number_format((int) $card['points']) ?> points)</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
