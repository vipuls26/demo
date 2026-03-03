<nav class="navbar bg-dark">
    
        <div class="d-flex justify-content-end align-items-start text-light">
            <a href="
                <?php 
                    if($_SESSION['role'] === "admin") {
                        echo "../admin/dashboard.php";
                    } else {
                        echo "../user/dashboard.php";
                    }
                ?>
            " class="text-light text-decoration-none mx-4">Dashboard</a>
        </div>
       
        <a href="../auth/logout.php" class="btn btn-danger mx-4">Logout</a>
    </nav>
