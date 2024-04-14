<?php
    include '../multilingue/translations.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Au temps Donné</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-700 text-white">

<?php include '../components/header.php'; ?>

<div class="container mx-auto flex-grow flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-semibold mb-4">Login</h2>
        <div id="loginMessage" class="mb-4"></div>
        
        <form id="loginForm" action="#" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors duration-200">Connect</button>
        </form>
    </div>
</div>

<?php include '../components/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("loginForm");
    const loginMessage = document.getElementById("loginMessage");

    loginForm.onsubmit = async (e) => {
        e.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        const data = {
            username: username,
            password: password
        };

        try {
    const response = await fetch('http://localhost:8080/account/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    if (response.ok) {
    const jsonResponse = await response.json();
    // Vérifie si un jeton existe déjà
    const existingToken = localStorage.getItem('Authorization');
    if (existingToken) {
        console.log("Remplacement du jeton existant par le nouveau.");
    } else {
        console.log("Enregistrement du nouveau jeton.");
    }
    localStorage.setItem('Authorization', `Bearer ${jsonResponse.accessToken}`);
    window.location.href = 'http://localhost/PA2024/'; // Redirige vers cette URL en cas de succès
} else {
    loginMessage.textContent = "Failed to login. Please check your credentials.";
    loginMessage.className = "text-red-500";
}
} catch (error) {
    console.error('Erreur:', error);
}
    };
});
</script>

</body>
</html>
