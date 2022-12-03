<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Cheap Marmiton | Recettes de cocktails</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="Style/style.css?v=1">

	</head>
	<body>
        <h1>Connexion</h1>
		<form id="connexion" action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
			<label for="identifiant">Identifiant</label>
			<input type="text" name="identifiant"></br>

			<label for="mdp">Mot de passe</label>
			<input type="password" name="mdp"></br>
			
			<p>Pas encore inscrit ? <a href="inscription.php">S'inscrire</a></p>

			<input type="submit" value="Se connecter">
		</form>

		<?php 
			require "helper.php"; 
			if(isset($_POST['identifiant'])){
				$link=connectToDatabase();
				query($link, "USE BDD_marmiton");
				$id = transformStringToSQLCompatible($link,$_POST['identifiant']);
				$password = $_POST['mdp'];
				if(!checkIfElementExists($link,$id,"UTILISATEUR")){
					echo "ID does not exists !";
				} else {
					$Sql = "SELECT id_utilisateur FROM UTILISATEUR WHERE id_utilisateur='$id' AND mdp='$password';";
					$result = query($link, $Sql);
					if(mysqli_num_rows($result) == 1){
						// Do stuff to connect user //

						echo "Connexion réussie !";
					} else {
						echo "Connexion échouée !";
					}
				}
			}
		?>
	</body>
</html>