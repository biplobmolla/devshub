<?php
    include "config.php";

    $notification_id = $_GET['notification_id'];

    $sql = "UPDATE notifications SET is_read=1 WHERE id='$notification_id'";

    if(mysqli_query($con, $sql)) {
      header("Location: profile.php?id=" . $_GET['sender_id']);
    }
?>