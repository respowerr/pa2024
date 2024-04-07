<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $language = $_GET['lang'];
} else {
    $language = 'en';
}

$translations_file = "../multilingue/lang/$language.json";
$translations = json_decode(file_get_contents($translations_file), true);
?>
