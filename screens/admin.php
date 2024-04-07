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
        <li><a href="#" onclick="showTrucks()">Camions</a></li>
        <li><a href="?page=maraudes"><i class="fas fa-walking"></i> Maraudes</a></li>
        <li><a href="?page=users"><i class="fas fa-users"></i> Utilisateurs</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content" id="content">
    </div>
</div>

<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

function showTrucks() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div id="truckFormContainer">
            <h2>Ajouter un camion</h2>
            <form id="addTruckForm">
                <input type="text" id="plaqueImmatriculation" name="plaqueImmatriculation" placeholder="Plaque d'immatriculation" required>
                <input type="number" id="tourneeId" name="tourneeId" placeholder="ID de la tournée" required>
                <input type="number" id="capacite" name="capacite" placeholder="Capacité" required>
                <button type="submit">Ajouter le camion</button>
            </form>
        </div>
        <div id="trucksContainer">
            <!-- Les camions seront chargés ici -->
        </div>
    `;
    document.getElementById('addTruckForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const plaqueImmatriculation = document.getElementById('plaqueImmatriculation').value;
        const tourneeId = document.getElementById('tourneeId').value;
        const capacite = document.getElementById('capacite').value;
        
        const truckData = {
            plaqueImmatriculation,
            tourneeId: parseInt(tourneeId),
            capacite: parseInt(capacite)
        };
        
        addTruck(truckData);
    });
    loadTrucks();
}

function loadTrucks() {
    fetch('http://localhost:8080/camions')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const trucksContainer = document.getElementById('trucksContainer');
            trucksContainer.innerHTML = data.map(truck => `
                <div class="truck">
                    <span>${truck.plaqueImmatriculation} - ${truck.tourneeId} - ${truck.capacite}</span>
                    <button onclick="deleteTruck('${truck.plaqueImmatriculation}')">Supprimer</button>
                </div>
            `).join('');
        })
        .catch(error => console.error('Error loading trucks:', error));
}

function addTruck(truckData) {
    fetch('http://localhost:8080/camions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(truckData)
    })
    .then(response => {
        if (response.status === 201) {
            return response.json();
        } else {
            throw new Error('Failed to add truck with status ' + response.status);
        }
    })
    .then(newTruck => {
        console.log('Added truck:', newTruck);
        loadTrucks();
    })
    .catch(error => console.error('Error adding truck:', error));
}

</script>

</body>
</html>
