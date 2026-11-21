<nav class="navbar navbar-dark bg-primary" aria-label="Main navigation">
<div class="container"><a class="navbar-brand" href="../index.php">Peer Tutoring Hub</a><div class="nav-links">
<?php if (isset($user)): ?>
<a href="profile.php">My profile</a><a href="change-password.php">Change password</a>
<?php if (in_array($user['role'], ['Student','Administrator'], true)): ?><a href="requests.php"><?= $user['role'] === 'Student' ? 'My requests' : 'All requests' ?></a><?php endif; ?>
<?php if ($user['role'] === 'Student'): ?><a href="request-form.php">Request a session</a><?php endif; ?>
<?php if ($user['role'] === 'Administrator'): ?><a href="users.php">Manage users</a><?php endif; ?>
<form class="logout-form" action="logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><button type="submit">Log out</button></form>
<?php else: ?><a href="login.php">Log in</a><a href="register.php">Create account</a><?php endif; ?>
</div></div></nav>
