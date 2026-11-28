<?php
require_once __DIR__ . '/includes/authorization.php';
requireRole('Admin');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$pageTitle = 'Add Gift Card';
$result = validateGiftCard([]);
$fields = $result['fields'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $result = validateGiftCard($_POST);
    $fields = $result['fields'];
    $errors = $result['errors'];

    if ($errors === []) {
        $statement = $pdo->prepare(
            'INSERT INTO GIFTCARD (cardName, cardType, cardValue, points)
             VALUES (:cardName, :cardType, :cardValue, :points)'
        );
        $statement->bindValue(':cardName', $fields['cardName']);
        $statement->bindValue(':cardType', $fields['cardType']);
        $statement->bindValue(':cardValue', $fields['cardValue']);
        $statement->bindValue(':points', (int) $fields['points'], PDO::PARAM_INT);
        $statement->execute();

        header('Location: card-list.php', true, 303);
        exit;
    }

    http_response_code(422);
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main>
    <h1>Add Gift Card</h1>

    <?php if ($errors !== []): ?>
        <div role="alert">
            <p>Please correct the following:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="card-add.php">
        <?= csrfField() ?>
        <p>
            <label for="cardName">Card Name</label>
            <input id="cardName" name="cardName" maxlength="100" required
                   value="<?= htmlspecialchars($fields['cardName'], ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label for="cardType">Card Type</label>
            <select id="cardType" name="cardType" required>
                <option value="">Choose a type</option>
                <?php foreach (giftCardTypes() as $type): ?>
                    <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $fields['cardType'] === $type ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="cardValue">Card Value</label>
            <input id="cardValue" name="cardValue" type="number" min="0.01" step="0.01" required
                   value="<?= htmlspecialchars($fields['cardValue'], ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label for="points">Points Required</label>
            <input id="points" name="points" type="number" min="1" step="1" required
                   value="<?= htmlspecialchars($fields['points'], ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <button type="submit">Add Gift Card</button>
        <a href="card-list.php">Cancel</a>
    </form>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
