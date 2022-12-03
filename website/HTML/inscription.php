<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Cheap Marmiton | Recettes de cocktails</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="Style/style.css?v=1">
	</head>
	<body>
        <h1>Inscription</h1>
		<form id="inscription" action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
			<label for="identifiant">Identifiant</label>
			<input type="text" name="identifiant"></br>

			<label for="nom">Nom</label>
			<input type="text" name="nom"></br>

			<label for="prenom">Prénom</label>
			<input type="text" name="prenom"></br>

			<label for="mail">E-mail</label>
			<input type="email" name="mail"></br>	

			<label for="mdp">Mot de passe</label>
			<input type="password" name="mdp"></br>

			<label for="mdp">Confirmer le mot de passe</label>
			<input type="password" name="mdpconfirm"></br>

			<input type="submit" name="submit" value="S'inscrire">
		</form>
		<?php 
			require "helper.php"; 
			if(isset($_POST['identifiant'])){
				$link=connectToDatabase();
				query($link, "USE BDD_marmiton");
				$id = transformStringToSQLCompatible($link,$_POST['identifiant']);
				$password = transformStringToSQLCompatible($link,$_POST['mdp']);
				$passwordConfirm = transformStringToSQLCompatible($link,$_POST['mdpconfirm']);
				$name = transformStringToSQLCompatible($link,$_POST['nom']);
				$firstname = transformStringToSQLCompatible($link,$_POST['prenom']);
				$mail = transformStringToSQLCompatible($link,$_POST['mail']);
				if(!checkIfElementExists($link,$id,"UTILISATEUR")){
					$Sql = "SELECT * FROM UTILISATEUR WHERE mail='$mail';";
					$result = query($link,$Sql);
					if(mysqli_num_rows($result) == 0){
						// -- There are no identical mail and ID, we can add the profile -- //
						$Sql = "INSERT INTO UTILISATEUR VALUES ('$id','$password','$name','$firstname','$mail');";
						query($link,$Sql);
						// Do stuff to connect user or redirect

						echo "User created !";
					} else {
						echo "MAIL does exists ! Bad !";
					}
				} else {
					echo "ID does exists ! Bad !";
				}
			}
		?>

	</body>
</html>