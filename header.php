<!-- header.php -->
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['accueil']; ?></title>
</head>
<body>
<header>
    <nav>
    <ul>
        <?php
            // Charger les langues disponibles depuis un fichier JSON
            $json_data = file_get_contents('Multilingue/languages.json');
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
            <li><a href="Multilingue/adminLang.php">Add Language</a></li>
        </ul>
    </nav>
</header>
