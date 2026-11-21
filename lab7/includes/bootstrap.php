<?php
require_once dirname(__DIR__).'/config/db.php';
require_once __DIR__.'/auth.php';
require_once dirname(__DIR__).'/services/requestService.php';
require_once dirname(__DIR__).'/services/userService.php';
startSecureSession();
header('Cache-Control: no-store');
$pdo = openDatabase();
function showPageError($error) {
    http_response_code(422);
    return $error instanceof InvalidArgumentException ? $error->getMessage() : 'Unable to save. Check your input or try again.';
}
