<?php
include '../multilingue/translations.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = array("username" => $username, "password" => $password);
    $data_string = json_encode($data);

    $ch = curl_init('http://localhost:8080/account/login');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));

    $result = curl_exec($ch);
    $response = json_decode($result, true);
    curl_close($ch);

    if ($response && isset($response['accessToken'])) {
        setcookie('Authorization', 'Bearer ' . $response['accessToken'], time() + 3600, '/');
        $_SESSION['username'] = $response['username'];
        $_SESSION['roles'] = $response['roles'];
        header('Location: ../public/index.php');
        exit;
    } else {
        $login_error = $translations['login_error'] ?? "Failed to login. Please check your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['login_title'] ?? 'Login - Au temps Donné'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-700 text-white">

<?php include '../components/header.php'; ?>

<div class="container mx-auto flex-grow flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $translations['login_title'] ?? 'Login'; ?></h2>
        <?php if (!empty($login_error)): ?>
            <div class="mb-4 text-red-500"><?php echo $login_error; ?></div>
        <?php endif; ?>
        
        <form id="loginForm" action="" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700"><?php echo $translations['Nom_utilisateur'] ?? 'Username'; ?></label>
                <input type="text" id="username" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700"><?php echo $translations['Mot_de_passe'] ?? 'Password'; ?></label>
                <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors duration-200"><?php echo $translations['Connecter'] ?? 'Log In'; ?></button>
        </form>
    </div>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
