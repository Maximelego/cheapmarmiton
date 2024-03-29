<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Cheap Marmiton | Recettes de cocktails</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Style/style.css?v=1">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#tags").autocomplete({
                source: "liste_interet.php",
                minLength: 1
            });
        });
    </script>


    <?php require "helper.php"; ?>
</head>

<body>
    <header class="main-head">
        <nav>
            <div class="container1">
                <a href="accueil.php">
                    <div class="header">
                        <img src="./ressources/Img/logoCM4.jpg" alt="logo" />
                    </div>
                    <div class="title">CheapMarmiton</div>
            </div>
            </a>
            <div class="searchbar">
                <form method="POST" action="search.php">
                    <input type="text" id="tags" name="q" placeholder="Rechercher...">
                    <button type="submit">
                        <img src="./ressources/Img/icons/search.png" alt="Rechercher">
                    </button>
                </form>

            </div>
            <ul>
                <?php
                session_start();
                if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
                    echo "<li><a href=\"connexion.php\"><img src=\"./ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Se connecter</a> </li>";
                } else {
                    echo "<li><a href=\"moncompte.php\"><img src=\"./ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Mon compte</a> </li>";
                }
                ?>
                <li><a href="favoris.php"><img src="./ressources/Img/icons/favoris.png" alt="favoris" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:10px" />Mes favoris</a> </li>
            </ul>
        </nav>
    </header>


    <?php
    // Connexion à la base de données
    $conn = connectToDatabase();
    query($conn, "USE $base;");

    // Récupérez la chaîne de recherche à partir des données POST et protégez-la contre les injections SQL 
    // (Il y a déjà une fonction pour ça dans helper.php)
    $search_query = transformStringToSQLCompatible($conn, $_POST['q']);

    // Créez une requête SQL pour récupérer les données de la base de données qui correspondent à la chaîne de recherche
    $sql = "SELECT * FROM RECETTES WHERE titre LIKE '%$search_query%' OR ingredients LIKE '%$search_query%'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<h1>Recettes trouvées pour les mots clés : " . $_POST['q'] . "</h1>";
        echo "<div class=\"wrapper\">";
        while ($row = mysqli_fetch_assoc($result)) {
            // traitement des lignes retournées ici
            $image_name = scanTitle($row['titre']);
            displayReciepeList($image_name, $row['id_recette'], $row['titre']);
        }
        echo "</div>";
    } else {
        // gestion de l'erreur ici
        echo "Erreur lors de l'exécution de la requête : " . mysqli_error($conn);
    }
    if (mysqli_affected_rows($conn) == 0) {
        // Aucun enregistrement n'a été trouvé
        echo "<h1>Aucun résultat.</h1>";
    }
    // Fermez la connexion à la base de données
    mysqli_close($conn);
    ?>
</body>