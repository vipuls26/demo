<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

    if(isset($_SESSION['email'])) {
        if($_SESSION['role'] == "admin") {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/dashboard.php");
        }
    }

require_once __DIR__ . ("/../database/db.php");

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $email = $password = null;
    $emailvalidation = $passwordvalidation = null;
    $emailflag = $passwordflag = true;


    if (empty($_POST['email'])) {
        $emailvalidation = "email is required";
        $emailflag = false;
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $emailvalidation = "please enter a valid email address";
        $emailflag = false;
    } else {
        $email = htmlspecialchars(trim($_POST['email']));
    }

    if (empty($_POST['password'])) {
        $passwordvalidation = "password is required";
        $passwordflag = false;
    } elseif (strlen($_POST['password']) < 5) {
        $passwordvalidation = "password at least 5 characters";
        $passwordflag = false;
    } else {
        $password = htmlspecialchars(trim($_POST['password']));
    }


    if($emailflag && $password) {
        
        $sql_select =  "SELECT name , email, password, role FROM users where email = :email";
        $stmt = $connect->prepare($sql_select);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // print_r($user);

       // print_r($user);

        if($user['email'] != $email) {
            $emailvalidation =  "email is wrong";
            
        } else {
            
            if($user && password_verify($password,$user['password'])) {
                $_SESSION['msg'] = "<div class='alert alert-success' role='alert' id='alert'>welcome " . $_SESSION['name'] . "</div>";
                if($user['role'] == "admin") {
                    header("Location: ../admin/dashboard.php");
                    exit();
                } else {
                    header("Location: ../user/dashboard.php");
                }
            } else {
                $passwordvalidation = "password is incorrect";
            }
        }


     
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- jquery validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

      <style>
        .toggle-password {
            float: right;
            cursor: pointer;
            margin-right: 15px;
            margin-top: -25px;
        }
    </style>

</head>
</head>

<body>

    <div class="container-fluid">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card mt-5 w-50">
                <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" id="loginform">
                    <div class="card-title text-center mt-3">Login form</div>

                    <div class="card-body  mx-auto">
                        <div class="row g-3">



                            <div class="col-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" class="form-control border-secondary" placeholder="email"
                                    value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>"
                                >

                                <div class="text-danger mt-2">
                                    <label id="email-error" class="error" for="email">
                                        <?php echo isset($emailvalidation) ? $emailvalidation : "" ?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control border-secondary" placeholder="password"
                                    value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>"
                                >
                                <i class="toggle-password fa fa-fw fa-eye"></i>

                                <div class="text-danger mt-2">
                                    <label id="password-error" class="error" for="password">
                                        <?php echo isset($passwordvalidation) ? $passwordvalidation : "" ?>
                                    </label>
                                </div>

                            </div>

                            <div class="col-12">
                                <div class="text-center">
                                    <input type="submit" class="btn btn-dark" value="login">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        // $(document).ready(function(){

        //     $("#loginform").validate({
        //         rules: {
        //             email : {
        //                 required : true,
        //                 email : true
        //             },
        //             password : {
        //                 required : true,
        //                 minlength : 5
        //             },

        //         },
        //         messages: {

        //             email : {
        //                 required : "email is required",
        //                 email : "please enter a valid email address"
        //             },
        //             password : {
        //                 required : "password is required",
        //                 minlength : "password at least 5 characters"
        //             },

        //         },



        //         submitHandler: function(form) {
        //             $("#loginform").submit();
        //         }
        //     });

         $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                input = $(this).parent().find("input");
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        // });
    </script>

</body>

</html>