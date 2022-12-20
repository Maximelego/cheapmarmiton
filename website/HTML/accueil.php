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
						<img src="../ressources/Img/logoCM4.jpg" alt="logo" />
					</div>
					<div class="title">CheapMarmiton</div>
			</div>
			</a>
			<div class="searchbar">
				<form method="POST" action="search.php">
					<input type="text" name="q" placeholder="Rechercher...">
					<button type="submit">
						<img src="../ressources/Img/icons/search.png" alt="Rechercher">
					</button>
				</form>
			</div>
			<ul>
				<?php
				session_start();
				if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
					echo "<li><a href=\"connexion.php\"><img src=\"../ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Se connecter</a> </li>";
				} else {
					echo "<li><a href=\"moncompte.php\"><img src=\"../ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Mon compte</a> </li>";
				}
				?>
				<li><a href="favoris.php"><img src="../ressources/Img/icons/favoris.png" alt="favoris" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:10px" />Mes favoris</a> </li>
			</ul>
		</nav>
	</header>



	<div class="wrapper">
		<?php
		$link = connectToDatabase();
		query($link, "USE $base;");
		$Sql = "SELECT * FROM RECETTES;";
		$result = query($link, $Sql);

		while ($index = mysqli_fetch_row($result)) {
			$image_name = scanTitle($index[1]);
			if (file_exists("../ressources/Photos/$image_name.jpg")) {
				echo "<div class=\"box\">";
				echo "<a href=\"recette.php?id_recette=$index[0]\">";
				echo "<img src=\"../ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/>" . "</br>";
				echo "<h2>" . utf8_encode($index[1]) . "</h2>" . "</br>";
				echo "</a>";
				echo "</div>";
			}
		}
		mysqli_close($link);
		?>
	</div>
</body>

</html>