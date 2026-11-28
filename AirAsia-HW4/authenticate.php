<?php
require_once __DIR__ . '/includes/auth.php';
requirePost();
requireCsrf();

if (currentUser() !== null) {
    header('Location: card-list.php', true, 303);
    exit;
}

require_once __DIR__ . '/includes/db.php';

$username = $_POST['userName'] ?? null;
$password = $_POST['password'] ?? null;
if (is_string($username) && is_string($password) && strlen($username) <= 100
    && strlen($password) <= 72 && strpos($password, "\0") === false) {
    $statement = $pdo->prepare('SELECT userId, userName, password, role FROM USER WHERE userName = :username');
    $statement->execute(['username' => trim($username)]);
    $user = $statement->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION = ['user' => [
            'userId' => (int) $user['userId'],
            'userName' => $user['userName'],
            'role' => $user['role'],
        ]];
        header('Location: card-list.php', true, 303);
        exit;
    }
}

$_SESSION['login_error'] = 'Invalid username or password.';
header('Location: login.php', true, 303);
exit;
