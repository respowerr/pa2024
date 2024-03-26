<?php
// addLanguages.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = $_POST["filename"];
    $file = $_FILES["file"];

    // Vérifier si un fichier a été téléchargé avec succès
    if ($file["error"] === UPLOAD_ERR_OK) {
        // Déplacer le fichier téléchargé vers le répertoire de destination
        $destination = "lang/" . basename($filename); // Utilisez le nom de fichier fourni
        if (move_uploaded_file($file["tmp_name"], $destination)) {
            echo "Le fichier a été téléchargé avec succès.";
            
            // Récupérer le nom de la langue à partir du nom de fichier sans extension
            $lang_name = pathinfo($filename, PATHINFO_FILENAME);
            $languages_file = 'languages.json';

            // Charger les langues existantes depuis le fichier JSON
            $languages_data = file_get_contents($languages_file);
            $languages = json_decode($languages_data, true);

            // Ajouter la nouvelle langue au tableau des langues
            $languages[$lang_name] = ucfirst($lang_name); // Mettre en majuscule la première lettre du nom de langue

            // Convertir le tableau mis à jour en JSON et enregistrer dans le fichier
            $json_data = json_encode($languages, JSON_PRETTY_PRINT);
            file_put_contents($languages_file, $json_data);
        } else {
            echo "Une erreur s'est produite lors du téléchargement du fichier.";
        }
    } else {
        echo "Une erreur s'est produite lors du téléchargement du fichier.";
    }
}
?>
