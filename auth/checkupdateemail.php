<?php

    require_once __DIR__ . "/../database/db.php";

    $email = $_POST['email'];
    $id    = $_POST['id'];

    $sql = "SELECT id FROM users WHERE email = :email AND id != :id";
    $stmt = $connect->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':id'    => $id
    ]);

    if ($stmt->rowCount() > 0) {
        echo "false";  
    } else {
        echo "true";   }

    exit();
?>