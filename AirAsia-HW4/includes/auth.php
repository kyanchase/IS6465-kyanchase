<?php

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (substr($_SERVER['HTTP_HOST'] ?? '', -strlen('.app.github.dev')) === '.app.github.dev'
            && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
    'samesite' => 'Lax',
]);
session_start();
header('Cache-Control: no-store');

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): array
{
    $user = currentUser();
    if ($user === null) {
        header('Location: login.php', true, 303);
        exit;
    }
    return $user;
}

function csrfField(): string
{
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

function requireCsrf(): void
{
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit('This form has expired. Return to the previous page and try again.');
    }
}

function requirePost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('This action requires a form submission.');
    }
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
