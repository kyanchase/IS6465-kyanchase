<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Delete Gift Card';
$problem = null;
$rawId = $_GET['card_id'] ?? null;
$cardId = is_string($rawId)
    ? filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;

if ($cardId === false) {
    http_response_code(400);
    $problem = 'Enter a valid gift card ID.';
} else {
    $statement = $pdo->prepare(
        'SELECT cardName FROM GIFTCARD WHERE cardId = :id'
    );
    $statement->bindValue(':id', $cardId, PDO::PARAM_INT);
    $statement->execute();
    $card = $statement->fetch();

    if ($card === false) {
        http_response_code(404);
        $problem = 'Gift card not found.';
    }
}

if ($problem === null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['confirm_delete'] ?? '') === 'yes') {
        $statement = $pdo->prepare('DELETE FROM GIFTCARD WHERE cardId = :id');
        $statement->bindValue(':id', $cardId, PDO::PARAM_INT);
        $statement->execute();

        header('Location: card-list.php', true, 303);
        exit;
    }

    http_response_code(400);
    $problem = 'Confirm the deletion from the gift card detail page.';
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main>
    <h1>Delete Gift Card</h1>

    <?php if ($problem !== null): ?>
        <p><?= htmlspecialchars($problem, ENT_QUOTES, 'UTF-8') ?></p>
        <p><a href="card-list.php">Return to Gift Card List</a></p>
    <?php else: ?>
        <p>
            Are you sure you want to delete
            <strong><?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?></strong>?
        </p>

        <form method="post" action="card-delete.php?card_id=<?= (int) $cardId ?>">
            <button type="submit" name="confirm_delete" value="yes">Delete Gift Card</button>
            <a href="card-details.php?card_id=<?= (int) $cardId ?>">Cancel</a>
        </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>