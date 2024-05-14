<?php
session_start();

if (!isset($_SESSION['role']) || !in_array('ROLE_ADMIN', $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$baseUrl = "http://ddns.callidos-mtf.fr:8080";
$authHeader = "Authorization: Bearer " . $_SESSION['accessToken'];

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
    } elseif ($method === "PUT") {
        $options[CURLOPT_CUSTOMREQUEST] = "PUT";
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    } elseif ($method === "DELETE") {
        $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
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

if (!isset($_GET['id'])) {
    echo "ID d'événement manquant.";
    exit;
}

$eventId = $_GET['id'];

$eventDetails = makeHttpRequest($baseUrl . "/event/{$eventId}", "GET");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedEvent = [
        "eventName" => $_POST['eventName'],
        "eventType" => $_POST['eventType'],
        "eventStart" => $_POST['eventStart'],
        "eventEnd" => $_POST['eventEnd'],
        "location" => $_POST['location'],
        "description" => $_POST['description']
    ];

    $jsonData = json_encode($updatedEvent);

    $curl = curl_init($baseUrl . "/event/{$eventId}");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData),
        $authHeader
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);

    if ($result !== false) {
        $responseData = json_decode($result, true);
        header("Location: events.php");
        exit;
        
    }else{
        echo "Une erreur s'est produite lors de la mise à jour de l'événement.";

    }


    curl_close($curl);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Modifier l'événement - HELIX";
    include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php');
    ?>

<style>
        .content {
            text-align: center;
            margin: auto;
            width: 60%;
        }

        #updateEventForm {
            margin-top: 20px;
        }

        #updateEventForm table {
            margin: auto;
        }

        #updateEventForm button {
            background-color: white;
            color: black;
            border: 2px solid #555555;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        #updateEventForm button:hover {
            background-color: #555555;
            color: white;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/header.php') ?>
        <main>
            <div class="content">
                <h2>Modifier l'événement</h2>
                <form id="updateEventForm" method="POST">
                    <table>
                        <tr>
                            <td><label for="eventName">Nom de l'événement:</label></td>
                            <td><input type="text" id="eventName" name="eventName" value="<?= escape($eventDetails['eventName'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="eventType">Type d'événement:</label></td>
                            <td><input type="text" id="eventType" name="eventType" value="<?= escape($eventDetails['eventType'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="eventStart">Date de début:</label></td>
                            <td><input type="datetime-local" id="eventStart" name="eventStart" value="<?= escape($eventDetails['eventStart'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="eventEnd">Date de fin:</label></td>
                            <td><input type="datetime-local" id="eventEnd" name="eventEnd" value="<?= escape($eventDetails['eventEnd'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="location">Lieu:</label></td>
                            <td><input type="text" id="location" name="location" value="<?= escape($eventDetails['location'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="description">Description:</label></td>
                            <td><textarea id="description" name="description" required><?= escape($eventDetails['description'] ?? '') ?></textarea></td>
                        </tr>
                    </table>
                    <button type="submit">Enregistrer les modifications</button>
                </form>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php') ?>
    </div>
</body>

</html>


