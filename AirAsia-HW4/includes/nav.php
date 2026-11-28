<nav>
    <a href="card-list.php">Gift Card List</a>
    <?php if (currentUser()['role'] === 'Admin'): ?>
        <a href="card-add.php">Add Gift Card</a>
        <a href="cust-add.php">Add Customer</a>
    <?php else: ?>
        <a href="customer-profile.php">My Account</a>
    <?php endif; ?>
    <a href="redeem.php">Redeem Gift Cards</a>
    <form method="post" action="logout.php" style="display:inline">
        <?= csrfField() ?>
        <button type="submit">Log Out</button>
    </form>
</nav>
