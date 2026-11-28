<?php
require_once __DIR__ . '/auth.php';

function requireRole(string ...$allowedRoles): array
{
    $user = requireLogin();
    if (!in_array($user['role'], $allowedRoles, true)) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
    return $user;
}
