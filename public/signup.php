<?php
include '../multilingue/translations.php';
include '../components/header.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $lastname = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $response = false;
    if ($name && $lastname && $username && $email && $phone && $password) {
        $response = true;
    }

    if ($response) {
        $message = "Inscription réussie! Bienvenue à Helix !";
    } else {
        $message = "Erreur lors de l'inscription. Veuillez réessayer.";
    }
}
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
</head>
<body class="flex flex-col min-h-screen bg-gray-700 text-white">

<div class="container mx-auto flex-grow flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $translations['Inscription']; ?></h2>
        <?php if ($message): ?>
            <p class="mb-4 text-green-500"><?php echo $message; ?></p>
        <?php endif; ?>
        <form id="registrationForm" action="" method="POST">
    <div class="mb-4">
        <label for="name" class="block text-gray-700"><?php echo $translations['Prénom']; ?></label>
        <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black bg-white" required>
    </div>
    <div class="mb-4">
        <label for="lastname" class="block text-gray-700"><?php echo $translations['Nom_de_famille']; ?></label>
        <input type="text" id="lastName" name="lastName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black bg-white" required>
    </div>
    <div class="mb-4">
        <label for="username" class="block text-gray-700"><?php echo $translations['Nom_utilisateur']; ?></label>
        <input type="text" id="username" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black bg-white" required>
    </div>
    <div class="mb-4">
        <label for="email" class="block text-gray-700"><?php echo $translations['Email']; ?></label>
        <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black bg-white" required>
    </div>
    <div class="mb-4">
        <label for="phone" class="block text-gray-700"><?php echo $translations['Téléphone']; ?></label>
        <input type="tel" id="phone" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black bg-white" required>
    </div>
    <div class="mb-4">
        <label for="password" class="block text-gray-700"><?php echo $translations['Mot_de_passe']; ?></label>
        <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black bg-white" required>
    </div>
    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors duration-200"><?php echo $translations['Inscrire']; ?></button>
</form>
    </div>
</div>

<?php include '../components/footer.php'; ?>
</body>
</html>
