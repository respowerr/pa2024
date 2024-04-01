<?php
    
    include '../multilingue/translations.php';
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Au temps Donné</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../styles/main.css">
    </head>
    <body class="flex flex-col min-h-screen bg-gray-100">

    <?php include '../components/header.php'; ?>

    <div class="container grid grid-cols-2 gap-8">
        <div class="camions">
            <h2 class="camions">Ajouter un camion</h2>
            <form action="camion.php?action=ajouter" method="POST">
                <input type="text" name="plaque_immatriculation" class="input-field" placeholder="Plaque d'immatriculation" required>
                <input type="number" name="capacite" class="input-field" placeholder="Capacité" required>
                <input type="number" name="tournee_id" class="input-field" placeholder="ID de la tournée" required>
                <button type="submit" class="submit-btn">Ajouter</button>
            </form>

            <h2 class="camions">Modifier un camion</h2>
            <form action="camion.php?action=modifier" method="POST">
                <input type="number" name="camion_id" class="input-field" placeholder="ID du camion à modifier" required>
                <input type="text" name="plaque_immatriculation" class="input-field" placeholder="Nouvelle plaque d'immatriculation" required>
                <input type="number" name="capacite" class="input-field" placeholder="Nouvelle capacité" required>
                <input type="number" name="tournee_id" class="input-field" placeholder="Nouvelle ID de tournée" required>
                <button type="submit" class="submit-btn">Modifier</button>
            </form>

            <h2 class="camions">Supprimer un camion</h2>
            <form action="camion.php?action=supprimer" method="POST">
                <input type="number" name="camion_id" class="input-field" placeholder="ID du camion à supprimer" required>
                <button type="submit" class="submit-btn">Supprimer</button>
            </form>
        </div>
        <div class="camions">
            <h2 class="camions">Liste des camions</h2>
            <table class="table-auto">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Plaque d'immatriculation</th>
                        <th class="px-4 py-2">Capacité</th>
                        <th class="px-4 py-2">ID de la tournée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        include '../db/db.php';
                        $camions = getCamions($db);

                        if ($camions) {
                            foreach ($camions as $camion) {
                                echo "<tr>";
                                echo "<td class='px-4 py-2'>" . $camion['id'] . "</td>";
                                echo "<td class='px-4 py-2'>" . $camion['plaque_immatriculation'] . "</td>";
                                echo "<td class='px-4 py-2'>" . $camion['capacite'] . "</td>";
                                echo "<td class='px-4 py-2'>" . $camion['tournee_id'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Aucun camion trouvé.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


    <?php include '../components/footer.php'; ?>

    </body>
</html>


<?php


function getCamions($conn) {
    $sql = "SELECT * FROM camions";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

//Ajouter un camion
function ajouterCamion($conn, $plaque_immatriculation, $capacite, $tournee_id) {
    $sql = "INSERT INTO camions (plaque_immatriculation, capacite, tournee_id) VALUES ('$plaque_immatriculation', $capacite, $tournee_id)";
    return $conn->query($sql);
}

//Modifier un camion
function modifierCamion($conn, $camion_id, $plaque_immatriculation, $capacite, $tournee_id) {
    $sql = "UPDATE camions SET plaque_immatriculation='$plaque_immatriculation', capacite=$capacite, tournee_id=$tournee_id WHERE id=$camion_id";
    return $conn->query($sql);
}

//Supprimer un camion
function supprimerCamion($conn, $camion_id) {
    $sql = "DELETE FROM camions WHERE id=$camion_id";
    return $conn->query($sql);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        switch ($action) {
            case 'ajouter':
                if (isset($_POST['plaque_immatriculation']) && isset($_POST['capacite']) && isset($_POST['tournee_id'])) {
                    $plaque_immatriculation = $_POST['plaque_immatriculation'];
                    $capacite = $_POST['capacite'];
                    $tournee_id = $_POST['tournee_id'];

                    $result = ajouterCamion($conn, $plaque_immatriculation, $capacite, $tournee_id);

                    if ($result) {
                        echo "Le camion a été ajouté avec succès.";
                    } else {
                        echo "Erreur lors de l'ajout du camion.";
                    }
                } else {
                    echo "Tous les champs du formulaire doivent être remplis.";
                }
                break;
            case 'modifier':
                if (isset($_POST['camion_id']) && isset($_POST['plaque_immatriculation']) && isset($_POST['capacite']) && isset($_POST['tournee_id'])) {
                    $camion_id = $_POST['camion_id'];
                    $plaque_immatriculation = $_POST['plaque_immatriculation'];
                    $capacite = $_POST['capacite'];
                    $tournee_id = $_POST['tournee_id'];

                    $result = modifierCamion($conn, $camion_id, $plaque_immatriculation, $capacite, $tournee_id);

                    if ($result) {
                        echo "Le camion a été modifié avec succès.";
                    } else {
                        echo "Erreur lors de la modification du camion.";
                    }
                } else {
                    echo "Tous les champs du formulaire doivent être remplis.";
                }
                break;
            case 'supprimer':
                if (isset($_POST['camion_id'])) {
                    $camion_id = $_POST['camion_id'];
                    $result = supprimerCamion($conn, $camion_id);

                    if ($result) {
                        echo "Le camion a été supprimé avec succès.";
                    } else {
                        echo "Erreur lors de la suppression du camion.";
                    }
                } else {
                    echo "L'ID du camion à supprimer n'est pas défini.";
                }
                break;
            default:
                echo "Action non valide.";
                break;
        }
    } else {
        echo "Action non spécifiée.";
    }
} else {
    echo "Méthode de requête incorrecte.";
}
?>
