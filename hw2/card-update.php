<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Gift Card | AirAsia Rewards</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="site-header">
    <a class="brand" href="card-list.php">AirAsia <span>Rewards</span></a>
    <nav aria-label="Main navigation"><a href="card-list.php">Gift cards</a><a href="card-add.php">Add gift card</a><a href="login.php">Logout</a></nav>
  </header>
  <main id="main">
    <a class="back-link" href="card-list.php">&larr; Back to gift cards</a>
    <section class="panel form-panel"><p class="eyebrow">GIFT CARD MANAGEMENT</p><h1>Update Gift Card</h1>
      <p class="intro">Edit the selected gift card.</p>
      <!-- No editable Card ID: the eventual database generates it. -->
      <form action="card-list.php" method="post"><div class="field"><label for="card-name">Card Name</label><input id="card-name" name="card-name" type="text" maxlength="100" value="Retail gift card" required></div>
<div class="field"><label for="card-type">Card Type</label><input id="card-type" name="card-type" type="text" maxlength="100" value="Retail" required></div>
<div class="field"><label for="card-value">Card Value</label><input id="card-value" name="card-value" type="number" min="0.01" step="0.01" value="25.00" required></div>
<div class="field"><label for="points">Points Required</label><input id="points" name="points" type="number" min="1" step="1" value="2500" required></div>

        <div class="actions"><button type="submit">Save changes</button><a class="button secondary" href="card-list.php">Cancel</a></div>
      </form><p class="hint">This static demo returns to the list without saving changes.</p>
    </section>
  </main>
  <footer>AirAsia Rewards &middot; Student demonstration website</footer>
</body>
</html>
