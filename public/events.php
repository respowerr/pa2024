<?php
session_start();
if (!in_array('ROLE_ADMIN', $_SESSION['role']) && !in_array('ROLE_USER', $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$baseUrl = "http://ddns.callidos-mtf.fr:8080";
$authHeader = "Authorization: Bearer " . $_SESSION['accessToken'];

function makeHttpRequest($url, $method, $data = null) {
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
        if ($data) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }
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

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function updateEvent($eventId, $data) {
    global $baseUrl;
    $url = $baseUrl . "/event/" . $eventId;
    return makeHttpRequest($url, "PUT", $data);
}

function deleteEvent($eventId) {
    global $baseUrl;
    $url = $baseUrl . "/event/" . $eventId;
    return makeHttpRequest($url, "DELETE");
}

function acceptEvent($eventId) {
    global $baseUrl;
    $url = $baseUrl . "/event/accept/" . $eventId;
    return makeHttpRequest($url, "POST");
}

$allEvents = makeHttpRequest($baseUrl . "/event", "GET");
$requestedEvents = makeHttpRequest($baseUrl . "/event/request", "GET");

error_log(print_r($requestedEvents, true));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
        $title = "Home - HELIX";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php');
    ?>    
    <style>
        .table-container table {
            margin-top: 20px;
            border: 1px solid #ccc;
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th, .table-container td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: white;
        }
        .table-container th {
            background-color: #333;
        }
        .table-container tr {
            background-color: black;
        }
        .actions button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/header.php') ?>
        <main>
            <div class="content">
                <img src="<?= '/assets/img/helix_white.png' ?>" alt="Helix_logo" width="600px" style="display: block; margin-left: auto; margin-right: auto; margin-top: 30px;">
                <div style="text-align: center;">
                    <h3 class="title is-3" style="margin-top: 10px;">Admin Panel</h3>
                </div>
                <div style="text-align: center;">
                <button onclick="redirectToCreateEvent()">Add Event</h3>
                </div>
                <div class="table-container">
                    <h3 class="title is-3" style="margin-top: 10px;">Events</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Event Type</th>
                                <th>Event Start</th>
                                <th>Event End</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Creator</th>
                                <th>Accepted</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allEvents as $event): ?>
                            <tr>
                                <td><?= escape($event['id']) ?></td>
                                <td><?= escape($event['eventName']) ?></td>
                                <td><?= escape($event['eventType']) ?></td>
                                <td><?= escape($event['eventStart']) ?></td>
                                <td><?= escape($event['eventEnd']) ?></td>
                                <td><?= escape($event['location']) ?></td>
                                <td><?= escape($event['description']) ?></td>
                                <td><?= escape($event['creator']) ?></td>
                                <td><?= escape($event['accepted']) ? 'Yes' : 'No' ?></td>
                                <td><?= escape($event['eventStartFormattedDate']) ?></td>
                                <td><?= escape($event['eventEndFormattedDate']) ?></td>
                                <td class="actions">
                                    <button onclick="redirectToEditEvent(<?= escape($event['id']) ?>)">Update</button>
                                    <button onclick="confirmDeleteEvent(<?= escape($event['id']) ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-container">
                    <h3 class="title is-3" style="margin-top: 10px;">Requested Events</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Event Type</th>
                                <th>Event Start</th>
                                <th>Event End</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Creator</th>
                                <th>Accepted</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($requestedEvents) && !empty($requestedEvents)) : ?>
                                <?php foreach ($requestedEvents as $event): ?>
                                    <?php if (is_array($event)) : ?>
                                    <tr>
                                        <td><?= escape($event['id']) ?></td>
                                        <td><?= escape($event['eventName']) ?></td>
                                        <td><?= escape($event['eventType']) ?></td>
                                        <td><?= escape($event['eventStart']) ?></td>
                                        <td><?= escape($event['eventEnd']) ?></td>
                                        <td><?= escape($event['location']) ?></td>
                                        <td><?= escape($event['description']) ?></td>
                                        <td><?= escape($event['creator']) ?></td>
                                        <td><?= escape($event['accepted']) ? 'Yes' : 'No' ?></td>
                                        <td><?= escape($event['eventStartFormattedDate']) ?></td>
                                        <td><?= escape($event['eventEndFormattedDate']) ?></td>
                                        <td class="actions">
                                            <button onclick="redirectToEditEvent(<?= escape($event['id']) ?>)">Update</button>
                                            <button onclick="confirmDeleteEvent(<?= escape($event['id']) ?>)">Delete</button>
                                            <button onclick="acceptEvent(<?= escape($event['id']) ?>)">Accept</button>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="12" style="text-align: center;">No requested events found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>

    <script>

        function redirectToCreateEvent(){
            window.location.href = 'create_event.php';

        }
        function redirectToEditEvent(eventId) {
            window.location.href = 'edit_event.php?id=' + eventId;
        }

        function confirmDeleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this event?')) {
                deleteEvent(eventId);
            }
        }

        function deleteEvent(eventId) {
            fetch('<?= $baseUrl ?>/event/' + eventId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $_SESSION["accessToken"]; ?>'
                },
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error('Erreur lors de la suppression de l\'événement:', response.statusText);
                }
            })
            .catch(error => console.error('Erreur lors de la suppression de l\'événement:', error));
        }

        function acceptEvent(eventId) {
            fetch('<?= $baseUrl ?>/event/accept/' + eventId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $_SESSION["accessToken"]; ?>'
                },
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error('Erreur lors de l\'acceptation de l\'événement:', response.statusText);
                }
            })
            .catch(error => console.error('Erreur lors de l\'acceptation de l\'événement:', error));
        }
    </script>
</body>
</html>
