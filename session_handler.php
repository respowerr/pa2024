<?php

session_start();

if (isset($_GET['token'])) {
    $_SESSION['Authorization'] = 'Bearer ' . $_GET['token'];
    
    header('Location: index.php');
    exit;
} else {
    header('Location: login.php?error=notoken');
    exit;
}
