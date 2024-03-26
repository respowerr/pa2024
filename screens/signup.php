<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Au temps Donné</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

<?php include '../components/header.php'; ?>

<div class="container mx-auto flex-grow flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-semibold mb-4">Inscription</h2>
        <form action="#" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nom complet :</label>
                <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Adresse email :</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Mot de passe :</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors duration-200">S'inscrire</button>
        </form>
    </div>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
