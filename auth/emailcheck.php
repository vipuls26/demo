<?php
    
    require_once __DIR__ . ("/../database/db.php");

    $email = $_POST['email'];

     $sql_select =  "SELECT email FROM users where email = :email";
        $stmt = $connect->prepare($sql_select);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "false";
        } else {
            echo "true";
        }

?>