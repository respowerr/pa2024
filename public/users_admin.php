<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Gestion des Utilisateurs</title>
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
        <li><a href="camion_admin.php"><i class="fas fa-truck"></i> Truck</a></li>
        <li><a href="event_admin.php"><i class="fas fa-calendar-alt"></i> Event</a></li>
        <li><a href="users_admin.php"><i class="fas fa-users"></i> Users</a></li>
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
        <h1>Gestion des Utilisateurs</h1>

        <?php
        $authorizationToken = 'Bearer ' . ($_COOKIE['Authorization'] ?? '');
        $ch = curl_init('http://localhost:8080/account');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $authorizationToken]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $users = json_decode($response, true);
        curl_close($ch);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteId'])) {
            $ch = curl_init('http://localhost:8080/account/delete/' . $_POST['deleteId']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $authorizationToken]);
            curl_exec($ch);
            curl_close($ch);
            echo "<p>User deleted successfully. Please refresh the page to see updated data.</p>";
        }
        ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td><?= htmlspecialchars($user['name'] . ' ' . $user['lastName']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="deleteId" value="<?= $user['id'] ?>">
                            <button type="submit">Delete</button>
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
