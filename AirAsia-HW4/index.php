<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
header('Location: card-list.php', true, 303);
exit;
