<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php
    $title = "Home - ATD";
    include_once('includes/head.php');
?>    
<link rel="stylesheet" href="<?='assets/css/index.css'?>">
</head>
<body>
    <div class="wrapper">
        <?php include_once('includes/header.php') ?>
        <main>
            <div class="content">
                <h1 class="title is-1" style="text-align: center;">Au Temps Donne</h1>
                <h2 class="subtitle is-5" style="text-align: center;">France's largest food aid and educational support association.</h2>
                <img src="<?='/assets/img/atd_logo.png'?>" alt="atd_principal_logo" id="principal_logo" class="image is-1by1">
            </div>
        </main>
        <?php include_once('includes/footer.php')?>
    </div>
</body>
</html>
