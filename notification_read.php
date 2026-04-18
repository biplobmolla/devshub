<?php
    include "config.php";

    $notification_id = $_GET['notification_id'];

    $sql2 = "SELECT * FROM notifications WHERE id=$notification_id";
    $query2 = mysqli_query($con, $sql2);
    if(mysqli_num_rows($query2)){
      $row = mysqli_fetch_assoc($query2);
      if($row['notification_type'] == 'friend_request'){
        $sql = "UPDATE notifications SET is_read=1 WHERE id='$notification_id'";
    
        if(mysqli_query($con, $sql)) {
          header("Location: profile.php?id=" . $_GET['sender_id']);
        }
      }else if($row['notification_type'] == 'post'){
        $sql = "UPDATE notifications SET is_read=1 WHERE id='$notification_id'";
    
        if(mysqli_query($con, $sql)) {
          header("Location: index.php#" . $row['post_id']);
        }
      }
    }

?>