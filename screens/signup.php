<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['titre_inscription']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

<?php include '../components/header.php'; ?>

<div class="container mx-auto flex-grow flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $translations['Inscription']; ?></h2>
        <form id="registrationForm" action="#" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700"><?php echo $translations['Inscription_nom']; ?></label>
                <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="mb-4">
                <label for="lastname" class="block text-gray-700">Nom de famille</label>
                <input type="text" id="lastname" name="lastname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700"><?php echo $translations['Mail']; ?></label>
                <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-gray-700">Téléphone</label>
                <input type="tel" id="phone" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="mb-4">
                <label for="postalAddress" class="block text-gray-700">Adresse postale</label>
                <input type="text" id="postalAddress" name="postalAddress" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors duration-200"><?php echo $translations['Inscrire']; ?></button>
        </form>
    </div>
</div>

<?php include '../components/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("registrationForm");
        form.onsubmit = async (e) => {
            e.preventDefault();

            const formData = {
                name: form.name.value,
                lastname: form.lastname.value,
                email: form.email.value,
                phone: form.phone.value,
                postalAddress: form.postalAddress.value,
            };

            try {
                const response = await fetch('account/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData),
                });

                if (response.ok) {
                    const jsonResponse = await response.json();
                    message de succès ou en redirigeant l'utilisateur
                    console.log(jsonResponse);
                    alert("Inscription réussie!");
                    window.location.href = "page_de_connexion_ou_autre.html";
                    } else {
                        console.error('Erreur lors de l'inscription. Veuillez réessayer.');
                        alert("Erreur lors de l'inscription. Veuillez réessayer.");
                    }
                    } catch (error) {
                    console.error('Erreur lors de l'envoi de la requête:', error);
                    alert("Erreur lors de l'envoi de la requête. Veuillez vérifier votre connexion.");
                    }
                    };
                    });
 </script>

    </body>
</html>