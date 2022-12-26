<!DOCTYPE html>
<html lang="fr">

<head>
	<title>Cheap Marmiton | Recettes de cocktails</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="Style/style.css?v=1">
	<?php require "helper.php";
	// Initialize the session
	session_start();
	$connected = isUserConnected();

	if (isset($_POST["add_recette"]) || isset($_POST["remove_recette"])) {
		// -- Favorites -- //
		// -> if the user is connected
		if ($connected) {
			$id_user = $_SESSION["id"];
			// -> if the user is not connected
		} else {
			$id_user = -1;
		}
		if (isset($_POST["add_recette"])) {
			addToFavorites($_POST["add_recette"], $id_user);
		} else {
			removeFromFavorites($_POST["remove_recette"], $id_user);
		}
	}
	?>
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
				if (!isUserConnected()) {
					echo "<li><a href=\"connexion.php\"><img src=\"./ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Se connecter</a> </li>";
					$id_user = -1;
				} else {
					echo "<li><a href=\"moncompte.php\"><img src=\"./ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Mon compte</a> </li>";
					$id_user = $_SESSION["id"];
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

	// -- Title and image -- //
	echo "<h1>" . $index[1] . "</h1>";
	?>

	<div class="container">
		<!-- Contenu de la première colonne -->
		<div class="column1">
			<?php
			$image_name = scanTitle($index[1]);
			if (file_exists("./ressources/Photos/$image_name.jpg")) {
				echo "<img src=\"./ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/>" . "</br>";
			} else {
				echo "<img src=\"./ressources/Img/DEFAULT.png\" alt=\"DEFAULT\"/>";
			}
			?>

			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
				<?php
				$id_recette = $_GET["id_recette"];
				if ($connected) {
					$id_user = $_SESSION["id"];
				} else {
					$id_user = -1;
				}
				$elementInBasket = checkIfReciepeinBasket($id_recette, $id_user);
				if (!$elementInBasket) {
					echo "<input type=\"hidden\" name=\"add_recette\" value=$id_recette>";
					echo "<input type=\"image\"  src=\"./ressources/Img/icons/favoris.png\" width=\"40\" height=\"45\" value=\"Ajouter aux favoris\">" . "Ajouter aux favoris";
				} else {
					echo "<input type=\"hidden\" name=\"remove_recette\" value=$id_recette>";
					echo "<input type=\"image\" src=\"./ressources/Img/icons/favoris.png\" width=\"40\" height=\"45\" value=\"Retirer des favoris\">" . "Retirer des favoris";
				}
				?>

			</form>

		</div>

		<!-- Contenu de la seconde colonne -->
		<div class="column0">
			<?php
			// -- Ingredients -- //
			echo "<h2>" . "Ingrédients : " . "</h2>";
			echo "<ul>";
			foreach (explode("|", $index[2]) as $str) {
				echo "<li>" . $str . "</li>";
			}
			echo "</ul>";

			// -- Reciepe -- //
			echo "<h2>" . "Recette : " . "</h2>";
			foreach (explode(".", $index[3]) as $value) {
				if (!empty($value)) {
					echo "<h3>" . $value . "." . "</h3>";
				}
			}
			?>
		</div>
	</div>


</body>

</html>