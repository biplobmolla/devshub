<?php
  include("config.php");
  session_start();

  if(isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
  }

  if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $query = mysqli_query($con, $sql);

    $row = mysqli_fetch_assoc($query);

    if(mysqli_num_rows($query) > 0) {
        echo "Login successful!";
        $_SESSION['user'] = $row;
        $_SESSION['username'] = $username;
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['user_id'] = $row['id'];
        header("Location: index.php");
    } else {
        echo "Invalid username or password.";
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
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" maxlength="64" autocomplete="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete="current-password" required />
      </div>
      <button name="login" type="submit" class="auth-submit">Log in</button>
    </form>
    <p class="auth-switch"><a href="register.php">Create an account</a></p>
  </div>
</body>
</html>
