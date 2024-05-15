<?php
session_start();
if (!in_array('ROLE_ADMIN', $_SESSION['role'])) {
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

$allVehicles = makeHttpRequest($baseUrl . "/vehicle", "GET");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_plate'])) {
    $vehicleData = [
        'id_plate' => $_POST['id_plate'],
        'fret_capacity' => $_POST['fret_capacity'],
        'human_capacity' => $_POST['human_capacity'],
        'model' => $_POST['model']
    ];

    $result = makeHttpRequest($baseUrl . '/vehicle', 'POST', $vehicleData);
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        header('Location: vehicles.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Vehicles - HELIX";
    include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php');
    ?>
    <style>
        .table-container table {
            margin-top: 20px;
            border: 1px solid #ccc;
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th,
        .table-container td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table-container th {
            background-color: #f2f2f2;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh;
            margin-top: 20px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            width: 300px;
        }

        .form-container input,
        .form-container button {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/header.php') ?>
        <main>
            <div class="content">
                <div style="text-align: center;">
                    <h3 class="title is-3" style="margin-top: 10px;">Vehicle List</h3>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID Plate</th>
                                <th>Fret Capacity</th>
                                <th>Human Capacity</th>
                                <th>Model</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allVehicles as $vehicle) : ?>
                                <tr>
                                    <td><?= escape($vehicle['id']) ?></td>
                                    <td><?= escape($vehicle['id_plate']) ?></td>
                                    <td><?= escape($vehicle['fret_capacity']) ?></td>
                                    <td><?= escape($vehicle['human_capacity']) ?></td>
                                    <td><?= escape($vehicle['model']) ?></td>
                                    <td>
                                        <button onclick="editVehicle(<?= $vehicle['id'] ?>)">Edit</button>
                                        <button onclick="confirmDeleteProfile(<?= $vehicle['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-container">
                    <form action="" method="POST">
                        <input type="text" name="id_plate" placeholder="ID Plate" required>
                        <input type="number" name="fret_capacity" placeholder="Fret Capacity" required>
                        <input type="number" name="human_capacity" placeholder="Human Capacity" required>
                        <input type="text" name="model" placeholder="Model" required>
                        <button type="submit">Add Vehicle</button>
                    </form>
                    <?php if (isset($error)): ?>
                        <p style="color: red;"><?= escape($error) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php') ?>
    </div>

    <script>
        function editVehicle(vehicleId) {
            window.location.href = 'edit_vehicle.php?id=' + vehicleId;
        }

        function confirmDeleteProfile(vehicleId) {
            if (confirm('Are you sure you want to delete this vehicle?')) {
                deleteProfile(vehicleId);
            }
        }

        function deleteProfile(vehicleId) {
            const url = `http://ddns.callidos-mtf.fr:8080`;

            fetch('<?= $baseUrl ?>/vehicle/' + vehicleId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $_SESSION["accessToken"]; ?>'
                },
            })
            .then(response => {
                if (response.ok) {
                    alert('Vehicle deleted successfully.');
                    window.location.reload();
                } else {
                    console.error('Vehicle associated with an event impossible to delete', response.statusText);
                }
            })
            .catch(error => console.error('Error deleting vehicle:', error));
        }
    </script>
</body>

</html>
