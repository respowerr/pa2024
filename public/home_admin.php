<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Au temps Donné</title>
    <link rel="stylesheet" href="../styles/admin_panel.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
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
        <li><a href="#" id="trucksLink"><i class="fas fa-truck"></i> Camions</a></li>
        <li><a href="?page=maraudes"><i class="fas fa-walking"></i> Maraudes</a></li>
        <li><a href="?page=users"><i class="fas fa-users"></i> Utilisateurs</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content" id="content">
        <h1 style="margin-top: 20px;">Gestion des Camions</h1>
        <input type="text" id="searchBox" placeholder="Rechercher un camion par nom..." style="margin-bottom: 20px; width: 100%; padding: 8px;">
        <form id="addTruckForm" style="margin-bottom: 20px;">
            <input type="text" id="plaqueImmatriculation" placeholder="Plaque d'immatriculation" required>
            <input type="number" id="tourneeId" placeholder="ID de la tournée" required>
            <input type="number" id="capacite" placeholder="Capacité" required>
            <button type="submit">Ajouter le camion</button>
        </form>
    </div>
    <button id="scrollTopBtn" style="position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; font-size: 16px; cursor: pointer;">Haut de page</button>
</div>

<script src="../script/admin.js"></script>
</body>
</html>
