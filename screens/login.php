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
<body class="flex flex-col min-h-screen bg-gray-100">

<?php include '../components/header.php'; ?>

<div class="container mx-auto flex-grow flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $translations['Connexion']; ?></h2>
        <form action="#" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-700"><?php echo $translations['Mail']; ?></label>
                <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700"><?php echo $translations['MDP']; ?></label>
                <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors duration-200"><?php echo $translations['Connecter']; ?></button>
        </form>
    </div>
</div>

<?php include '../components/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.querySelector("form"); 
    loginForm.onsubmit = async (e) => {
        e.preventDefault(); 

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        const data = {
            email: email,
            password: password
        };

        try {
            const response = await fetch('account/login', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (response.ok) {
                const jsonResponse = await response.json();
                console.log(jsonResponse);
            } else {
                alert("Échec de la connexion. Veuillez vérifier vos identifiants.");
            }
        } catch (error) {
            console.error('Erreur lors de la connexion:', error);
            alert("Une erreur est survenue. Veuillez réessayer.");
        }
    };
});
</script>


</body>
</html>
