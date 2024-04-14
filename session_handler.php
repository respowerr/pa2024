<?php
// session_handler.php

session_start();

if (isset($_GET['token'])) {
    // Stocker le jeton JWT dans la session PHP sous la clé 'Authorization'
    $_SESSION['Authorization'] = 'Bearer ' . $_GET['token'];
    
    // Rediriger vers la page d'accueil ou le tableau de bord
    header('Location: index.php');
    exit;
} else {
    // Gérer l'erreur si le jeton n'est pas fourni
    header('Location: login.php?error=notoken');
    exit;
}
