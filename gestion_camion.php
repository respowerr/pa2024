//Ajouter la connexion a la DB


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
?>