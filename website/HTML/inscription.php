<?php
require "helper.php";

// Connexion to database
$link = connectToDatabase();
query($link, "USE $base");

// Define variables and initialize with empty values
$username = $password = $confirm_password = $name = $firstname = $mail = $sex = $num_rue = $nom_rue = $ville = $code_postal = "";
$username_err = $password_err = $confirm_password_err = $mail_err = $address_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Validate username
	if (empty(trim($_POST["username"]))) {
		$username_err = "Veuillez entrer un nom d'utilisateur.";
	} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
		$username_err = "Les noms d'utilisateurs ne peuvent contenir que des lettres, des nombres et underscores.";
	} else {
		// Prepare a select statement
		$sql = "SELECT * FROM UTILISATEUR WHERE pseudo = ?";

		if ($stmt = mysqli_prepare($link, $sql)) {
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "s", $param_username);

			// Set parameters
			$param_username = trim($_POST["username"]);

			// Attempt to execute the prepared statement
			if (mysqli_stmt_execute($stmt)) {
				/* store result */
				mysqli_stmt_store_result($stmt);

				if (mysqli_stmt_num_rows($stmt) == 1) {
					$username_err = "Ce nom d'utilisateur est déjà utilisé.";
				} else {
					$username = trim($_POST["username"]);
				}
			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}

			// Close statement
			mysqli_stmt_close($stmt);
		}
	}

	// Validate password
	if (empty(trim($_POST["password"]))) {
		$password_err = "Veuillez entrer un mot de passe.";
	} elseif (strlen(trim($_POST["password"])) < 6) {
		$password_err = "Votre mot de passe doit contenir au moins 6 caractères.";
	} else {
		$password = trim($_POST["password"]);
	}

	// Validate confirm password
	if (empty(trim($_POST["confirm_password"]))) {
		$confirm_password_err = "Veuillez confirmer le mot de passe.";
	} else {
		$confirm_password = trim($_POST["confirm_password"]);
		if (empty($password_err) && ($password != $confirm_password)) {
			$confirm_password_err = "Les mots de passe ne correspondent pas.";
		}
	}

	$name = trim($_POST["name"]);
	$firstname = trim($_POST["firstname"]);
	$mail = trim($_POST["mail"]);
	if (!empty($mail)) {
		// Checking if the mail doesn't already exist
		$sql = "SELECT * FROM UTILISATEUR WHERE mail='" . transformStringToSQLCompatible($link, $mail) . "';";
		$result = query($link, $sql);
		$checkrows = mysqli_num_rows($result);
		if ($checkrows != 0) {
			$mail_err = "Cette adresse mail est déjà utilisée.";
		}
	}

	$sexe = trim($_POST["sex"]);

	// Validate address
	$num_rue = trim($_POST["num_rue"]);
	$nom_rue = trim($_POST["nom_rue"]);
	$ville = trim($_POST["ville"]);
	$code_postal = trim($_POST["code_postal"]);
	if ((empty($num_rue) || empty($nom_rue) || empty($ville) || empty($code_postal)) && !(empty($num_rue) && empty($nom_rue) && empty($ville) && empty($code_postal))) {
		$address_err = "Veuillez saisir une adresse complète.";
	}

	// Check input errors before inserting in database
	if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($mail_err)) {

		// Prepare an insert statement
		$sql = "INSERT INTO UTILISATEUR (pseudo, mdp, nom, prenom, mail, sexe, num_rue, nom_rue, ville, code_postal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


		if ($stmt = mysqli_prepare($link, $sql)) {
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "ssssssissi", $param_username, $param_password, $param_name, $param_firstname, $param_mail, $param_sexe, $param_num_rue, $param_nom_rue, $param_ville, $param_code_postal);

			// Set parameters
			$param_username = $username;
			$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
			$param_name = $name;
			$param_firstname = $firstname;
			$param_mail = $mail;
			$param_sexe = $sexe;
			$param_num_rue = $num_rue;
			$param_nom_rue = $nom_rue;
			$param_ville = $ville;
			$param_code_postal = $code_postal;


			// Attempt to execute the prepared statement
			if (mysqli_stmt_execute($stmt)) {
				mysqli_stmt_close($stmt);
				// Redirect to login page
				header("Location: connexion.php");
			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}

			// Close statement
			mysqli_stmt_close($stmt);
		} else {
			die("Error: " . $link->error);
		}
	}

	// Close connection
	mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<title>Cheap Marmiton | Recettes de cocktails</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="Style/style.css?v=1.1">
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

	<div class="formulaire">
		<h1>Inscription</h1>
		<p>Veuillez remplir ce formulaire afin de créer un compte.</p>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group">
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Nom d'utilisateur</label>
					</div>
					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
					</div>
				</div>
				<span class="invalid-feedback"><?php echo $username_err; ?></span>
			</div>
			<div class="form-group">

				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Adresse mail</label>
					</div>
					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="text" name="mail" class="form-control <?php echo (!empty($mail_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $mail; ?>">
					</div>
				</div>
				<span class="invalid-feedback"><?php echo $mail_err; ?></span>
			</div>
			<div class="form-group">
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Mot de passe</label>
					</div>
					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
					</div>
				</div>
				<span class="invalid-feedback"><?php echo $password_err; ?></span>
			</div>
			<div class="form-group">
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Confirmation du mot de passe</label>
					</div>
					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
					</div>
				</div>
				<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
			</div>
			<div class="form-group">
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Nom</label>
					</div>
					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="text" name="name" value="<?php echo $name; ?>">
					</div>
				</div>

			</div>
			<div class="form-group">
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Prénom</label>
					</div>
					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="text" name="firstname" value="<?php echo $firstname; ?>">
					</div>
				</div>
			</div>
			<div class="gender">
				<label>Femme</label>
				<input type="radio" name="sex" value="woman">
				<label>Homme</label>
				<input type="radio" name="sex" value="man">
				<label>Autre</label>
				<input type="radio" name="sex" value="other" checked="checked">
			</div>
			<div class="adr">
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Numéro de rue</label>
					</div>

					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="number" name="num_rue" value="<?php echo $num_rue; ?>">
					</div>
				</div>
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Nom de la rue</label>
					</div>

					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="text" name="nom_rue" value="<?php echo $nom_rue; ?>">
					</div>
				</div>
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Ville</label>
					</div>

					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="text" name="ville" value="<?php echo $ville; ?>">
					</div>
				</div>
				<div class="container">
					<!-- Contenu de la première colonne -->
					<div class="column">
						<label>Code Postal</label>
					</div>

					<!-- Contenu de la seconde colonne -->
					<div class="column">
						<input type="number" name="code_postal" value="<?php echo $code_postal; ?>">
					</div>
				</div>

				<span class="invalid-feedback"><?php echo $address_err; ?></span>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="S'inscrire">
			</div>
			<p>Vous possèdez déjà un compte ? <a href="connexion.php">Connectez vous ici</a>.</p>
		</form>
	</div>
</body>

</html>