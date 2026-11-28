<?php
require_once __DIR__ . '/includes/authorization.php';
$user = requireRole('Customer', 'Admin');
requirePost();
requireCsrf();
require_once __DIR__ . '/includes/db.php';

function finishRedemption(array $result): void
{
    $_SESSION['redemption_result'] = $result;
    header('Location: redeem.php', true, 303);
    exit;
}

$cardId = filter_var($_POST['card_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($cardId === false) {
    finishRedemption(['error' => 'Select a valid gift card.']);
}

try {
    $pdo->beginTransaction();

    $cardLookup = $pdo->prepare('SELECT cardName, points FROM GIFTCARD WHERE cardId = :cardId');
    $cardLookup->execute(['cardId' => $cardId]);
    $card = $cardLookup->fetch();
    $cardLookup->closeCursor();
    if (!$card) {
        $pdo->rollBack();
        finishRedemption(['error' => 'That gift card is no longer available.']);
    }

    $accountLookup = $pdo->prepare('SELECT accountId, points FROM ACCOUNT WHERE userId = :userId ORDER BY accountId LIMIT 1');
    $accountLookup->execute(['userId' => $user['userId']]);
    $account = $accountLookup->fetch();
    $accountLookup->closeCursor();
    if (!$account) {
        $pdo->rollBack();
        finishRedemption(['error' => 'No rewards account is linked to your user.']);
    }

    $cost = (int) $card['points'];
    $deduct = $pdo->prepare(
        'UPDATE ACCOUNT SET points = points - :cost
         WHERE accountId = :accountId AND userId = :userId AND points >= :minimum'
    );
    $deduct->bindValue(':cost', $cost, PDO::PARAM_INT);
    $deduct->bindValue(':minimum', $cost, PDO::PARAM_INT);
    $deduct->bindValue(':accountId', (int) $account['accountId'], PDO::PARAM_INT);
    $deduct->bindValue(':userId', $user['userId'], PDO::PARAM_INT);
    $deduct->execute();
    if ($deduct->rowCount() !== 1) {
        $pdo->rollBack();
        finishRedemption(['error' => 'You do not have enough points to redeem this gift card.']);
    }

    $insert = $pdo->prepare(
        'INSERT INTO REDEMPTION (date, accountId, cardId, pointsRedeemed)
         VALUES (:date, :accountId, :cardId, :cost)'
    );
    $insert->execute([
        'date' => gmdate(DATE_ATOM),
        'accountId' => $account['accountId'],
        'cardId' => $cardId,
        'cost' => $cost,
    ]);
    $pdo->commit();
    finishRedemption(['cardName' => $card['cardName'], 'balance' => (int) $account['points'] - $cost]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($e->getMessage());
    finishRedemption(['error' => 'Your redemption could not be completed. Please try again.']);
}
