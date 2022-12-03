	<?php
		require "helper.php";

		// Connexion to database
		$link = connectToDatabase();

		// Define variables and initialize with empty values
		$username = $password = $confirm_password = "";
		$username_err = $password_err = $confirm_password_err = "";
		
		// Processing form data when form is submitted
		if($_SERVER["REQUEST_METHOD"] == "POST"){
		
			// Validate username
			if(empty(trim($_POST["username"]))){
				$username_err = "Veuillez entrer un nom d'utilisateur.";
			} elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
				$username_err = "Les noms d'utilisateurs ne peuvent contenir que des lettres, des nombres et underscores.";
			} else{
				// Prepare a select statement
				$sql = "SELECT id_utilisateurs FROM UTILISATEURS WHERE = ?";
				
				if($stmt = mysqli_prepare($link, $sql)){
					// Bind variables to the prepared statement as parameters
					mysqli_stmt_bind_param($stmt, "s", $param_username);
					
					// Set parameters
					$param_username = trim($_POST["username"]);
					
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						/* store result */
						mysqli_stmt_store_result($stmt);
						
						if(mysqli_stmt_num_rows($stmt) == 1){
							$username_err = "This username is already taken.";
						} else{
							$username = trim($_POST["username"]);
						}
					} else{
						echo "Oops! Something went wrong. Please try again later.";
					}

					// Close statement
					mysqli_stmt_close($stmt);
				}
			}
			
			// Validate password
			if(empty(trim($_POST["password"]))){
				$password_err = "Please enter a password.";     
			} elseif(strlen(trim($_POST["password"])) < 6){
				$password_err = "Password must have atleast 6 characters.";
			} else{
				$password = trim($_POST["password"]);
			}
			
			// Validate confirm password
			if(empty(trim($_POST["confirm_password"]))){
				$confirm_password_err = "Please confirm password.";     
			} else{
				$confirm_password = trim($_POST["confirm_password"]);
				if(empty($password_err) && ($password != $confirm_password)){
					$confirm_password_err = "Password did not match.";
				}
			}
			
			// Check input errors before inserting in database
			if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
				
				// Prepare an insert statement
				$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
				
				if($stmt = mysqli_prepare($link, $sql)){
					// Bind variables to the prepared statement as parameters
					mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
					
					// Set parameters
					$param_username = $username;
					$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
					
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						// Redirect to login page
						header("location: login.php");
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
		<link rel="stylesheet" type="text/css" href="Style/styleInscriptionConnexion.css?v=1">
	</head>
	<body>
		<div class="wrapper">
			<h2>Inscription</h2>
			<p>Veuillez remplir ce formulaire afin de créer un compte.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-group">
					<label>Nom d'utilisateur</label>
					<input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
					<span class="invalid-feedback"><?php echo $username_err; ?></span>
				</div>    
				<div class="form-group">
					<label>Mot de passe</label>
					<input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
					<span class="invalid-feedback"><?php echo $password_err; ?></span>
				</div>
				<div class="form-group">
					<label>Confirmation du mot de passe</label>
					<input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
					<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit">
					<input type="reset" class="btn btn-secondary ml-2" value="Reset">
				</div>
				<p>Vous possèdez déjà un compte ? <a href="login.php">Connectez vous ici</a>.</p>
			</form>
		</div>    
	</body>
</html>
