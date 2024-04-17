<?php
session_start();

unset($_SESSION['Authorization']);  

session_destroy(); 

setcookie('Authorization', '', time() - 3600, '/'); 

header('Location: /public/index.php');
exit;
?>
