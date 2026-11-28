<?php
// Router for the PHP development server. Only allow the app's public pages and assets.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$pages = [
    '/', '/index.php', '/login.php', '/authenticate.php', '/logout.php', '/card-list.php',
    '/card-details.php', '/card-add.php', '/card-update.php', '/card-delete.php',
    '/cust-add.php', '/customer-profile.php', '/redeem.php', '/process-redemption.php',
];

if ($path === '/router.php' && PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}
if (in_array($path, $pages, true)
    || (preg_match('~^/(?:css/[a-z0-9-]+\.css|images/[a-z0-9-]+\.svg)$~D', $path)
        && is_file(__DIR__ . $path))) {
    return false;
}

http_response_code(404);
echo 'Not found.';
