document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('addTruckForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        addTruck();
    });

    const searchBox = document.getElementById('searchBox');
    searchBox.addEventListener('input', function() {
        filterTrucks(searchBox.value);
    });

    const scrollTopBtn = document.getElementById('scrollTopBtn');
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    loadTrucks(); // Load trucks initially
});

async function fetchWithAuth(url, options = {}) {
    const authToken = localStorage.getItem('Authorization');
    options.headers = {
        'Content-Type': 'application/json',
        ...options.headers,
        ...(authToken && { Authorization: authToken })
    };

    const response = await fetch(url, options);
    if (!response.ok) throw new Error('Network error: ' + response.statusText);
    return response.json();
}

async function loadTrucks() {
    try {
        const trucks = await fetchWithAuth('http://localhost:8080/camions');
        displayTrucks(trucks);
    } catch (error) {
        console.error('Error loading trucks:', error);
    }
}

function displayTrucks(trucks) {
    const trucksContainer = document.getElementById('content');
    trucksContainer.innerHTML += `
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plaque d'immatriculation</th>
                    <th>Capacité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="truckTableBody">
                ${trucks.map(truck => `
                    <tr>
                        <td>${truck.id}</td>
                        <td>${truck.plaqueImmatriculation}</td>
                        <td>${truck.capacite}</td>
                        <td>
                            <button onclick="deleteTruck('${truck.id}')">Supprimer</button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function filterTrucks(searchTerm) {
    const rows = document.querySelectorAll("#truckTableBody tr");
    rows.forEach(row => {
        const plaque = row.cells[0].textContent.toLowerCase();
        row.style.display = plaque.includes(searchTerm.toLowerCase()) ? "" : "none";
    });
}

async function addTruck() {
    const plaque = document.getElementById('plaqueImmatriculation').value;
    const tourneeId = document.getElementById('tourneeId').value;
    const capacite = document.getElementById('capacite').value;
    try {
        await fetchWithAuth('http://localhost:8080/camions', {
            method: 'POST',
            body: JSON.stringify({ plaque, tourneeId, capacite })
        });
        loadTrucks(); // Reload list after addition
    } catch (error) {
        console.error('Error adding truck:', error);
    }
}

async function deleteTruck(truckId) {
    try {
        await fetchWithAuth(`http://localhost:8080/camions/${truckId}`, { method: 'DELETE' });
        loadTrucks(); // Reload list after deletion
    } catch (error) {
        console.error('Error deleting truck:', error);
    }
}
