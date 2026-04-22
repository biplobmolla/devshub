<?php

    $host = "localhost";
    $username = "root"; 
    $password = ""; 
    $database = "devshub"; 

    $con = mysqli_connect($host, $username, $password, $database);

    if(!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    function profile_image_url($filename) {
        return !empty($filename) ? './uploads/profiles/' . $filename : './images/profile-icon.png';
    }

?>
