<?php
    include "config.php";

    session_start();

    $id = $_GET['id'];

    $sql = "SELECT * FROM friends WHERE fr_receiver_id='" . $id . "' OR fr_sender_id='" . $id . "'";
    $query = mysqli_query($con, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Friends List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="friends-container">
  <div class="header">Friends List</div>
<?php if(mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_assoc($query)) {
        $user_id = $row['fr_sender_id'] == $id ? $row['fr_receiver_id'] : $row['fr_sender_id'];
        $sql2 = "SELECT * FROM users WHERE id='" . $user_id . "'";
        $query2 = mysqli_query($con, $sql2);
        $user = mysqli_fetch_assoc($query2);
?>
  <div class="friend">
    <img src="<?php echo profile_image_url($user['profile_image'] ?? ''); ?>" class="avatar">
    <div class="info">
      <a class="name" href="profile.php?id=<?php echo $user['id'] ?>"><?php echo $user['fullname'] ?></a>
      <div class="status <?php echo $user["status"] ?>"><?php echo $user["status"] ?></div>
    </div>
  </div>
<?php } } else { ?>
  <div class="no-friends">You have no friends yet.</div>
  <?php } ?>
</div>

</body>
</html>