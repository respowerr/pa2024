<!-- admin.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
</head>
<body>
    <h1>Ajouter une nouvelle langue</h1>
    <form action="addLanguages.php" method="post" enctype="multipart/form-data">
        <label for="filename">Nom du fichier :</label>
        <input type="text" id="filename" name="filename" required><br>
        <input type="file" id="file" name="file" required><br>
        <input type="submit" value="Ajouter">
    </form>
</body>
</html>
