<?php
    include '../multilingue/translations.php';
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
                    $json_data = file_get_contents('../multilingue/languages.json');
                    $available_languages = json_decode($json_data, true);
                    $current_language = isset($_GET['lang']) ? $_GET['lang'] : '';
                    echo '<li><select name="language" id="language-selector" onchange="location = this.value;">';
                    echo "<option value='' disabled selected>" . $translations['choose_language'][$current_language] . "</option>";
                    foreach ($available_languages as $lang_code => $lang_name) {
                        $selected = ($current_language == $lang_code) ? 'selected' : '';
                        echo "<option value='?lang=$lang_code' $selected>$lang_name</option>";
                    }
                    echo '</select></li>';
                ?>
                <!-- <li><a href="../multilingue/adminLang.php"><?php echo $translations['Add_Language']; ?></a></li> -->
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