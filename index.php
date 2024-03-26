<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Au temps Donné</title>
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
            <a href="screens/login.php" class="custom-btn-login">Connexion</a>
            <a href="screens/signup.php" class="custom-btn-signup">Inscription</a>
        </nav>
    </div>
</header>


<section class="hero bg-gray-700 text-white py-20 flex-grow" style="background-image: url('chemin/vers/une/image.jpg'); background-size: cover; background-position: center;">
    <div class="container mx-auto text-center">
        <h2 class="text-5xl font-bold mb-4">Bienvenue chez "Au temps Donné"!</h2>
        <p class="mb-8">Rejoignez-nous pour faire une différence dans la vie des personnes en besoin avec notre aide alimentaire et notre soutien scolaire.</p>
        <a href="#" class="bg-pink-600 py-3 px-6 rounded text-xl hover:bg-pink-500 transition-colors duration-200 shadow-lg">Découvrez nos actions</a>
    </div>
</section>

<footer class="custom-footer">
    © 2024 Au temps Donné. Tous droits réservés.
</footer>


<script src="/script/script.js"></script>

</body>
</html>
