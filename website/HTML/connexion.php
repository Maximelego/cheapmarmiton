<?php
	require "helper.php";
	// Initialize the session
	session_start();
	
	// Check if the user is already logged in, if yes then redirect him to welcome page
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		header("Location: accueil.php");
		exit;
	}
	
	// Include config file
	$link=connectToDatabase();
	query($link,"USE $base"); 

	// Define variables and initialize with empty values
	$username = $password = "";
	$username_err = $password_err = $login_err = "";
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
	
		// Check if username is empty
		if(isset($_POST["username"]) && empty(trim($_POST["username"]))){
			$username_err = "Veuillez saisir un nom d'utilisateur.";
		} else{
			$username = trim($_POST["username"]);
		}
		
		// Check if password is empty
		if(isset($_POST["username"]) && empty(trim($_POST["mdp"]))){
			$password_err = "Veuillez saisir votre mot de passe.";
		} else{
			$password = trim($_POST["mdp"]);
		}
		
		// Validate credentials
		if(empty($username_err) && empty($password_err)){
			// Prepare a select statement
			$sql = "SELECT id_utilisateur, pseudo, mdp FROM UTILISATEUR WHERE pseudo = ?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_username);
				
				// Set parameters
				$param_username = $username;
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Store result
					mysqli_stmt_store_result($stmt);
					
					// Check if username exists, if yes then verify password
					if(mysqli_stmt_num_rows($stmt) == 1){             
						// Bind result variables
						mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
						if(mysqli_stmt_fetch($stmt)){
							if(password_verify($password, $hashed_password)){
								// Password is correct, so start a new session
								session_start();
								
								// Store data in session variables
								$_SESSION["loggedin"] = true;
								$_SESSION["id"] = $id;
								$_SESSION["username"] = $username;                            
								
								// Redirect user to welcome page
								header("Location: accueil.php");
							} else{
								// Password is not valid, display a generic error message
								$login_err = "Le mot de passe est invalide.";
							}
						}
					} else{
						// Username doesn't exist, display a generic error message
						$login_err = "Le nom d'utilisateur n'existe pas.";
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
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
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mdp" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Connexion">
            </div>
            <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez vous ici</a>.</p>
        </form>
	</div>
</body>

</html>