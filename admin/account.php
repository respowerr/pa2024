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

// Fonction pour traiter les valeurs possiblement null avant de les échapper
function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$parityData = makeHttpRequest($baseUrl . "/account/parity", "GET");
$allAccounts = makeHttpRequest($baseUrl . "/account/all", "GET");
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
  .chart-container {
      height: 300px;
      width: 300px;
  }
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
                <div class="chart-container">
                    <canvas id="myChart2" class="chart"></canvas>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Role</th>
                                <th>Sex</th>
                                <th>Last Login</th>
                                <th>Registered Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allAccounts as $account): ?>
                            <tr>
                                <td><?= escape($account['id']) ?></td>
                                <td><?= escape($account['username']) ?></td>
                                <td><?= escape($account['name']) ?></td>
                                <td><?= escape($account['lastName']) ?></td>
                                <td><?= escape($account['email']) ?></td>
                                <td><?= escape($account['phone']) ?></td>
                                <td><?= escape($account['location']) ?></td>
                                <td><?= escape($account['role']) ?></td>
                                <td><?= escape($account['sex']) ?></td>
                                <td><?= escape($account['last_login']) ?></td>
                                <td><?= escape($account['register_date']) ?></td>
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