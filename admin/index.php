<?php 
session_start();

if (!isset($_SESSION['role']) || !in_array('ROLE_ADMIN', $_SESSION['role'])) {
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

$maintenance_mode = isset($_SESSION['maintenance_mode']) ? $_SESSION['maintenance_mode'] : false;

$title = "Home - HELIX";

include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/head.php'; ?>    
</head>
<style>
  .chart-container{
      height: 300px;
      width: 300px;
  }
</style>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<body>
    <div class="wrapper">
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/header.php'; ?>
        <main>
            <div class="content">
                <img src="<?= '/assets/img/helix_white.png' ?>" alt="Helix_logo" width="600px" style="display: block; margin-left: auto; margin-right: auto; margin-top: 30px;">
                <div style="text-align: center;">
                    <h3 class="title is-3" style="margin-top: 10px;">Admin Panel</h3>
                    <form method="post" action="../public/maintenance_check.php">
                        <label for="maintenance_mode_toggle">Maintenance Mode:</label>
                        <label class="switch">
                        <input type="checkbox" id="maintenance_mode_toggle" name="maintenance_mode_toggle" value="on" <?php if($maintenance_mode) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>                        
                        <input type="submit" value="Submit">
                    </form>

                </div>
            </div>
            <div class="chart-container">
                <canvas id="myChart2" class="chart"></canvas>
            </div>
            
        </main>
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
    </div>
    <script>
    const data2 = {
        labels: ['Men', 'Women'],
        datasets: [{
            label: 'Parity',
            data: [<?= $parityData['M'] ?? 0 ?>, <?= $parityData['F'] ?? 0 ?>],
            backgroundColor: ['rgb(205, 7, 7)', 'rgb(54, 162, 235)']
        }]
    };

    const config2 = {
        type: 'pie',
        data: data2,
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            }
        }
    };

    const myChart2 = new Chart(
        document.getElementById('myChart2'),
        config2
    );

    document.getElementById('maintenance_mode_toggle').addEventListener('change', function() {
        const maintenance_mode_toggle = document.getElementById('maintenance_mode_toggle');
        if (maintenance_mode_toggle.checked) {
            maintenance_mode_toggle.value = 'on';
        } else {
            maintenance_mode_toggle.value = 'off';
        }
        this.form.submit();
    });
</script>

</body>

</html>
