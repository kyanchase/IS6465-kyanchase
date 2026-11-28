<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Gift Card List';
$cards = $pdo->query(
    'SELECT cardId, cardName, cardType, points
     FROM GIFTCARD
     ORDER BY cardName, cardId'
)->fetchAll();

$images = [
    'Dining' => 'images/cafe-gift-card.svg',
    'Retail' => 'images/retail-gift-card.svg',
    'Travel' => 'images/travel-gift-card.svg',
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main>
    <h1>Gift Cards</h1>

    <?php if ($cards === []): ?>
        <p>No gift cards are available.</p>
    <?php endif; ?>

    <ul>
        <?php foreach ($cards as $card): ?>
            <?php $image = $images[$card['cardType']] ?? 'images/retail-gift-card.svg'; ?>
            <li>
                <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                     alt="" width="160">
                <a href="card-details.php?card_id=<?= (int) $card['cardId'] ?>">
                    <?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?>
                </a>
                <span><?= number_format((int) $card['points']) ?> points</span>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
