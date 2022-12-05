<?php
	// Initialize the session
	session_start();
?>


<!DOCTYPE html>
<html lang="fr">

    <head>
        <title>Cheap Marmiton | Recettes de cocktails</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="Style/style.css?v=1">
        <?php require "helper.php"; ?>
    </head>

    <body>
        <header class="main-head">
            <nav>
                <div class="header">
                    <img src="../ressources/Img/logoCM4.jpg" alt="logo" />
                </div>
                <h1 id="logo">CheapMarmiton</h1>
                <div class="searchbar">
                    <input type="text">
                    <img src="../ressources/Img/icons/search.png">
                </div>
                <ul>
                    <li><a href="moncompte.php"><img src="../ressources/Img/icons/login.png" alt="login" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px" />Mon compte</a> </li>
                    <li><a href="favoris.php"><img src="../ressources/Img/icons/favoris.png" alt="favoris" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:10px" />Mes favoris</a> </li>
                </ul>
            </nav>
        </header>
    </body>
</html>