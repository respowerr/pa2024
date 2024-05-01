<?php
    // Inclure les traductions
    include_once($_SERVER['DOCUMENT_ROOT'] . '/multilingue/translations.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Démarrer une session seulement si aucune session n'est active
    }
    $is_logged_in = isset($_COOKIE['Authorization']) && !empty($_COOKIE['Authorization']);  // Utilisez cette ligne si le token est stocké dans un cookie

    // Chargement des traductions
    $language = $_SESSION['language'] ?? 'fr';  // Utilisez une langue par défaut si non spécifiée
    $translations_file = $_SERVER['DOCUMENT_ROOT'] . "/multilingue/lang/$language.json";
    if (file_exists($translations_file) && is_readable($translations_file)) {
        $translations = json_decode(file_get_contents($translations_file), true);
    } else {
        $translations = [];  // Utilisez un tableau vide en cas d'échec du chargement pour éviter des erreurs
    }
?>

<!-- components/header.php -->
<header class="custom-header">
    <div class="header-container">
        <a href="/public/index.php">
            <img src="/assets/logo.jpeg" alt="Logo Au temps Donné" class="logo">
        </a>
        <nav class="header-nav">
            <?php if ($is_logged_in) : ?>
                <a href="/public/home_admin.php" class="custom-btn-admin"><?php echo $translations['Panel_admin'] ?? 'Panel Admin'; ?></a>
                <a href="/public/logout.php" class="custom-btn-logout"><?php echo $translations['Déconnexion'] ?? 'Déconnexion'; ?></a>
            <?php else : ?>
                <a href="/public/login.php" class="custom-btn-login"><?php echo $translations['Connexion'] ?? 'Connexion'; ?></a>
                <a href="/public/signup.php" class="custom-btn-signup"><?php echo $translations['Inscription'] ?? 'Inscription'; ?></a>
            <?php endif; ?>
            <ul>
                <?php
                    $json_data = @file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/multilingue/languages.json');  // Correction du chemin et ajout de @ pour gérer les erreurs
                    if ($json_data !== false) {
                        $available_languages = json_decode($json_data, true);
                        $current_language = $_GET['lang'] ?? 'fr';  // Utilisation d'une valeur par défaut si non spécifiée
                        echo '<li><select onchange="location = this.value;">';
                        echo "<option value='' selected>" . ($translations['choose_language'][$current_language] ?? 'Choisir langue') . "</option>";
                        foreach ($available_languages as $lang_code => $lang_name) {
                            $selected = ($current_language == $lang_code) ? 'selected' : '';
                            echo "<option value='?lang=$lang_code' $selected>$lang_name</option>";
                        }
                        echo '</select></li>';
                    } else {
                        echo "<li>Erreur de chargement des langues</li>";
                    }
                ?>
            </ul>
        </nav>
    </div>
</header>
<link rel="stylesheet" href="/styles/header.css">
