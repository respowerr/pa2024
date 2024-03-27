
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

$translations_file = "Multilingue/lang/$language.json";
$translations = json_decode(file_get_contents($translations_file), true);
?>
    
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Au temps Donné</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="script" href="script/script.js">
    
</head>
<body class="flex flex-col min-h-screen">

<header class="custom-header">
    <div class="header-container">
        <a href="index.php">
            <img src="assets/logo.jpeg" alt="Logo Au temps Donné" class="logo">
        </a>
        <nav class="header-nav">
            <a href="screens/login.php" class="custom-btn-login"><?php echo $translations['Connexion']; ?></a>
            <a href="screens/signup.php" class="custom-btn-signup"><?php echo $translations['Inscription']; ?></a>
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
        </nav>
    </div>
</header>


<section class="hero bg-gray-700 text-white py-20 flex-grow" style="background-image: url('chemin/vers/une/image.jpg'); background-size: cover; background-position: center;">
    <div class="container mx-auto text-center">
        <h2 class="text-5xl font-bold mb-4"><?php echo $translations['Bienvenue']; ?></h2>
        <p class="mb-8"><?php echo $translations['Rejoindre']; ?></p>
        <a href="#" class="bg-pink-600 py-3 px-6 rounded text-xl hover:bg-pink-500 transition-colors duration-200 shadow-lg"><?php echo $translations['Action']; ?></a>
    </div>
</section>

<footer class="custom-footer">
    © 2024 Au temps Donné. <?php echo $translations['Footer']; ?>
</footer>


<script src="/script/script.js"></script>

</body>
</html>
>