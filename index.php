<?php
// index.php

session_start(); // Assurez-vous que la session est démarrée

// Vérification de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification de la langue
if (isset($_GET['lang'])) {
    $language = $_GET['lang'];
    $_SESSION['language'] = $language;
} else if (isset($_SESSION['language'])) {
    $language = $_SESSION['language'];
} else {
    $language = 'fr'; 
}

// Inclusion des traductions
$translations_file = "multilingue/lang/$language.json";
$translations = json_decode(file_get_contents($translations_file), true);

// Vérification de la connexion en utilisant le jeton JWT
$is_logged_in = isset($_SESSION['Authorization']) && !empty($_SESSION['Authorization']); // Vous devrez ajuster cette condition pour valider le jeton

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Au temps Donné</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <script src="script/script.js"></script>
</head>
<body class="flex flex-col min-h-screen">

<header class="custom-header">
    <div class="header-container">
        <a href="index.php">
            <img src="assets/logo.jpeg" alt="Logo Au temps Donné" class="logo">
        </a>
        <nav class="header-nav">
            <?php if ($is_logged_in) : ?>
                <!-- L'utilisateur est connecté, afficher les liens du panel admin et de déconnexion -->
                <a href="admin_panel.php" class="custom-btn-admin"><?php echo $translations['Panel_admin']; ?></a>
                <a href="logout.php" class="custom-btn-logout"><?php echo $translations['Déconnexion']; ?></a>
            <?php else : ?>
                <!-- L'utilisateur n'est pas connecté, afficher les liens de connexion et d'inscription -->
                <a href="screens/login.php" class="custom-btn-login"><?php echo $translations['Connexion']; ?></a>
                <a href="screens/signup.php" class="custom-btn-signup"><?php echo $translations['Inscription']; ?></a>
            <?php endif; ?>
            <ul>
            <?php
                $json_data = file_get_contents('multilingue/languages.json');
                $available_languages = json_decode($json_data, true);
                $current_language = isset($_GET['lang']) ? $_GET['lang'] : '';
                echo '<li><select onchange="location = this.value;" style="color: black;">';
                echo "<option value='' style='color: black;'>" . $translations['choose_language'][$current_language] . "</option>";
                foreach ($available_languages as $lang_code => $lang_name) {
                    $selected = ($current_language == $lang_code) ? 'selected' : '';
                    echo "<option value='?lang=$lang_code' $selected style='color: black;'>$lang_name</option>";
                }
                echo '</select></li>';
            ?>
            </ul>
        </nav>
    </div>
</header>

<section class="hero bg-gray-700 text-white py-20 flex-grow" style="background-size: cover; background-position: center;">
    <div class="container mx-auto text-center">
        <h2 class="text-5xl font-bold mb-4"><?php echo $translations['Bienvenue']; ?></h2>
        <p class="mb-8"><?php echo $translations['Rejoindre']; ?></p>
        <a href="#" class="bg-pink-600 py-3 px-6 rounded text-xl hover:bg-pink-500 transition-colors duration-200 shadow-lg"><?php echo $translations['Action']; ?></a>
    </div>
</section>

<footer class="custom-footer">
    © 2024 Au temps Donné. <?php echo $translations['Footer']; ?>
</footer>

</body>
</html>
