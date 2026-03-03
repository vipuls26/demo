<?php

    require_once __DIR__ . ("/../database/db.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    if (!isset($_SESSION['email'])) {
        header("Location: ../auth/logout.php");
    } elseif ($_SESSION['role'] !== "admin") {
        header("Location: ../user/dashboard.php");
        exit();
    } 

    try {
        $sql_select = "SELECT * FROM `users` ORDER BY id";
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
    <title>Admin dashboard</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>


    <?php  require_once __DIR__ . ("/../utility/header.php") ?>

    <div class="container mt-2">

        <div class="d-flex justify-content-between">

            <p class="mt-2">welcome , <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : "" ?></p>
                <?php
                    if (isset($_SESSION['toast'])) {
                        echo $_SESSION['toast'];
                        unset($_SESSION['toast']);
                    }
                ?>
        </div>

        <a href="../auth/register.php" class="btn btn-info mb-3">Add user</a>

        <div class="table-responsive mt-2">
            <table class="table table-bordered border-secondary">
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
                        <th>Action</th>
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
                                <td> <?= $row['updated_at'] ?? 'not updated yet' ?></td>
                                <td>
                                    <a href="../admin/edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                    <a href="../admin/delete.php?id=<?= $row['id'] ?>" class="btn btn-danger mt-2 delete"> Delete</a>
                                </td>
                            </tr>

                    <?php  }
                    } 
                    else {
                        echo "<p class='fs-4 text-secondary text-center'>no user are in database</p>";
                    }
                    
                    ?>
                </tbody>

            </table>
        </div>

    </div>


    <script>
        $(document).ready(function() {

            setTimeout(() => {
                $(".alert").fadeOut("fast");
            }, 2000);

              setTimeout(() => {
                $("#toastmsg").fadeOut("fast");
            }, 2000);




            $(".delete").click(function(event) {
                if (!confirm("confirm to delete this blog")) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>