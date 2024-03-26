<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrateur</title>
    <link rel="stylesheet" href="../styles/admin_panel.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="admin-panel">
    <div class="content">
        <header>
            <button class="menu-toggle"><i class="fas fa-bars"></i></button>
            <div class="profile-btn"><i class="fas fa-user"></i> Profil</div> 
        </header>
        <div class="admin-content">Contenu principal...</div>
    </div>
    <div class="sidebar">
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Utilisateurs</a></li>
            <li><a href="#">Messages</a></li>
            <li><a href="#">Paramètres</a></li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');


    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        content.classList.add('collapsed');
    }

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
        menuToggle.style.display = 'block'; 
    });
});
</script>

</body>
</html>
