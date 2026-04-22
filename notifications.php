<?php
  include "config.php";
  include "./utils/timeAgo.php";

  session_start();

  $sql = "SELECT * FROM notifications WHERE notification_receiver_id='" . $_SESSION['user_id'] . "' ORDER BY created_at DESC";
  $query = mysqli_query($con, $sql);

  

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css" />
    <title>Notifications</title>
  </head>
  <body class="notification-body">
    <div class="container">
      <h2>Notifications</h2>

      <ul class="">
        <?php if(mysqli_num_rows($query) > 0) {
          while($row = mysqli_fetch_assoc($query)){
            $sql2 = "SELECT * FROM users WHERE id='" . $row['notification_sender_id'] . "'";
            $query2 = mysqli_query($con, $sql2);
            $sender_info = mysqli_fetch_assoc($query2);
          ?>
        <li class="notification <?php echo ($row['is_read'] == 0) ? 'unread' : ''; ?>">
          <a href="./notification_read.php?notification_id=<?php echo $row['id']; ?>&sender_id=<?php echo $row['notification_sender_id']; ?>">
            <div class="icon">🔔</div>
            <div class="content">
              <div class="title"><?php echo $sender_info['fullname']; ?></div>
              <div class="message"><?php echo $row['notification_message']; ?></div>
              <div class="time" title="<?php echo htmlspecialchars($row['created_at'] ?? ''); ?>"><?php echo timeAgo($row['created_at'] ?? '', $con); ?></div>
            </div></a
          >
        </li>
        <?php } }else{ echo "<p>No notifications found.</p>"; } ?>
      </ul>
    </div>
  </body>
</html>
