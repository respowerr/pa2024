<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Gestion des Camions</title>
    <link rel="stylesheet" href="../styles/admin_panel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="header">
    <div class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
    <div class="profile-btn">
        <a href="#" id="profileLink">
            <i class="fas fa-user-circle"></i> <span>Profil</span>
        </a>
    </div>
</div>

<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-tachometer-alt"></i> Dashboard</h3>
    </div>
    <ul class="main-menu">
        <li class="active"><a href="home_admin.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="camion_users.php"><i class="fas fa-truck"></i> Truck</a></li>
        <li><a href="event_users.php"><i class="fas fa-calendar-alt"></i> Event</a></li>
    </ul>
    <div class="admin-section">
        <h4>Administration</h4>
        <ul class="admin-menu">
            <li><a href="camion_admin.php"><i class="fas fa-truck"></i> Truck</a></li>
            <li><a href="event_admin.php"><i class="fas fa-calendar-alt"></i> Event</a></li>
            <li><a href="users_admin.php"><i class="fas fa-users"></i> Users</a></li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="content" id="content">
        <h1>Gestion des Camions</h1>
        <form method="post">
            <input type="text" name="plaqueImmatriculation" placeholder="Plaque d'immatriculation" required>
            <input type="number" name="capacite" placeholder="Capacité" required>
            <input type="number" name="tourneeId" placeholder="ID de la tournée" required>
            <input type="submit" name="addTruck" value="Ajouter le camion">
        </form>

        <?php
        $authorizationToken = urldecode($_COOKIE['Authorization'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['addTruck'])) {
                $postData = [
                    'plaqueImmatriculation' => $_POST['plaqueImmatriculation'],
                    'capacite' => $_POST['capacite'],
                    'tourneeId' => $_POST['tourneeId'],
                ];
                
                $ch = curl_init('http://localhost:8080/camions');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: $authorizationToken",
                    'Content-Type: application/json'
                ]);
                
                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    echo "<p>cURL Error #:" . $err . "</p>";
                } else {
                    echo "<p>Camion ajouté avec succès.</p>";
                }
            } elseif (isset($_POST['delete_id'])) {
                $ch = curl_init('http://localhost:8080/camions/' . $_POST['delete_id']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: $authorizationToken"
                ]);
                
                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    echo "<p>cURL Error #:" . $err . "</p>";
                } else {
                    echo "<p>Camion supprimé avec succès.</p>";
                }
            }
        }

        $ch = curl_init('http://localhost:8080/camions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $authorizationToken"
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo "<p>cURL Error #:" . $err . "</p>";
        } else {
            $camions = json_decode($response, true);
            if (is_array($camions)) {
                echo "<table>";
                echo "<thead><tr><th>ID</th><th>Plaque d'immatriculation</th><th>Capacité</th><th>Actions</th></tr></thead><tbody>";
                foreach ($camions as $camion) {
                    echo "<tr>";
                    echo "<td>{$camion['id']}</td>";
                    echo "<td>{$camion['plaqueImmatriculation']}</td>";
                    echo "<td>{$camion['capacite']}</td>";
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='delete_id' value='{$camion['id']}'>";
                    echo "<input type='submit' value='Supprimer' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce camion ?\");'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>Erreur lors du décodage de la réponse de l'API.</p>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
