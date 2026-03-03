<?php

    session_start();

    if(!isset($_SESSION['email'])) {
        header("Location: ./login.php");
        exit();
    } elseif ( $_SESSION['role'] === "admin" ) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }

?>