<?php
require_once __DIR__.'/../includes/bootstrap.php';
requirePost();
verifyCsrfToken($_POST);
logoutSession();
header('Location: login.php?logged_out=1', true, 303);
exit;
