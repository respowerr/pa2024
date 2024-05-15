<?php
session_start();
if (!isset($_SESSION['role']) || !in_array('ROLE_ADMIN', $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$baseUrl = "http://ddns.callidos-mtf.fr:8080";
$authHeader = "Authorization: Bearer " . $_SESSION['accessToken'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventData = [
        "eventName" => $_POST['eventName'],
        "eventType" => $_POST['eventType'],
        "eventStart" => $_POST['eventStart'],
        "eventEnd" => $_POST['eventEnd'],
        "location" => $_POST['location'],
        "description" => $_POST['description']
    ];

    $vehicleIds = $_POST['vehicleIds'];

    $requestData = [
        "event" => $eventData,
        "vehicleIds" => $vehicleIds
    ];

    $response = makeHttpRequest($baseUrl . "/event", "POST", $requestData);

    if ($response && isset($response['success']) && $response['success']) {
        header("Location: events.php");
        exit;
    } else {
        echo "Une erreur s'est produite lors de la création de l'événement.";
    }
}

function makeHttpRequest($url, $method, $data = null)
{
    global $authHeader;
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            $authHeader
        ]
    ];

    if ($method === "POST") {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    $curl = curl_init($url);
    curl_setopt_array($curl, $options);
    $result = curl_exec($curl);

    if ($result === false) {
        throw new Exception(curl_error($curl), curl_errno($curl));
    }

    curl_close($curl);

    return json_decode($result, true);
}

function escape($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$allEvents = makeHttpRequest($baseUrl . "/event", "GET");
$allVehicles = makeHttpRequest($baseUrl . "/vehicle", "GET");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Create Event - HELIX";
    include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php');
    ?>
</head>

<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/header.php') ?>
        <main>
            <div class="content">
                <h2>Create New Event</h2>
                <form method="POST">
                    <label for="eventName">Event Name:</label><br>
                    <input type="text" id="eventName" name="eventName" required><br><br>

                    <label for="eventType">Event Type:</label><br>
                    <input type="text" id="eventType" name="eventType" required><br><br>

                    <label for="eventStart">Event Start:</label><br>
                    <input type="datetime-local" id="eventStart" name="eventStart" required><br><br>

                    <label for="eventEnd">Event End:</label><br>
                    <input type="datetime-local" id="eventEnd" name="eventEnd" required><br><br>

                    <label for="location">Location:</label><br>
                    <input type="text" id="location" name="location" required><br><br>

                    <label for="description">Description:</label><br>
                    <textarea id="description" name="description" required></textarea><br><br>

                    <label for="vehicleIds">Select Vehicles:</label><br>
                    <select id="vehicleIds" name="vehicleIds[]" multiple required>
                        <?php foreach ($allVehicles as $vehicle) : ?>
                            <option value="<?= escape($vehicle['id']) ?>">
                                <?= escape($vehicle['id_plate']) ?> - <?= escape($vehicle['model']) ?> (Fret Capacity: <?= escape($vehicle['fret_capacity']) ?>, Human Capacity: <?= escape($vehicle['human_capacity']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <input type="submit" value="Create Event">
                </form>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php') ?>
    </div>
</body>

</html>
