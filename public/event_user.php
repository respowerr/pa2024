<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Gestion des Événements</title>
    <link rel="stylesheet" href="../styles/admin_panel.css">
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

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Admin Dashboard</h3>
    </div>
    <ul>
        <li class="active"><a href="home_admin.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="camion_admin.php"><i class="fas fa-truck"></i> Camions</a></li>
        <li><a href="event_admin.php"><i class="fas fa-calendar"></i> Event</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content" id="content">
        <h1>Gestion des Événements</h1>

        <?php
        $authorizationToken = urldecode($_COOKIE['Authorization'] ?? '');
        $ch = curl_init('http://localhost:8080/event');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $authorizationToken"]);
        $response = curl_exec($ch);
        $events = json_decode($response, true);
        curl_close($ch);
        ?>

        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['eventName']) ?></td>
                    <td><?= htmlspecialchars($event['eventType']) ?></td>
                    <td><?= htmlspecialchars($event['eventStart']) ?></td>
                    <td><?= htmlspecialchars($event['eventEnd']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="eventId" value="<?= htmlspecialchars($event['id']) ?>">
                            <button type="submit" name="joinEvent">Rejoindre</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
