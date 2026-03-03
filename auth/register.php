<?php

    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (!isset($_SESSION['email'])) {
        header("Location: ../auth/logout.php");
    } elseif ($_SESSION['role'] === "user") {
        header("Location: ../user/dashboard.php");
    } 
    require_once __DIR__ . ("/../database/db.php");

    $name = $email = $password = $gender = $role = $interest = $address = null;
    $namevalidation = $emailvalidation = $passwordvalidation = $addressvalidation = $gendervalidation = $interestvalidation = $rolevalidation = null;
    $nameflag = $emailflag = $passwordflag = $genderflag = $roleflag = $interestflag = $addressflag = true;

    function timestamp() {
        $dateTime = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
        return $dateTime->format('Y-m-d H:i:s');
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['register'])) {

        // name 
        if (empty($_POST['name'])) {
            $namevalidation = "name is required";
            $nameflag = false;
        } elseif (strlen($_POST['name']) < 2) {
            $namevalidation = "name must be more than 1 character";
            $nameflag = false;
        } else {
            $name = htmlspecialchars(trim($_POST['name']));
        }

        // email 
        if (empty($_POST['email'])) {
            $emailvalidation = "email is required";
            $emailflag = false;
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $emailvalidation = "please enter a valid email address";
            $emailflag = false;
        } else {
            $email = htmlspecialchars(trim($_POST['email']));
        }

        // password
        if (empty($_POST['password'])) {
            $passwordvalidation = "password is required";
            $passwordflag = false;
        } elseif (strlen($_POST['password']) < 5) {
            $passwordvalidation = "password at least 5 characters";
            $passwordflag = false;
        } else {
            $password = htmlspecialchars(trim($_POST['password']));
        }

        // gender 
        if (empty($_POST['gender'])) {
            $gendervalidation = "gender is required";
            $genderflag = false;
        } else {
            $gender = $_POST['gender'];
        }

        // role
        if (empty($_POST['role'])) {
            $rolevalidation = "role is required";
            $roleflag = false;
        } else {
            $role = htmlspecialchars(trim($_POST['role']));
        }

        // interest 
        if (empty($_POST['interest'])) {
            $interestvalidation = "interest filed is required";
            $interestflag = false;
        } else {
            $interest = implode(",", $_POST['interest']);
            $interestflag = true;
            $interest;
        }

        // address
        if (empty($_POST['address'])) {
            $addressvalidation = "address is required";
            $addressflag = false;
        } elseif (strlen($_POST['address']) < 5) {
            $addressvalidation = "address required at least 5 characters";
            $addressflag = false;
        } else {
            $address = htmlspecialchars(trim($_POST['address']));
        }

        if ($nameflag && $emailflag && $passwordflag && $genderflag && $roleflag && $addressflag) {
            try {
                $sql_select =  "SELECT email FROM users where email = :email";
                $stmt = $connect->prepare($sql_select);
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $emailvalidation = "this email already exist";
                } else {

                    $created_at = timestamp();
                    $passwordHash = password_hash((string)$password, PASSWORD_DEFAULT);

                    $sql_insert = "INSERT INTO users 
                            (name, gender, email, password, role, address, interest, created_at) 
                            VALUES (:name, :gender, :email, :password, :role, :address, :interest, :created_at)";

                    $stmt = $connect->prepare($sql_insert);

                    $stmt->execute([
                        ':name' => $name,
                        ':gender' => $gender,
                        ':email' => $email,
                        ':password' => $passwordHash,
                        ':role' => $role,
                        ':address' => $address,
                        ':interest' => $interest,
                        ':created_at' => $created_at
                    ]);

                    //$_SESSION['msg'] = "<div class='alert alert-success' role='alert' id='alert'>record added successfully</div>";
                     $_SESSION['toast'] = "<div class='toast show' role='alert' id='toastmsg' aria-live='assertive' aria-atomic='true'>
                            <div class='toast-header bg-success'>
                                <strong class='me-auto'>Notification</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>
                            </div>
                            <div class='toast-body bg-success'>
                                user add successfully
                            </div>
                    </div>";
                    header("Location: ../admin/dashboard.php");
                }
            } catch (Exception $e) {
                echo "Error occur while inserting data " . $e->getMessage();
            } finally {
                $stmt = null;
                $connect = null;
            }
        }
    }




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

<body>

    <?php require_once __DIR__ . ("/../utility/header.php") ?>

    <div class="container-fluid">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card mt-5 w-75">
                <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" id="registerform">
                    <div class="card-title text-center mt-3">Registration form</div>

                    <div class="card-body">
                        <div class="row g-4">
                            <!-- name -->
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control border-secondary" id="name" placeholder="name"
                                    value="<?php echo isset($_POST['name']) ? $_POST['name'] : "" ?>">

                                <div class="text-danger mt-2">
                                    <label id="name-error" class="error" for="name">
                                        <?php echo isset($namevalidation) ? $namevalidation : '' ?>
                                    </label>
                                </div>

                            </div>

                            <!-- email -->
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" class="form-control border-secondary" placeholder="email"
                                    value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" id="email">

                                <div class="text-danger mt-2">
                                    <label id="email-error" class="error" for="email">
                                        <?php echo isset($emailvalidation) ? $emailvalidation : '' ?>
                                    </label>
                                </div>
                            </div>

                            <!-- password -->
                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control border-secondary" placeholder="password"
                                    value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>">
                                <i class="toggle-password fa fa-fw fa-eye"></i>

                                <div class="text-danger mt-2">
                                    <label id="password-error" class="error" for="password">
                                        <?php echo isset($passwordvalidation) ? $passwordvalidation : '' ?>
                                    </label>
                                </div>

                            </div>

                            <!-- gender -->
                            <div class="col-12 col-md-6">
                                <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="radio" id="male"
                                        value="male" <?php
                                                        if (isset($_POST['gender'])) {
                                                            echo $_POST['gender'] == "male" ? 'checked' : '';
                                                        }
                                                        ?>
                                        name="gender"
                                        checked>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>

                                <div class="form-check form-check-inline ">
                                    <input class="form-check-input border-dark" type="radio" id="female"
                                        value="female" <?php
                                                        if (isset($_POST['gender'])) {
                                                            echo $_POST['gender'] == "female" ? 'checked' : '';
                                                        }
                                                        ?>
                                        name="gender">
                                    <label class="form-check-label" for="female">Female</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="radio" id="other"
                                        value="other" <?php
                                                        if (isset($_POST['gender'])) {
                                                            echo $_POST['gender'] == "other" ? 'checked' : '';
                                                        }
                                                        ?>
                                        name="gender">
                                    <label class="form-check-label" for="other">Other</label>
                                </div>
                            </div>

                            <!-- role -->
                            <div class="col-12 col-md-6">
                                <label for="role" class="form-label">Role<span class="text-danger">*</span></label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="radio" id="admin"
                                        value="admin"
                                        <?php
                                        if (isset($_POST['role'])) {
                                            echo $_POST['role'] == "admin" ? 'checked' : '';
                                        }
                                        ?>
                                        name="role" checked>
                                    <label class="form-check-label" for="admin">Admin</label>
                                </div>

                                <div class="form-check form-check-inline ">
                                    <input class="form-check-input border-dark" type="radio" id="user"
                                        value="user"
                                        <?php
                                        if (isset($_POST['role'])) {
                                            echo $_POST['role'] == "user" ? 'checked' : '';
                                        }
                                        ?>
                                        name="role">
                                    <label class="form-check-label" for="user">User</label>
                                </div>
                            </div>

                            <!-- interest -->
                            <div class="col-12 col-md-6">
                                <label for="interest" class="form-label">Interest</label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="checkbox" id="reading"
                                        value="reading"
                                        <?php
                                        if (isset($_POST['interest'])) {
                                            echo (in_array("reading", $_POST['interest'])) ? 'checked' : '';
                                        }
                                        ?>
                                        name="interest[]">
                                    <label class="form-check-label" for="reading">Reading</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="checkbox" id="playing"
                                        value="playing"
                                        <?php
                                        if (isset($_POST['interest'])) {
                                            echo (in_array("playing", $_POST['interest'])) ? 'checked' : '';
                                        }
                                        ?>
                                        name="interest[]">
                                    <label class="form-check-label" for="playing">Playing</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="checkbox" id="travelling"
                                        value="travelling"
                                        <?php
                                        if (isset($_POST['interest'])) {
                                            echo (in_array("travelling", $_POST['interest'])) ? 'checked' : '';
                                        }
                                        ?>
                                        name="interest[]">
                                    <label class="form-check-label" for="travelling">Travelling</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-dark" type="checkbox" id="exploring"
                                        value="exploring"
                                        <?php
                                        if (isset($_POST['interest'])) {
                                            echo (in_array("exploring", $_POST['interest'])) ? 'checked' : '';
                                        }
                                        ?>
                                        name="interest[]">
                                    <label class="form-check-label" for="exploring">Exploring</label>
                                </div>
                            </div>

                            <!-- address -->
                            <div class="col-12">
                                <label for="address" class="form-label">Address<span class="text-danger">*</span></label><br>
                                <textarea class="form-control border-dark" name="address" placeholder="address"><?php echo isset($_POST['address']) ? $_POST['address'] : "" ?></textarea>

                                <div class="text-danger mt-2">
                                    <label id="address-error" class="error" for="address">
                                        <?php echo isset($addressvalidation) ? $addressvalidation : '' ?>
                                    </label>
                                </div>
                            </div>

                            <!-- register btn -->
                            <div class="col-12">
                                <div class="text-center">
                                    <input type="submit" class="btn btn-dark" id="register" name="register">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $("#registerform").validate({
                onkeypress: function(element) {
                    let validator = this;
                    setTimeout(() => {
                        validator.element(element);
                    }, 500);
                },

                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: './emailcheck.php',
                            type: "post",
                            data: {
                                email: function() {
                                    return $("#email").val();
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    address: {
                        required: true,
                        minlength: 5
                    },
                    interest: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "name is required",
                        minlength: "name at least 2 character"
                    },
                    email: {
                        required: "email is required",
                        email: "please enter a valid email address",
                        remote: "this email is already exist"
                    },
                    password: {
                        required: "password is required",
                        minlength: "password at least 5 characters"
                    },
                    address: {
                        required: "address is required",
                        minlength: "address required at least 5 characters"
                    },
                    interest: {
                        required: "interest filed is required"
                    }

                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                input = $(this).parent().find("input");
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        });
    </script>
</body>

</html>