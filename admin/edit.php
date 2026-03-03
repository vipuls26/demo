<?php

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
    require_once __DIR__ . "/../database/db.php";


    function timestamp()
    {
        $dateTime = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
        return $dateTime->format('Y-m-d H:i:s');
    }


    $editrecord = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $editrecord = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    } else {
        $editrecord = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    }

    if ($editrecord <= 0) {
        die("Invalid ID");
    }


    $stmt = $connect->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $editrecord]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    $name = $email = $password = $gender = $role = $interest = $address = null;
    $namevalidation = $emailvalidation = $passwordvalidation = $addressvalidation = $gendervalidation = $rolevalidation = null;
    $nameflag = $emailflag = $passwordflag = $genderflag = $roleflag = $interestflag = $addressflag = true;
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['update'])) {

        $id = (int)$_POST['id'];


        if (empty($_POST['name'])) {
            $namevalidation = "name is required";
            $nameflag = false;
        } elseif (strlen($_POST['name']) < 2) {
            $namevalidation = "name must be more than 1 character";
            $nameflag = false;
        } else {
            $name = htmlspecialchars(trim($_POST['name']));
            $nameflag = true;
        }

        if (empty($_POST['email'])) {
            $emailvalidation = "email is required";
            $emailflag = false;
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $emailvalidation = "please enter a valid email address";
            $emailflag = false;
        } else {
            $email = htmlspecialchars(trim($_POST['email']));
            $emailflag = true;
        }

        if (empty($_POST['gender'])) {
            $gendervalidation = "gender is required";
            $genderflag = false;
        } else {
            $gender = $_POST['gender'];
            $genderflag = true;
        }

        if (empty($_POST['role'])) {
            $rolevalidation = "role is required";
            $roleflag = false;
        } else {
            $role = trim($_POST['role']);
            $roleflag = true;
        }


        $interest = isset($_POST['interest']) ? implode(",", $_POST['interest']) : '';
        $interestflag = true;

        // address
        if (empty($_POST['address'])) {
            $addressvalidation = "address is required";
            $addressflag = false;
        } elseif (strlen($_POST['address']) < 5) {
            $addressvalidation = "address required at least 5 characters";
            $addressflag = false;
        } else {
            $address = trim($_POST['address']);
            $addressflag = true;
        }





        $check = $connect->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $check->execute([
            ':email' => $email,
            ':id' => $id
        ]);

        if ($check->rowCount() > 0) {
            $emailvalidation  = "Email already exists";
        } else {
            if ($nameflag && $emailflag && $passwordflag && $genderflag && $roleflag && $interestflag && $addressflag) {
                $update = $connect->prepare("
                        UPDATE users SET
                            name = :name,
                            gender = :gender,
                            email = :email,
                            role = :role,
                            address = :address,
                            interest = :interest,
                            updated_at = :updated_at
                        WHERE id = :id
                    ");

                $update->execute([
                    ':name' => $name,
                    ':gender' => $gender,
                    ':email' => $email,
                    ':role' => $role,
                    ':address' => $address,
                    ':interest' => $interest,
                    ':updated_at' => timestamp(),
                    ':id' => $id
                ]);

                $_SESSION['msg'] = "<div class='alert alert-info' role='alert' id='alert'>record update successfully</div>";

                header("Location: dashboard.php");
                exit();
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- jquery validation -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
</head>

<body>

    <div class="container-fluid">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card mt-5 w-75">
                <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" id="registerform">
                    <div class="card-title text-center mt-3">update record</div>

                    <div class="card-body">
                        <div class="row g-4">

                            <?php
                            if ($user):
                                $interestarray = explode(",", $user['interest']);
                            ?>

                                <input type="hidden" id="user_id" name="id" value="<?php echo $user['id']; ?>">
                                
                                <!-- name -->
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control border-secondary" id="name" placeholder="name"
                                        value=" <?php echo isset($_POST['name']) ? trim($_POST['name']) : trim($user['name']); ?>">
                                       

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
                                        value="<?php echo isset($_POST['email']) ? $_POST['email'] : $user['email']; ?>"
                                        id="email">

                                    <div class="text-danger mt-2">
                                        <label id="email-error" class="error" for="email">
                                            <?php echo isset($emailvalidation) ? $emailvalidation : '' ?>
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
                                                                if(isset($_POST['gender'])) {
                                                                    echo $_POST['gender'] == "male" ? 'checked' : '';
                                                                } else {
                                                                    echo $user['gender'] == "male" ? 'checked' : '';
                                                                } 
                                                            ?>
                                            name="gender">
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>

                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input border-dark" type="radio" id="female"
                                            value="female" <?php 
                                                                if(isset($_POST['gender'])) {
                                                                    echo $_POST['gender'] == "female" ? 'checked' : '';
                                                                } else {
                                                                    echo $user['gender'] == "female" ? 'checked' : '';
                                                                } 
                                                            ?>
                                            name="gender">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input border-dark" type="radio" id="other"
                                            value="other" <?php 
                                                                if(isset($_POST['gender'])) {
                                                                    echo $_POST['gender'] == "other" ? 'checked' : '';
                                                                } else {
                                                                    echo $user['gender'] == "other" ? 'checked' : '';
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
                                            value="admin" <?php 
                                                            if(isset($_POST['role'])) {
                                                                echo $_POST['role'] == "admin" ? 'checked' : '';
                                                            } else {
                                                                echo $user['role'] == "admin" ? 'checked' : '';
                                                                } ?>
                                            name="role">
                                        <label class="form-check-label" for="admin">Admin</label>
                                    </div>

                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input border-dark" type="radio" id="user"
                                            value="user" <?php 
                                                            if(isset($_POST['role'])) {
                                                                echo $_POST['role'] == "user" ? 'checked' : '';
                                                            } else {
                                                                echo $user['role'] == "user" ? 'checked' : '';
                                                                } ?>
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
                                                    if(isset($_POST['interest'])) {
                                                        echo (in_array("reading",$_POST['interest'])) ? 'checked' : '';
                                                    } else {
                                                        echo (in_array("reading", $interestarray)) ? 'checked' : '';
                                                    } 
                                                ?>

                                            name="interest[]">
                                        <label class="form-check-label" for="reading">Reading</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input border-dark" type="checkbox" id="playing"
                                            value="playing"  
                                                <?php
                                                    if(isset($_POST['interest'])) {
                                                        echo (in_array("playing",$_POST['interest'])) ? 'checked' : '';
                                                    } else {
                                                        echo (in_array("playing", $interestarray)) ? 'checked' : '';
                                                    } 
                                                ?>
                                            name="interest[]">
                                        <label class="form-check-label" for="playing">Playing</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input border-dark" type="checkbox" id="travelling"
                                            value="travelling" 
                                             <?php
                                                    if(isset($_POST['interest'])) {
                                                        echo (in_array("travelling",$_POST['interest'])) ? 'checked' : '';
                                                    } else {
                                                        echo (in_array("travelling", $interestarray)) ? 'checked' : '';
                                                    } 
                                                ?>
                                            name="interest[]">
                                        <label class="form-check-label" for="travelling">Travelling</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input border-dark" type="checkbox" id="exploring"
                                            value="exploring" 
                                                     <?php
                                                    if(isset($_POST['interest'])) {
                                                        echo (in_array("exploring",$_POST['interest'])) ? 'checked' : '';
                                                    } else {
                                                        echo (in_array("exploring", $interestarray)) ? 'checked' : '';
                                                    } 
                                                ?>                                                 
                                            name="interest[]">
                                        <label class="form-check-label" for="exploring">Exploring</label>
                                    </div>
                                </div>

                                <!-- address -->
                                <div class="col-12 col-md-6">

                                    <label for="address" class="form-label">Address<span class="text-danger">*</span></label><br>
                                    <textarea class="form-control" name="address" placeholder="address"><?php echo isset($_POST['address']) ? $_POST['address'] : $user['address']; ?></textarea>

                                    <div class="text-danger mt-2">
                                        <label id="address-error" class="error" for="address">
                                            <?php echo isset($addressvalidation) ? $addressvalidation : '' ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="text-center">
                                        <input type="submit" class="btn btn-warning" id="update" value="update" name="update">
                                    </div>
                                </div>
                            <?php endif; ?>

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
                            url: '../auth/checkupdateemail.php',
                            type: "post",
                            data: {
                                email: function() {
                                    return $("#email").val();
                                },
                                id: function() {
                                    return $("#user_id").val();
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
                    }
                },
                messages: {
                    name: {
                        required: "name is required",
                        minlength: "name must be more than 1 character"
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
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>
</body>

</html>