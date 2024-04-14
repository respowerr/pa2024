<?php
    include '../multilingue/translations.php';
?>

<!-- components/header.php -->

<header class="custom-header">
    <div class="header-container">
        <a href="index.php">
            <img src="../assets/logo.jpeg" alt="Logo Au temps Donné" class="logo">
        </a>
        <nav class="header-nav">
            <?php if (isset($is_logged_in) && $is_logged_in) : ?>
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
                $json_data = @file_get_contents('multilingue/languages.json'); // Utilisation de @ pour éviter l'affichage d'une erreur
                if ($json_data !== false) {
                    $available_languages = json_decode($json_data, true);
                    $current_language = isset($_GET['lang']) ? $_GET['lang'] : '';
                    echo '<li><select onchange="location = this.value;" style="color: black;">';
                    echo "<option value='' style='color: black;'>" . $translations['choose_language'][$current_language] . "</option>";
                    foreach ($available_languages as $lang_code => $lang_name) {
                        $selected = ($current_language == $lang_code) ? 'selected' : '';
                        echo "<option value='?lang=$lang_code' $selected style='color: black;'>$lang_name</option>";
                    }
                    echo '</select></li>';
                }
            ?>
            </ul>
        </nav>
    </div>
</header>

<link rel="stylesheet" href="../styles/header.css">
<script>
    document.querySelector('.hamburger-menu').addEventListener('click', function() {
    document.querySelector('.header-nav').classList.toggle('active');
});
</script>