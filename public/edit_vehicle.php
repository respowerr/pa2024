<?php 
session_start();

if(!in_array('ROLE_ADMIN', $_SESSION['role'])){
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
    echo "ID du véhicule manquant.";
    exit;
}

$vehicleId = $_GET['id'];

$vehicleDetails = makeHttpRequest($baseUrl . "/vehicle/{$vehicleId}", "GET");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedVehicle = [
        "id_plate" => $_POST['id_plate'],
        "fret_capacity" => $_POST['fret_capacity'],
        "human_capacity" => $_POST['human_capacity'],
        "model" => $_POST['model']
    ];

    $jsonData = json_encode($updatedVehicle);

    $curl = curl_init($baseUrl . "/vehicle/{$vehicleId}");
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
        header("Location: vehicles.php");
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
        $title = "Modifier le véhicule - HELIX";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php');
    ?>    
    <style>
        .content {
            text-align: center;
            margin: auto;
            width: 60%;
        }

        #updateVehicleForm {
            margin-top: 20px;
        }

        #updateVehicleForm table {
            margin: auto;
        }

        #updateVehicleForm button {
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

        #updateVehicleForm button:hover {
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
                <h2>Modifier les informations du véhicule</h2>
                <form id="updateVehicleForm" method="POST">
                    <table>
                        <tr>
                            <td><label for="id_plate">ID Plate:</label></td>
                            <td><input type="text" id="id_plate" name="id_plate" value="<?= escape($vehicleDetails['id_plate'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="fret_capacity">Fret Capacity:</label></td>
                            <td><input type="number" id="fret_capacity" name="fret_capacity" value="<?= escape($vehicleDetails['fret_capacity'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="human_capacity">Human Capacity:</label></td>
                            <td><input type="number" id="human_capacity" name="human_capacity" value="<?= escape($vehicleDetails['human_capacity'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="model">Model:</label></td>
                            <td><input type="text" id="model" name="model" value="<?= escape($vehicleDetails['model'] ?? '') ?>" required></td>
                        </tr>
                    </table>
                    <button type="submit">Enregistrer les modifications</button>
                </form>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>

</body>

</html>
