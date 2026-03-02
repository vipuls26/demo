<?php

    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . ("/../database/db.php");

    $deleterecord = $_GET['id'];

    $sql_delete = "DELETE FROM `users` WHERE id = " . $deleterecord;
    $connect->query($sql_delete);
    $_SESSION['msg'] = "<div class='alert alert-danger' role='alert' id='alert'>record delete successfully</div>";
    header("Location: ./dashboard.php");
    exit();



?>