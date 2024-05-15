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

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'eventName' => $_POST['eventName'],
        'eventType' => $_POST['eventType'],
        'eventStart' => date('c', strtotime($_POST['eventStart'])),
        'eventEnd' => date('c', strtotime($_POST['eventEnd'])),
        'location' => $_POST['location'],
        'description' => $_POST['description']
    ];

    $result = makeHttpRequest($baseUrl . '/event/request', 'POST', $data);
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        header('Location: create_event.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
        $title = "Create Event - ATD";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/head.php');
    ?>    
    <style>
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            width: 300px;
        }
        .form-container input, 
        .form-container textarea,
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
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
        <main>
            <div class="content">
                <div class="form-container">
                    <form action="" method="POST">
                        <input type="text" name="eventName" placeholder="Event Name" required>
                        <input type="text" name="eventType" placeholder="Event Type" required>
                        <input type="datetime-local" name="eventStart" placeholder="Event Start" required>
                        <input type="datetime-local" name="eventEnd" placeholder="Event End" required>
                        <input type="text" name="location" placeholder="Location" required>
                        <textarea name="description" placeholder="Description" rows="4" required></textarea>
                        <button type="submit">Create Event</button>
                    </form>
                    <?php if (isset($error)): ?>
                        <p style="color: red;"><?= escape($error) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>
    </div>
</body>
</html>
