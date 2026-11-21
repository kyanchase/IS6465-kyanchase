<?php
require_once dirname(__DIR__) . '/repositories/userPDO.php';

function startSecureSession() {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    // Codespaces terminates HTTPS at its trusted forwarding proxy.
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || getenv('CODESPACES') === 'true';
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $https,
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
}
function csrfToken() {
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function validCsrfToken($input) {
    startSecureSession();
    return isset($input['csrf_token'], $_SESSION['csrf_token'])
        && is_string($input['csrf_token']) && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $input['csrf_token']);
}
function verifyCsrfToken($input) {
    if (!validCsrfToken($input)) {
        http_response_code(403);
        exit('Invalid CSRF token. Reload the form and try again.');
    }
}
function logoutSession() {
    startSecureSession();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000, 'path' => $p['path'], 'domain' => $p['domain'],
            'secure' => $p['secure'], 'httponly' => $p['httponly'], 'samesite' => $p['samesite']
        ]);
    }
    session_destroy();
}
function establishLogin($user) {
    startSecureSession();
    session_regenerate_id(true);
    $_SESSION = [
        'user_id' => (int)$user['user_id'], 'display_name' => $user['full_name'],
        'role' => $user['role'], 'last_activity' => time()
    ];
    csrfToken();
}
function requireLogin($pdo) {
    startSecureSession();
    header('Cache-Control: no-store');
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php'); exit;
    }
    if (time() - ($_SESSION['last_activity'] ?? 0) > 1200) {
        logoutSession(); header('Location: login.php?expired=1'); exit;
    }
    $user = findUserById($pdo, (int)$_SESSION['user_id']);
    if (!$user || $user['status'] !== 'Active') {
        logoutSession(); header('Location: login.php?inactive=1'); exit;
    }
    $_SESSION['last_activity'] = time();
    $_SESSION['display_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    return $user;
}
function requireRole($user, $allowedRoles) {
    if (!in_array($user['role'], $allowedRoles, true)) {
        http_response_code(403); exit('You are not authorized to perform this action.');
    }
}
function requirePost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST'); http_response_code(405); exit('POST required');
    }
}
