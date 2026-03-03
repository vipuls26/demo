<?php

    session_start();

    if (!isset($_SESSION['email'])) {
        header("Location: ../auth/logout.php");
    } elseif ($_SESSION['role'] !== "admin") {
        header("Location: ../user/dashboard.php");
        exit();
    }

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . ("/../database/db.php");

    $deleterecord = $_GET['id'];

    $stmt = $connect->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $deleterecord]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['role'] === "admin") {
        $_SESSION['toast'] = "<div class='toast show' role='alert' id='toastmsg' aria-live='assertive' aria-atomic='true'>
                            <div class='toast-header bg-danger'>
                                <strong class='me-auto'>Notification</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>
                            </div>
                            <div class='toast-body bg-danger'>
                                can not admin user
                            </div>
                        </div>";
        header("Location: ./dashboard.php");
        exit();
    } else {

        $sql_delete = "DELETE FROM `users` WHERE id = " . $deleterecord;
        $connect->query($sql_delete);

        //$_SESSION['msg'] = "<div class='alert alert-danger' role='alert' id='alert'>record delete successfully</div>";
        $_SESSION['toast'] = "<div class='toast show' role='alert' id='toastmsg' aria-live='assertive' aria-atomic='true'>
                            <div class='toast-header bg-danger'>
                                <strong class='me-auto'>Notification</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>
                            </div>
                            <div class='toast-body bg-danger'>
                                user delete successfully
                            </div>
                        </div>";
        header("Location: ./dashboard.php");
        exit();
    }

?>