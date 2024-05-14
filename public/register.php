<?php
    include_once('maintenance_check.php');
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
            echo "<p data-translate='registration_failed'>Registration failed.</p>";
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
    <script src="/assets/js/translation.js"></script>
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
                        <label class="label" data-translate="username">Username</label>
                        <div class="control">
                            <input class="input" type="text" name="username" placeholder="Emperor Palpatine" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="email">Email</label>
                        <div class="control">
                            <input class="input" type="email" name="email" placeholder="okenobi@jeditemple.com" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="phone">Phone</label>
                        <div class="control">
                            <input class="input" type="tel" name="phone" placeholder="Your super number" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="name">Name</label>
                        <div class="control">
                            <input class="input" type="text" name="name" placeholder="Your beautiful name" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="last_name">Last name</label>
                        <div class="control">
                            <input class="input" type="text" name="lastName" placeholder="Your beautiful last name" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="postal_address">Postal address</label>
                        <div class="control">
                            <input class="input" type="text" name="location" placeholder="Your amazing address" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="password">Password</label>
                        <div class="control">
                            <input class="input" type="password" name="password" id="password" placeholder="Your super password" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <input class="input" type="password" name="confpassword" id="confpassword" placeholder="Confirm password" required>
                            <p class="help is-danger" id="password-error" style="display:none; color:red; margin-top: 10px;" data-translate="password_mismatch">Passwords do not match</p>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" data-translate="gender">Your gender</label>
                        <div class="control">
                            <div class="select">
                                <select name="gender">
                                    <option value="M" data-translate="man">Man</option>
                                    <option value="F" data-translate="woman">Woman</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="control">
                        <button type="submit" class="button is-info" id="btn" data-translate="join_us">Join us</button>
                    </div>
                </form>
            </section>

            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>
</body>
</html>
