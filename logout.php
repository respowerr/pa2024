<?php
// logout.php

// Déconnexion de l'utilisateur
session_start();
session_unset();
session_destroy();

// Redirection vers la page d'accueil
header("Location: index.php");
exit();
?>
