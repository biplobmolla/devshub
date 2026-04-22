<?php
    include 'config.php';

    $errors = [];
    $fname = '';
    $lname = '';
    $username = '';

    if(isset($_POST['register'])){
        $fname = trim($_POST['first_name'] ?? '');
        $lname = trim($_POST['last_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if($fname === '') {
            $errors[] = 'First name is required.';
        } elseif(strlen($fname) > 120) {
            $errors[] = 'First name must be 120 characters or fewer.';
        }

        if($lname === '') {
            $errors[] = 'Last name is required.';
        } elseif(strlen($lname) > 120) {
            $errors[] = 'Last name must be 120 characters or fewer.';
        }

        if($username === '') {
            $errors[] = 'Username is required.';
        } elseif(strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters long.';
        } elseif(strlen($username) > 64) {
            $errors[] = 'Username must be 64 characters or fewer.';
        } elseif(!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        }

        if($password === '') {
            $errors[] = 'Password is required.';
        } elseif(strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        } elseif(strlen($password) > 72) {
            $errors[] = 'Password must be 72 characters or fewer.';
        }

        if($confirm_password === '') {
            $errors[] = 'Please confirm your password.';
        } elseif($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }

        if(empty($errors)) {
            $safe_username = mysqli_real_escape_string($con, $username);
            $check_sql = "SELECT id FROM users WHERE username='$safe_username'";
            $check_query = mysqli_query($con, $check_sql);
            if($check_query && mysqli_num_rows($check_query) > 0) {
                $errors[] = 'This username is already taken. Please choose another.';
            }
        }

        if(empty($errors)) {
            $safe_fullname = mysqli_real_escape_string($con, "$fname $lname");
            $safe_username = mysqli_real_escape_string($con, $username);
            $hashed_password = md5($password);
            $safe_password = mysqli_real_escape_string($con, $hashed_password);

            $sql2 = "INSERT INTO users (fullname, username, password) VALUES ('$safe_fullname', '$safe_username', '$safe_password')";
            if(mysqli_query($con, $sql2)) {
                header("Location: login.php");
                exit();
            } else {
                $errors[] = 'Something went wrong while creating your account. Please try again.';
            }
        }
    }
?>


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
        <label for="first_name">First name</label>
        <input
          type="text"
          name="first_name"
          id="first_name"
          maxlength="120"
          autocomplete="given-name"
          value="<?php echo htmlspecialchars($fname); ?>"
        />
      </div>
      <div class="form-group">
        <label for="last_name">Last name</label>
        <input
          type="text"
          name="last_name"
          id="last_name"
          maxlength="120"
          autocomplete="family-name"
          value="<?php echo htmlspecialchars($lname); ?>"
        />
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          name="username"
          id="username"
          maxlength="64"
          autocomplete="username"
          value="<?php echo htmlspecialchars($username); ?>"
        />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          name="password"
          id="password"
          autocomplete="new-password"
        />
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm password</label>
        <input
          type="password"
          name="confirm_password"
          id="confirm_password"
          autocomplete="new-password"
        />
      </div>
      <button name="register" type="submit" class="auth-submit">Register</button>
    </form>
    <p class="auth-switch"><a href="login.php">Already have an account? Log in</a></p>
  </div>
</body>
</html>
