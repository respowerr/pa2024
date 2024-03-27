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
    $language = 'fr';
}

$translations_file = "../Multilingue/lang/$language.json";
$translations = json_decode(file_get_contents($translations_file), true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Au temps Donné</title>
    <link rel="stylesheet" href="../styles/admin_panel.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="admin-panel">
    <div class="content">
        <header>
            <li><a href="../Multilingue/adminLang.php"><?php echo $translations['Add_Language']; ?></a></li>
            <ul>
            <?php
                $json_data = file_get_contents('Multilingue/languages.json');
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
            <button class="menu-toggle"><i class="fas fa-bars"></i></button>
            <div class="profile-btn"><i class="fas fa-user"></i><?php echo $translations['Profil']; ?></div> 
        </header>
        <div class="admin-content"><?php echo $translations['ContenuProfil']; ?></div>
    </div>
    <div class="sidebar">
        <ul>
            <li><a href="#"><?php echo $translations['Dashboard']; ?></a></li>
            <li><a href="#"><?php echo $translations['Utilisateurs']; ?></></li>
            <li><a href="#"><?php echo $translations['Messages']; ?></a></li>
            <li><a href="#"><?php echo $translations['Paramètres']; ?></a></li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');


    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        content.classList.add('collapsed');
    }

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
        menuToggle.style.display = 'block'; 
    });
});
</script>

</body>
</html>
