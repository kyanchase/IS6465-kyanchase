<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Gift Card Details';
$card = null;
$error = null;
$rawId = $_GET['card_id'] ?? null;
$cardId = is_string($rawId)
    ? filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;

if ($cardId === false) {
    http_response_code(400);
    $error = 'Enter a valid gift card ID.';
} else {
    $statement = $pdo->prepare(
        'SELECT cardId, cardName, cardType, cardValue, points
         FROM GIFTCARD
         WHERE cardId = :id'
    );
    $statement->bindValue(':id', $cardId, PDO::PARAM_INT);
    $statement->execute();
    $card = $statement->fetch();

    if ($card === false) {
        http_response_code(404);
        $error = 'Gift card not found.';
    }
}

$images = [
    'Dining' => 'images/cafe-gift-card.svg',
    'Retail' => 'images/retail-gift-card.svg',
    'Travel' => 'images/travel-gift-card.svg',
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main>
    <h1>Gift Card Details</h1>

    <?php if ($error !== null): ?>
        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php else: ?>
        <?php $image = $images[$card['cardType']] ?? 'images/retail-gift-card.svg'; ?>
        <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
             alt="" width="160">

        <dl>
            <dt>Card ID</dt>
            <dd><?= (int) $card['cardId'] ?></dd>
            <dt>Card Name</dt>
            <dd><?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?></dd>
            <dt>Card Type</dt>
            <dd><?= htmlspecialchars($card['cardType'], ENT_QUOTES, 'UTF-8') ?></dd>
            <dt>Card Value</dt>
            <dd><?= number_format((float) $card['cardValue'], 2) ?></dd>
            <dt>Points Required</dt>
            <dd><?= number_format((int) $card['points']) ?></dd>
        </dl>

        <p>
        <?php if (currentUser()['role'] === 'Admin'): ?>
            <a href="card-update.php?card_id=<?= (int) $card['cardId'] ?>">Update Gift Card</a>
            <a href="card-delete.php?card_id=<?= (int) $card['cardId'] ?>">Delete Gift Card</a>
        <?php endif; ?>
        </p>
    <?php endif; ?>

    <p><a href="card-list.php">Return to Gift Card List</a></p>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
