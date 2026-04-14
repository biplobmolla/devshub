<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register — DevsHub</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="auth-body">
  <div class="auth-box">
    <h1>DevsHub</h1>
    <h2>Create account</h2>
    <form>
      <div class="form-group">
        <label for="first_name">First name</label>
        <input type="text" name="first_name" id="first_name" maxlength="120" autocomplete="given-name" required />
      </div>
      <div class="form-group">
        <label for="last_name">Last name</label>
        <input type="text" name="last_name" id="last_name" maxlength="120" autocomplete="family-name" required />
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" maxlength="64" autocomplete="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete="new-password" required />
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm password</label>
        <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" required />
      </div>
      <button type="submit" class="auth-submit">Register</button>
    </form>
    <p class="auth-switch"><a href="login.php">Already have an account? Log in</a></p>
  </div>
</body>
</html>
