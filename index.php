<!-- index.php -->
<?php
// Inclusion du fichier translations.php
include 'Multilingue/translations.php';

// Inclusion du fichier header.php
include 'header.php';
?>

<main>
    <h1><?php echo $translations['titre_accueil']; ?></h1>
    <p><?php echo $translations['description_accueil']; ?></p>
</main>

<?php
// Inclusion du fichier footer.php
include 'footer.php';
?>
