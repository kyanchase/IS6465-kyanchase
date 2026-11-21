<?php
// Run from lab7: php -S 0.0.0.0:8000 php/router.php
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if ($path === '/') $path = '/index.php';
$pages = ['register','login','logout','profile','change-password','users','manage-user',
    'request-form','process-request','requests','request-details','edit-request','update-request','delete-request'];
$allowed = ['/index.php', '/css/styles.css'];
foreach ($pages as $page) $allowed[] = '/php/'.$page.'.php';
if (!in_array($path, $allowed, true)) { http_response_code(404); exit('Not found.'); }
$full = dirname(__DIR__).$path;
if (substr($path, -4) === '.css') {
    header('Content-Type: text/css; charset=UTF-8'); readfile($full); exit;
}
require $full;
