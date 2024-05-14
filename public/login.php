<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $data = array(
            "username" => $username,
            "password" => $password
        );

        $url = "http://ddns.callidos-mtf.fr:8080/account/login";
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
            echo "<p data-translate='login_failed'>Login failed.</p>";
        } else {
            $userData = json_decode($result, true);

            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['role'] = $userData['roles'];
            $_SESSION['accessToken'] = $userData['accessToken'];
            $_SESSION['tokenType'] = $userData['tokenType'];

            header("Location: myprofil.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $title = "Login - ATD";
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
                <img src="/assets/img/helix_white.png" alt="Helix_logo" width="600px" style="display: block; margin-left: auto; margin-right: auto; margin-top: 30px;">
                <section class="container is-max-desktop">
                    <form action="login.php" method="post">
                        <div class="field">
                            <label class="label" data-translate="username">Username</label>
                            <div class="control">
                                <input class="input" type="text" name="username" placeholder="Emperor Palpatine" required>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" data-translate="password">Password</label>
                            <div class="control">
                                <input class="input" type="password" name="password" id="password" placeholder="Your super password" required>
                            </div>
                        </div>
                        <div class="control">
                            <button type="submit" class="button is-info" id="btn" data-translate="log_in">Log in</button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>
</body>
</html>
