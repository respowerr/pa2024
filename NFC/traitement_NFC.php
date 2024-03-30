<?php

function enregistrerPassage($codeNFC) {
    //Mettre le bon nom de DB
    $pdo = new PDO('mysql:host=localhost;dbname=', 'root', 'root');
    
    $requete = $pdo->prepare("INSERT INTO passages (code_nfc, date_passage) VALUES (:code_nfc, NOW())");
    
    $requete->execute(array(
        'code_nfc' => $codeNFC
    ));
    
    if ($requete->rowCount() > 0) {
        echo "Passage enregistré avec succès.";
    } else {
        echo "Erreur lors de l'enregistrement du passage.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['codeNFC'])) {
        $codeNFC = $_POST['codeNFC'];
        
        enregistrerPassage($codeNFC);
    }
}

?>
