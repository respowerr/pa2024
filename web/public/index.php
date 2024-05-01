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

$translations_file = "../multilingue/lang/$language.json";
$translations = json_decode(file_get_contents($translations_file), true);

$is_logged_in = isset($_SESSION['Authorization']) && !empty($_SESSION['Authorization']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Au temps Donné</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles/header.css">
    <link rel="stylesheet" href="/styles/footer.css">
</head>
<body class="flex flex-col min-h-screen">

<?php include_once('../components/header.php'); ?>

<section class="hero bg-gray-700 text-white py-20 flex-grow" style="background-size: cover; background-position: center;">
    <div class="container mx-auto text-center">
        <h2 class="text-5xl font-bold mb-4"><?php echo $translations['Bienvenue']; ?></h2>
        <p class="mb-8"><?php echo $translations['Rejoindre']; ?></p>
        <a href="#" class="bg-pink-600 py-3 px-6 rounded text-xl hover:bg-pink-500 transition-colors duration-200 shadow-lg"><?php echo $translations['Action']; ?></a>
    </div>
</section>

<?php include_once('../components/footer.php'); ?>

</body>
</html>
