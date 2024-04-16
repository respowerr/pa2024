<?php
session_start();

unset($_SESSION['Authorization']);  

session_destroy(); 

setcookie('Authorization', '', time() - 3600, '/'); // Réglez la date d'expiration dans le passé

header('Location: /public/index.php');
exit;
?>
