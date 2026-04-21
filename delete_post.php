<?php
    include 'config.php';

    $id = $_GET['id'];

    $sql = "DELETE FROM posts WHERE id = $id";
    mysqli_query($con, $sql);

    header("Location: index.php");
?>