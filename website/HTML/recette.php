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
	<?php
	$id = $_GET['id_recette'];
	$link = connectToDatabase();
	$Sql = "USE $base";
	query($link, $Sql);
	$Sql = "SELECT * FROM RECETTES WHERE id_recette=$id;";
	$result = query($link, $Sql);
	$index = mysqli_fetch_row($result);

	// -- Classical values -- //
	echo "<h1>" . utf8_encode($index[1]) . "</h1>";

	$image_name = scanTitle($index[1]);
	if (file_exists("./ressources/Photos/$image_name.jpg")) {
		echo "<img src=\"./ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/>" . "</br>";
	} else {
		echo "<img src=\"./ressources/Img/DEFAULT.png\" alt=\"DEFAULT\"/>";
	}
	echo "<h2>" . "Ingrédients : " . "</h2>";
	echo "<ul>";
	foreach (explode("|", $index[2]) as $str) {
		echo "<li>" . utf8_encode($str) . "</li>";
	}
	echo "</ul>";
	echo "<h2>" . "Recette : " . "</h2>";
	echo "<h3>" . utf8_encode($index[3]) . "</h3>";

	?>
</body>

</html>