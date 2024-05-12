<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $name = $_POST["name"];
        $lastName = $_POST["lastName"];
        $location = $_POST["location"];
        $password = $_POST["password"];
        $confpassword = $_POST["confpassword"];
        $gender = $_POST["gender"];

        $data = array(
            "username" => $username,
            "email" => $email,
            "phone" => $phone,
            "name" => $name,
            "lastName" => $lastName,
            "location" => $location,
            "password" => $password,
            "sex" => $gender
        );

        $url = "http://ddns.callidos-mtf.fr:8080/account/register";
        $options = array(
            "http" => array(
                "header" => "Content-type: application/json",
                "method" => "POST",
                "content" => json_encode($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            echo "Registration failed.";
        } else {
            header("Location: login.php");
            exit();
        }
    }
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $title = "Join us - ATD";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/head.php');
    ?>    
</head>
<style>
    .container{
        max-width: 500px;
        margin-top: 50px;
    }
    #btn{
        margin-top: 15px;
        display: block; margin-left: auto; margin-right: auto;
    }

</style>
<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php') ?>
        <main>
            <div class="content">
            <img src="<?= '/assets/img/helix_white.png' ?>" alt="Helix_logo" width="600px" style="display: block; margin-left: auto; margin-right: auto; margin-top: 30px;">
            
            <section class="container is-max-desktop">
                <form action="register.php" method="post" onsubmit="return checkPassword();">
                    <div class="field">
                        <label class="label">Username</label>
                        <div class="control">
                            <input class="input" type="text" name="username" placeholder="Emperor Palpatine" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input class="input" type="email" name="email" placeholder="okenobi@jeditemple.com" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Phone</label>
                        <div class="control">
                            <input class="input" type="tel" name="phone" placeholder="Your super number" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Name</label>
                        <div class="control">
                            <input class="input" type="text" name="name" placeholder="Your beautiful name" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Last name</label>
                        <div class="control">
                            <input class="input" type="text" name="lastName" placeholder="Your beautiful last name" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Postal address</label>
                        <div class="control">
                            <input class="input" type="text" name="location" placeholder="Your amazing address" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="control">
                            <input class="input" type="password" name="password" id="password" placeholder="Your super password" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <input class="input" type="password" name="confpassword" id="confpassword" placeholder="Confirm password" required>
                            <p class="help is-danger" id="password-error" style="display:none; color:red; margin-top: 10px;">Passwords do not match</p>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Your gender</label>
                        <div class="control">
                            <div class="select">
                                <select name="gender">
                                    <option value="M">Man</option>
                                    <option value="F">Women</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="control">
                        <button type="submit" class="button is-info" id="btn">Join us</button>
                    </div>
                </form>
            </section>

            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>
</body>
<script>
function checkPassword() {
    var password = document.getElementById("password").value;
    var confpassword = document.getElementById("confpassword").value;
    var passwordError = document.getElementById("password-error");

    if (password != confpassword) {
        passwordError.style.display = "block";
        return false;
    } else {
        passwordError.style.display = "none";
        return true;
    }
}
</script>
</html>
