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
        <li class="active"><a href="home_admin.php"></i> Home</a></li>
        <li class="active"><a href="camion_admin.php"></i> Camions</a></li>
        <li class="active"><a href="event_admin.php"></i> Event</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content" id="content">
        <h1>Gestion des Événements</h1>

        <?php
        $authorizationToken = urldecode($_COOKIE['Authorization'] ?? '');
        $events = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ch = curl_init();
            $data = [
                'eventName' => $_POST['eventName'] ?? '',
                'eventType' => $_POST['eventType'] ?? '',
                'eventStart' => $_POST['eventStart'] ?? '',
                'eventEnd' => $_POST['eventEnd'] ?? '',
                'users' => $_POST['users'] ?? '',
                'location' => $_POST['location'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            if (isset($_POST['addEvent'])) {
            } elseif (isset($_POST['editEvent'])) {
            } elseif (isset($_POST['deleteEvent'])) {
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $authorizationToken", 'Content-Type: application/json']);
            $response = curl_exec($ch);
            curl_close($ch);

            echo "<p>Operation Successful. Please refresh the page to see updated data.</p>";
        }

        $ch = curl_init('http://localhost:8080/event');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $authorizationToken"]);
        $response = curl_exec($ch);
        if ($response) {
            $events = json_decode($response, true);
        }
        curl_close($ch);

        $eventToEdit = null;
        if (isset($_GET['editId'])) {
            foreach ($events as $event) {
                if ($event['id'] == $_GET['editId']) {
                    $eventToEdit = $event;
                    break;
                }
            }
        }
        ?>

        <?php if ($eventToEdit): ?>
        <form method="post">
            <input type="text" name="eventName" value="<?= $eventToEdit['eventName'] ?>" required>
            <input type="text" name="eventType" value="<?= $eventToEdit['eventType'] ?>" required>
            <input type="date" name="eventStart" value="<?= $eventToEdit['eventStart'] ?>" required>
            <input type="date" name="eventEnd" value="<?= $eventToEdit['eventEnd'] ?>" required>
            <input type="text" name="users" value="<?= $eventToEdit['users'] ?>" required>
            <input type="text" name="location" value="<?= $eventToEdit['location'] ?>" required>
            <input type="text" name="description" value="<?= $eventToEdit['description'] ?>" required>
            <button type="submit" name="editEvent">Save Changes</button>
        </form>
        <?php else: ?>
        <form method="post">
            <input type="text" name="eventName" placeholder="Event Name" required>
            <input type="text" name="eventType" placeholder="Event Type" required>
            <input type="date" name="eventStart" placeholder="Start Date" required>
            <input type="date" name="eventEnd" placeholder="End Date" required>
            <input type="text" name="users" placeholder="User" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="text" name="description" placeholder="Description" required>
            <button type="submit" name="addEvent">Ajouter Événement</button>
        </form>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= $event['id'] ?></td>
                    <td><?= $event['eventName'] ?></td>
                    <td><?= $event['eventType'] ?></td>
                    <td><?= $event['eventStart'] ?></td>
                    <td><?= $event['eventEnd'] ?></td>
                    <td><?= $event['location'] ?></td>
                    <td><?= $event['description'] ?></td>
                    <td>
                        <a href="?editId=<?= $event['id'] ?>">Modifier</a>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this event?');">
                            <input type="hidden" name="deleteId" value="<?= $event['id'] ?>">
                            <button type="submit" name="deleteEvent">Supprimer</button>
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
