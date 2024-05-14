<?php 
session_start();
if(!in_array('ROLE_ADMIN', $_SESSION['role'])){
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

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$allEvents = makeHttpRequest($baseUrl . "/event", "GET");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
        $title = "Home - HELIX";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php');
    ?>    
</head>
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
  }
  .table-container th {
      background-color: #f2f2f2;
  }
</style>
<body>
    <div class="wrapper">
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/header.php') ?>
        <main>
            <div class="content">
                <img src="<?= '/assets/img/helix_white.png' ?>" alt="Helix_logo" width="600px" style="display: block; margin-left: auto; margin-right: auto; margin-top: 30px;">
                <div style="text-align: center;">
                    <h3 class="title is-3" style="margin-top: 10px;">Admin Panel</h3>
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
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>
</body>

</html>
