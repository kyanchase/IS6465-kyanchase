<?php
require_once __DIR__ . '/includes/authorization.php';
requireRole('Admin');
require_once __DIR__ . '/includes/db.php';

$fields = ['userName' => 'Username', 'firstName' => 'First name', 'lastName' => 'Last name'];
$values = array_fill_keys(array_keys($fields), '');
$errors = [];
$created = $_SESSION['created_customer'] ?? null;
unset($_SESSION['created_customer']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    foreach ($fields as $name => $label) {
        $input = $_POST[$name] ?? null;
        $values[$name] = is_string($input) ? trim($input) : '';
        if ($values[$name] === '' || strlen($values[$name]) > 100 || strpos($values[$name], "\0") !== false) {
            $errors[] = "$label must contain between 1 and 100 bytes.";
        }
    }
    $password = $_POST['password'] ?? null;
    if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72
        || strpos($password, "\0") !== false) {
        $errors[] = 'Use a password between 8 and 72 bytes.';
    }

    if ($errors === []) {
        $lookup = $pdo->prepare('SELECT 1 FROM USER WHERE userName = :username');
        $lookup->execute(['username' => $values['userName']]);
        $exists = $lookup->fetchColumn() !== false;
        $lookup->closeCursor();
        if ($exists) {
            $errors[] = 'That username is already in use.';
        } else {
            try {
                $pdo->beginTransaction();
                $insert = $pdo->prepare(
                    "INSERT INTO USER (userName, password, firstName, lastName, role)
                     VALUES (:userName, :password, :firstName, :lastName, 'Customer')"
                );
                $insert->execute($values + ['password' => password_hash($password, PASSWORD_DEFAULT)]);
                $account = $pdo->prepare("INSERT INTO ACCOUNT (userId, accountType, points) VALUES (?, 'Rewards', 0)");
                $account->execute([$pdo->lastInsertId()]);
                $pdo->commit();
                $_SESSION['created_customer'] = $values;
                header('Location: cust-add.php', true, 303);
                exit;
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'USER.userName') !== false) {
                    $errors[] = 'That username is already in use.';
                } else {
                    error_log($e->getMessage());
                    $errors[] = 'The customer could not be created. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Add Customer';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main>
    <h1>Add Customer</h1>
    <?php if ($created !== null): ?>
        <p role="status">Created customer <?= escapeHtml($created['firstName'] . ' ' . $created['lastName']) ?>
            (<?= escapeHtml($created['userName']) ?>) with 0 reward points.</p>
    <?php endif; ?>
    <?php foreach ($errors as $error): ?>
        <p role="alert"><?= escapeHtml($error) ?></p>
    <?php endforeach; ?>
    <form method="post" action="cust-add.php">
        <?= csrfField() ?>
        <?php foreach ($fields as $name => $label): ?>
            <label for="<?= $name ?>"><?= escapeHtml($label) ?></label>
            <input id="<?= $name ?>" name="<?= $name ?>" maxlength="100" value="<?= escapeHtml($values[$name]) ?>" required>
        <?php endforeach; ?>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        <button type="submit">Create Customer</button>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
