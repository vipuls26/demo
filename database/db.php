<?php

    $servername = "localhost";
    $username = "root";
    $password = "Root@123";
    $dbname = "demo";

    try {

        $connect = new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "connection successfully";

    } catch ( Exception $e ) {
        echo "Error while connecting to database" . $e->getMessage();
    }

?>