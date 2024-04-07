<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Au temps Donné</title>
    <link rel="stylesheet" href="../styles/admin_panel.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>

<div class="header">
    <div class="menu-toggle" id="menu-toggle">
        <i class="fas fa-list-ul"></i>
    </div>
    <div class="profile-btn">
    <a href="#">
        <i class="fas fa-user-circle"></i> <span>Profil</span>
    </a>
</div>
</div>


<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Admin Dashboard</h3>
    </div>
    <ul>
        <li><a href="?page=trucks"><i class="fas fa-truck"></i> Camions</a></li>
        <li><a href="?page=maraudes"><i class="fas fa-walking"></i> Maraudes</a></li>
        <li><a href="?page=users"><i class="fas fa-users"></i> Utilisateurs</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content">
    </div>
</div>

<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});
</script>

</body>
</html>
