<!DOCTYPE html>
<html lang="fr">

<head>
	<title>Cheap Marmiton | Recettes de cocktails</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="Style/styleInscriptionConnexion.css? ">
	<meta http-equiv="Cache-control" content="no-cache">

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
				<li><a href="connexion.php"><img src="../ressources/Img/icons/login.png" alt="login" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px" />Se connecter</a> </li>
				<li><a href="favoris.php"><img src="../ressources/Img/icons/favoris.png" alt="favoris" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:10px" />Mes favoris</a> </li>
			</ul>
		</nav>
	</header>

	<div id="frm">
		<h1>Connexion</h1>
		<form id="connexion" action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
			<label for="identifiant">Identifiant</label>
			<input required="true" type="text" name="identifiant"></br>

			<label for="mdp">Mot de passe</label>
			<input required="true" type="password" name="mdp"></br>

			<p>Pas encore inscrit ? <a href="inscription.php">S'inscrire</a></p>

			<div id="seCoInput">
				<input id="seCoInput" type="submit" value="Se connecter">
			</div>

		</form>

		<?php
		require "helper.php";
		if (isset($_POST['identifiant'])) {
			$link = connectToDatabase();
			query($link, "USE BDD_marmiton");
			$id = transformStringToSQLCompatible($link, $_POST['identifiant']);
			$password = $_POST['mdp'];
			if (!checkIfElementExists($link, $id, "UTILISATEUR")) {
				echo "ID does not exists !";
			} else {
				$Sql = "SELECT id_utilisateur FROM UTILISATEUR WHERE id_utilisateur='$id' AND mdp='$password';";
				$result = query($link, $Sql);
				if (mysqli_num_rows($result) == 1) {
					// Do stuff to connect user //

					echo "Connexion réussie !";
				} else {
					echo "Connexion échouée !";
				}
			}
		}
		?>
	</div>
</body>

</html>