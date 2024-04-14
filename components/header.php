<?php
    include '../multilingue/translations.php';
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Démarrer une session seulement si aucune session n'est active
    }
    $is_logged_in = isset($_SESSION['Authorization']) && !empty($_SESSION['Authorization']);
?>

<!-- components/header.php -->
<header class="custom-header">
    <div class="header-container">
        <a href="index.php">
            <img src="../assets/logo.jpeg" alt="Logo Au temps Donné" class="logo">
        </a>
        <nav class="header-nav">
            <?php if ($is_logged_in) : ?>
                <a href="admin_panel.php" class="custom-btn-admin"><?php echo $translations['Panel_admin']; ?></a>
                <a href="logout.php" class="custom-btn-logout"><?php echo $translations['Déconnexion']; ?></a>
            <?php else : ?>
                <a href="screens/login.php" class="custom-btn-login"><?php echo $translations['Connexion']; ?></a>
                <a href="screens/signup.php" class="custom-btn-signup"><?php echo $translations['Inscription']; ?></a>
            <?php endif; ?>
            <ul>
                <?php
                    $json_data = @file_get_contents('multilingue/languages.json');
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
