<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | AirAsia Rewards</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="site-header">
    <a class="brand" href="login.php">AirAsia <span>Rewards</span></a>
    
  </header>
  <main id="main">
    <section class="panel login-panel">
      <p class="eyebrow">WELCOME TO REWARDS</p>
      <h1>A little closer to your next reward.</h1>
      <p class="intro">Sign in to explore gift cards.</p>
      <!-- Static demo: submitting navigates to the list without authenticating. -->
      <form action="card-list.php" method="post">
        <div class="field"><label for="username">Username</label><input id="username" name="username" type="text" autocomplete="username" required></div>
        <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
        <button type="submit">Login</button>
      </form>
      <p class="hint">Classroom demo. Use sample credentials only.</p>
    </section>
  </main>
  <footer>AirAsia Rewards &middot; Student demonstration website</footer>
</body>
</html>
