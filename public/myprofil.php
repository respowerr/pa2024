<?php 
include_once('maintenance_check.php');

if (!isset($_SESSION['accessToken'])) {
    header("Location: login.php");
    exit();
}

$authHeader = "Authorization: Bearer " . $_SESSION['accessToken'];
$options = [
    "http" => [
        "method" => "GET",
        "header" => $authHeader
    ]
];
$context = stream_context_create($options);
$url = "http://ddns.callidos-mtf.fr:8080/account/me";

$response = file_get_contents($url, false, $context);
if ($response === FALSE) {
    echo "Failed to retrieve profile information.";
    exit;
}

$profileData = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $title = "My Profile - ATD";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/head.php');
    ?>    
</head>
<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
        <main>
            <div class="content">
                <div class="container">
                    <h1>Welcome, <?= htmlspecialchars($profileData['username']); ?>!</h1>
                    <p><strong>Name:</strong> <?= htmlspecialchars($profileData['name'] . " " . $profileData['lastName']); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($profileData['email']); ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($profileData['location']); ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($profileData['role']); ?></p>
                    <p><strong>Last Login:</strong> <?= htmlspecialchars($profileData['last_login']); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($profileData['phone']); ?></p>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>
    </div>
</body>
</html>
