<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__).'/config/db.php';
require_once dirname(__DIR__).'/services/userService.php';
if ($argc !== 4) {
    fwrite(STDERR, 'Usage: php create-admin.php "Full Name" email@example.com "password"'.PHP_EOL);
    exit(1);
}
try {
    createAccount(openDatabase(), [
        'full_name'=>$argv[1], 'email'=>$argv[2], 'password'=>$argv[3],
        'confirm_password'=>$argv[3], 'role'=>'Administrator'
    ], true);
    echo 'Administrator created.'.PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
