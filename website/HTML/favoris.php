<?php
require "helper.php";
// Initialize the session
session_start();
$connected = isUserConnected();
?>


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

	<h1>Mes Favoris</h1>

	<?php
	$link = connectToDatabase();
	query($link, "USE $base;");

	// -> if the user is connected
	if ($connected) {
		$id = $_SESSION["id"];
		$Sql = "SELECT * FROM PANIER JOIN RECETTES USING (id_recette) WHERE PANIER.id_utilisateur=$id;";
		$result = query($link, $Sql);
		$count = 0;

		echo "<div class=\"wrapper\">";
		while ($index = mysqli_fetch_row($result)) {
			$count++;
			$image_name = scanTitle($index[2]);
			displayReciepeList($image_name, $index[0], $index[2]);
		}
		echo "</div>";
		if ($count == 0) {
			// -- No favorites -- //
			echo "<h2>Vous n'avez ajouté aucun favoris !</h2>";
		}

		// -> if the user is not connected
	} else {
		if (!isset($_SESSION["favorites"]) || (isset($_SESSION["favorites"]) && (is_array($_SESSION["favorites"]) ? count($_SESSION["favorites"]) : 0) == 0)) {
			// -- No favorites -- //
			echo "<h2>Vous n'avez ajouté aucun favoris ! </h2>";
		} else {
			echo "<div class=\"wrapper\">";
			foreach ($_SESSION["favorites"] as $id_recette) {
				$Sql = "SELECT * FROM RECETTES WHERE id_recette=$id_recette;";
				$result = query($link, $Sql);
				$index = mysqli_fetch_row($result);
				$image_name = scanTitle($index[1]);
				displayReciepeList($image_name, $index[0], $index[1]);
			}
			echo "</div>";
		}
	}
	mysqli_close($link);
	?>
</body>

</html>