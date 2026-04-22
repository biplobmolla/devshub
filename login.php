<?php
  include("config.php");
  session_start();

  if(isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
  }

  $errors = [];
  $username = '';

  if(isset($_POST['login'])){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if($username === '') {
      $errors[] = 'Username is required.';
    } elseif(strlen($username) > 64) {
      $errors[] = 'Username must be 64 characters or fewer.';
    }

    if($password === '') {
      $errors[] = 'Password is required.';
    }

    if(empty($errors)) {
      $safe_username = mysqli_real_escape_string($con, $username);
      $hashed_password = md5($password);
      $safe_password = mysqli_real_escape_string($con, $hashed_password);

      $sql = "SELECT * FROM users WHERE username='$safe_username' AND password='$safe_password'";
      $query = mysqli_query($con, $sql);

      if($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $_SESSION['user'] = $row;
        $_SESSION['username'] = $row['username'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['user_id'] = $row['id'];
        header("Location: index.php");
        exit();
      } else {
        $errors[] = 'Invalid username or password.';
      }
    }
  }
?>

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

    <?php if(!empty($errors)): ?>
      <div class="form-errors <?php echo count($errors) === 1 ? 'form-errors-single' : ''; ?>">
        <?php if(count($errors) > 1): ?>
          <span class="form-errors-title">Please fix the following:</span>
          <ul>
            <?php foreach($errors as $err): ?>
              <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <?php echo htmlspecialchars($errors[0]); ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" novalidate>
      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          name="username"
          id="username"
          maxlength="64"
          autocomplete="username"
          value="<?php echo htmlspecialchars($username); ?>"
          class="<?php echo !empty($errors) ? 'field-invalid' : ''; ?>"
        />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          name="password"
          id="password"
          autocomplete="current-password"
          class="<?php echo !empty($errors) ? 'field-invalid' : ''; ?>"
        />
      </div>
      <button name="login" type="submit" class="auth-submit">Log in</button>
    </form>
    <p class="auth-switch"><a href="register.php">Create an account</a></p>
  </div>
</body>
</html>
