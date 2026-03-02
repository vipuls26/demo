<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    require_once __DIR__ . ("/../database/db.php");

    $editrecord = $_GET['id'] ?? 0;

    function timestamp() {
        $dateTime = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
        return $dateTime->format('Y-m-d H:i:s');
    }

    try {

    

    $sql_select = "SELECT id, name, gender, email, password, role, address, interest, created_at, updated_at FROM users WHERE id = '" . $editrecord . "'";

        $result = $connect->query($sql_select);

        $name = $email = $password = $gender = $role = $interest = $address = null;
        $namevalidation = $emailvalidation = $passwordvalidation = $addressvalidation = $gendervalidation = $interestvalidation = $rolevalidation = null;
        $nameflag = $emailflag = $passwordflag = $genderflag = $roleflag = $interestflag = $addressflag = true;


        if (($_SERVER['REQUEST_METHOD'] == "POST") && $_POST['update']) {


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
                $gender = htmlspecialchars(trim($_POST['gender']));
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
                $interest = implode(" ", $_POST['interest']);
                $interestflag = true;
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

            $sql_select =  "SELECT email FROM users where email = :email";
            $stmt = $connect->prepare($sql_select);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $emailvalidation = "this is email already exist";
                
            } 
            $updated_at = timestamp();

            $editrecord = $_POST['id'];

            $sql_update = "UPDATE `users` SET 
                                    `name`='$name',
                                    `gender`='$gender',
                                    `email`='$email',
                                    `role`='$role',
                                    `address`='$address',
                                    `interest`='$interest',
                                    `updated_at`='$updated_at' 
                                    WHERE id = $editrecord";

                $connect->exec($sql_update);

                $_SESSION['msg'] = "<div class='alert alert-info' role='alert' id='alert'> record update successfully</div>";

                header("Location: ../admin/dashboard.php");
        }
    } catch (Exception $e) {
        echo "Error msg : " . $e->getMessage();
        echo $e->getCode();
        echo $e->getLine();
    } finally {

        $connect = null;
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

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- jquery validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
 
                            if ($result->rowCount() > 0) {
                                while ($row = $result->fetch()) {
                                    $interestarray = explode(" ", $row['interest']);

                            ?>

                              <input type="hidden" name="id" value="<?php echo isset($row['id']) ? $row['id'] : "" ?> ">


                                    <div class="col-12 col-md-6">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control border-secondary" id="name" placeholder="name"
                                            value="<?php echo isset($row['name']) ? $row['name'] : "" ?>">

                                        <div class="text-danger mt-2">
                                            <label id="name-error" class="error" for="name">
                                                <?php echo isset($namevalidation) ? $namevalidation : '' ?>
                                            </label>
                                        </div>

                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" class="form-control border-secondary" placeholder="email"
                                            value="<?php echo isset($row['email']) ? $row['email'] : "" ?>" id="email">

                                        <div class="text-danger mt-2">
                                            <label id="email-error" class="error" for="email">
                                                <?php echo isset($emailvalidation) ? $emailvalidation : '' ?>
                                            </label>
                                        </div>
                                    </div>


                                    <div class="col-12 col-md-6">

                                        <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                                        <br>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input border-dark" type="radio" id="male"
                                                value="male" <?= $row['gender'] == "male" ? 'checked' : '' ?>
                                                name="gender">
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>

                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input border-dark" type="radio" id="female"
                                                value="female" <?= $row['gender'] == "female" ? 'checked' : '' ?>
                                                name="gender">
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input border-dark" type="radio" id="other"
                                                value="other" <?= $row['gender'] == "other" ? 'checked' : '' ?>
                                                name="gender">
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="role" class="form-label">Role<span class="text-danger">*</span></label>
                                        <br>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input border-dark" type="radio" id="admin"
                                                value="admin" <?= $row['role'] == "admin" ? 'checked' : '' ?>
                                                name="role">
                                            <label class="form-check-label" for="inlineCheckbox1">Admin</label>
                                        </div>

                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input border-dark" type="radio" id="user"
                                                value="user" <?= $row['role'] == "user" ? 'checked' : '' ?>
                                                name="role">
                                            <label class="form-check-label" for="inlineCheckbox2">User</label>
                                        </div>


                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="interest" class="form-label">Interest<span class="text-danger">*</span></label>
                                        <br>

                                        <div class="form-check form-check-inline">


                                            <input class="form-check-input border-dark" type="checkbox" id="reading"
                                                value="reading"
                                                <?php echo (in_array("reading", $interestarray)) ? 'checked' : '' ?>
                                                name="interest[]">



                                            <label class="form-check-label" for="inlineCheckbox1">Reading</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input border-dark" type="checkbox" id="playing"
                                                value="playing" <?php echo (in_array("playing", $interestarray)) ? 'checked' : '' ?>
                                                name="interest[]">
                                            <label class="form-check-label" for="inlineCheckbox1">Playing</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input border-dark" type="checkbox" id="travelling"
                                                value="travelling" <?php echo (in_array("travelling", $interestarray)) ? 'checked' : '' ?>
                                                name="interest[]">
                                            <label class="form-check-label" for="inlineCheckbox1">Travelling</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input border-dark" type="checkbox" id="exploring"
                                                value="exploring" <?php echo (in_array("exploring", $interestarray)) ? 'checked' : '' ?>
                                                name="interest[]">
                                            <label class="form-check-label" for="inlineCheckbox1">Exploring</label>
                                        </div>

                                        <div class="text-danger mt-2">
                                            <label id="address-error" class="error" for="address">
                                                <?php echo isset($interestvalidation) ? $interestvalidation : '' ?>
                                            </label>
                                        </div>

                                    </div>

                                    <div class="col-12 col-md-6">

                                        <label for="address" class="form-label">Address<span class="text-danger">*</span></label><br>
                                        <textarea class="form-control" placeholder="address" name="address">
                                          <?php echo isset($row['address']) ? $row['address'] : "" ?>
                                        </textarea>

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
                            <?php
                                }
                            }
                            ?>

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
                        minlength: "name must be more than 1 character"
                    },
                    email: {
                        required: "email is required",
                        email: "please enter a valid email address",
                        // remote: "this email is already exist"
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
                    $("#registerform").submit();
                }
            });
        });
    </script>
</body>

</html>