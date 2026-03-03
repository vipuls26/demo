<?php

    require_once __DIR__ . ("/../database/db.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    // var_dump($_SESSION);
    if (!isset($_SESSION['email'])) {
        header("Location: ../auth/logout.php");
    } elseif ($_SESSION['role'] !== "user") {
    header("Location: ../admin/dashboard.php");
        exit();
    }

    try {

        $sql_select = "SELECT * FROM users WHERE role = 'user' ORDER BY id;";
        $result = $connect->query($sql_select);
    } catch (Exception $e) {
        echo "Error occur while fetching data" . $e->getMessage();
    } finally {
        $stmt = null;
        $connect = null;
    }


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>user dashboard</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>

    <?php  require_once __DIR__ . ("/../utility/header.php") ?>


    <div class="container mt-5">
        <?php
        if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }

        ?>

        <p>welcome , <?php echo $_SESSION['name']; ?></p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Address</th>
                        <th>Interest</th>
                        <th>Created_at</th>
                        <th>Updated_at</th>

                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($result->rowCount() > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

                    ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td> <?= $row['name'] ?></td>
                                <td> <?= $row['gender'] ?></td>
                                <td> <?= $row['email'] ?></td>
                                <td> <?= $row['role'] ?></td>
                                <td> <?= $row['address'] ?></td>
                                <td> <?= $row['interest'] ?></td>
                                <td> <?= $row['created_at'] ?></td>
                                <td> <?= $row['updated_at'] ?? 'not update yet' ?> </td>
                            </tr>

                    <?php  }
                    } ?>
                </tbody>

            </table>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            setTimeout(() => {
                $(".alert").fadeOut("fast");
            }, 2000);

        });
    </script>
</body>

</html>