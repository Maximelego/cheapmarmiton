<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Cheap Marmiton | Recettes de cocktails</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <?php require "helper.php"; ?>
</head>



<?php
// Connexion à la base de données
$conn = connectToDatabase();

// Vérification de la connexion
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
query($conn, "USE $base;");



// Récupérez la chaîne de recherche à partir des données POST et protégez-la contre les injections SQL
$search_query = mysqli_real_escape_string($conn, $_POST['q']);

// Créez une requête SQL pour récupérer les données de la base de données qui correspondent à la chaîne de recherche
$sql = "SELECT * FROM RECETTES WHERE titre LIKE '%$search_query%' OR ingredients LIKE '%$search_query%'";

// Exécutez la requête et récupérez les résultats
$result = mysqli_query($conn, $sql);

if ($result) {
    $width = $height = 250;
    echo "<h1>Résultats trouvé pour la recherche " . $_POST['q'] . ": </h1>";
    while ($row = mysqli_fetch_assoc($result)) {
        // traitement des lignes retournées ici

        $image_name = scanTitle($row['titre']);
        if (file_exists("../ressources/Photos/$image_name.jpg")) {
            echo "<div class=\"box\">";
            echo "<a href='recette.php?id_recette={$row['id_recette']}'>";
            echo "<img src=\"../ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/ width=\"$width\" height=\"$height\">" . "</br>";
            echo "</div>";
        } else {
            echo "<div class=\"box\">";
            echo "<a href='recette.php?id_recette={$row['id_recette']}'>";
            echo "<img src=\"../ressources/Photos/DEFAULT.png\" alt=\"$image_name\"/ width=\"$width\" height=\"$height\">" . "</br>";
            echo "</div>";
        }

        echo "<ul>";
        echo "<li>" . $row['titre'] . "</li>";
        echo "</ul>";
    }
} else {
    // gestion de l'erreur ici
    echo "Erreur lors de l'exécution de la requête : " . mysqli_error($conn);
}

if (mysqli_affected_rows($conn) == 0) {
    // Aucun enregistrement n'a été trouvé
    echo "<h1>Aucun résultat trouvé pour votre recherche.</h1>";
}


// Fermez la connexion à la base de données
mysqli_close($conn);
?>