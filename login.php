<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Log in — DevsHub</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="auth-body">
  <div class="auth-box">
    <h1>DevsHub</h1>
    <h2>Log in</h2>
    <form>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" maxlength="64" autocomplete="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete="current-password" required />
      </div>
      <button type="submit" class="auth-submit">Log in</button>
    </form>
    <p class="auth-switch"><a href="register.php">Create an account</a></p>
  </div>
</body>
</html>
