<!-- translations.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $language = $_GET['lang'];
    $_SESSION['language'] = $language;
} else if (isset($_SESSION['language'])) {
    $language = $_SESSION['language'];
} else {
    $language = 'en'; // Langue par défaut
}

// Charger les traductions depuis le fichier JSON
$translations_file = "../Multilingue/lang/$language.json";
$translations = json_decode(file_get_contents($translations_file), true);
?>
