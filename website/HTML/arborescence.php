<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Cheap Marmiton | Recettes de cocktails</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Style/style.css?v=1.1">
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
                    <input type="text" name="q" placeholder="Rechercher...">
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

    <body>
        <div class="left-region">
            <?php
            $link = connectToDatabase();
            query($link, "USE $base");

            if (!isset($_GET["current_element_id"]) || empty($_GET["current_element_id"])) {
                // -- Displaying root -- //
                $result = query($link, "SELECT * FROM SelectRoot;");
                echo "<ul>";
                while ($index = mysqli_fetch_array($result)) {
                    echo "<a href=\"arborescence.php?current_element_id=" . $index[0] . "\"><li>" . $index[1] . "</li></a>";
                }
                echo "</ul>";
            } else {
                // -- Displaying subcategories -- //
                $result = requestAllSubElements($link, $_GET["current_element_id"]);
                echo "<ul>";
                while ($index = mysqli_fetch_array($result)) {
                    echo "<a href=\"arborescence.php?current_element_id=" . $index[0] . "\"><li>" . $index[1] . "</li></a>";
                }
                echo "</ul>";
            }
            mysqli_free_result($result);
            mysqli_close($link);
            ?>
        </div>
        <div class="right-region">
            <div class="wrapper">
                <?php
                $link = connectToDatabase();
                query($link, "USE $base");
                if (!isset($_GET["current_element_id"]) || empty($_GET["current_element_id"])) {
                    $result = query($link, "SELECT * FROM RECETTES");
                    while ($index = mysqli_fetch_array($result)) {
                        displayReciepeList(scanTitle($index[1]), $index[0], $index[1]);
                    }
                } else {
                    $element_id = $_GET["current_element_id"];
                    $result = queryAllReciepieFromElementID($link, $element_id);
                    while ($index = mysqli_fetch_array($result)) {
                        displayReciepeList(scanTitle($index[1]), $index[0], $index[1]);
                    }
                }
                ?>
            </div>
        </div>
    </body>

</html>