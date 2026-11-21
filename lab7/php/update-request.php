<?php
require_once __DIR__.'/../includes/bootstrap.php';
$user = requireLogin($pdo); requireRole($user, ['Student','Administrator']);
requirePost(); verifyCsrfToken($_POST);
$id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Invalid request ID.'); }
if (!findRequest($pdo, $id, requestOwnerScope($user))) { http_response_code(404); exit('Request not found.'); }
try {
    if (!reviseAuthenticatedRequest($pdo, $user, $id, $_POST)) { http_response_code(409); exit('Only Open requests may be edited.'); }
    header('Location: request-details.php?request_id='.$id, true, 303); exit;
} catch (InvalidArgumentException $e) { http_response_code(422); exit(escapeHtml($e->getMessage())); }
catch (Throwable $e) { error_log($e->getMessage()); http_response_code(500); exit('Unable to update request.'); }
