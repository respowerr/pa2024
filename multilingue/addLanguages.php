<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = $_POST["filename"];
    $file = $_FILES["file"];

    if ($file["error"] === UPLOAD_ERR_OK) {
        $destination = "lang/" . basename($filename);
        if (move_uploaded_file($file["tmp_name"], $destination)) {
            echo "Le fichier a été téléchargé avec succès.";
            
            $lang_name = pathinfo($filename, PATHINFO_FILENAME);
            $languages_file = 'languages.json';

            $languages_data = file_get_contents($languages_file);
            $languages = json_decode($languages_data, true);

            $languages[$lang_name] = ucfirst($lang_name);

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
