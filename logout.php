<?php
session_start();
unset($_SESSION['Authorization']);  // Supprimer le jeton de la session
header('Location: index.php');  // Rediriger vers la page d'accueil ou la page de connexion
exit;
?>
