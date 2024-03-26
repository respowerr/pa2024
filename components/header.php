<?php
    include '../Multilingue/translations.php';
?>

<header class="custom-header">
    <div class="header-container">
        <a href="../index.php">
            <img src="../assets/logo.jpeg" alt="Logo Au temps Donné" class="logo">
        </a>
        <nav class="header-nav">
            <a href="../screens/login.php" class="custom-btn-login"><?php echo $translations['Connexion']; ?></a>
            <a href="../screens/signup.php" class="custom-btn-signup"><?php echo $translations['Inscription']; ?></a>
            <ul>
                <?php
                    // Charger les langues disponibles depuis un fichier JSON
                    $json_data = file_get_contents('../Multilingue/languages.json');
                    $available_languages = json_decode($json_data, true);

                    // Obtenir la langue actuellement sélectionnée (si elle est définie dans l'URL)
                    $current_language = isset($_GET['lang']) ? $_GET['lang'] : '';
                    
                    // Générer les options du menu déroulant
                    echo '<li><select onchange="location = this.value;">';
                    // Option vide
                    echo "<option value=''>" . $translations['choose_language'][$current_language] . "</option>";
                    // Options pour les autres langues
                    foreach ($available_languages as $lang_code => $lang_name) {
                        $selected = ($current_language == $lang_code) ? 'selected' : '';
                        echo "<option value='?lang=$lang_code' $selected>$lang_name</option>";
                    }
                    echo '</select></li>';
                ?>
                <!-- <li><a href="../Multilingue/adminLang.php"><?php echo $translations['Add_Language']; ?></a></li> -->
            </ul>
        </nav>
    </div>
</header>
<link rel="stylesheet" href="../styles/header.css">

