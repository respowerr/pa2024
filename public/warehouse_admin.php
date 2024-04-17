<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Gestion des Camions</title>
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
        <h1>Bienvenue sur le panel Admin</h1>
    </div>
</div>

</body>
</html>
