<?php
require_once __DIR__.'/../includes/bootstrap.php';
$user = requireLogin($pdo); requireRole($user, ['Student','Administrator']);
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','POST'], true)) { http_response_code(405); exit('Method not allowed.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') verifyCsrfToken($_POST);
$id = filter_var(($_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET)['request_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Invalid request ID.'); }
$scope = requestOwnerScope($user);
$r = findRequest($pdo, $id, $scope);
if (!$r) { http_response_code(404); exit('Request not found.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!cancelOpenRequest($pdo, $id, $scope)) { http_response_code(409); exit('Only Open requests may be cancelled.'); }
        header('Location: request-details.php?request_id='.$id, true, 303); exit;
    } catch (Throwable $e) { error_log($e->getMessage()); http_response_code(500); exit('Unable to cancel request.'); }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Cancel tutoring request</h1><p>Cancel request #<?= (int)$id ?>? The record will be retained with status Cancelled.</p>
<?php if ($r['status'] === 'Open'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><input type="hidden" name="request_id" value="<?= (int)$id ?>"><button type="submit">Yes, cancel request</button></form>
<?php else: ?><p>Only Open requests can be cancelled.</p><?php endif; ?>
<a href="request-details.php?request_id=<?= (int)$id ?>">Keep request</a></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
