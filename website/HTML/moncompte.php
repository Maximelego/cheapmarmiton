<?php
    require "helper.php";
	// Initialize the session
	session_start();
    $ancient_password = $new_password = $new_password_confirm = $username = "";
    $ancient_password_err = $new_password_err = $new_password_confirm_err = $username_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){	
        // Connexion to database
        $link = connectToDatabase();
        query($link,"USE $base");

        // Initializing value
        $changing_id = false;
        $changing_password = false;
        $disconnect = false;
        if(isset($_POST["action"])){
            if(strcmp($_POST["action"],"changing_id") == 0){ $changing_id = true; }
            if(strcmp($_POST["action"],"changing_password") == 0){ $changing_password = true; }
            if(strcmp($_POST["action"],"disconnect") == 0){ $disconnect = true; }
        }

        if($disconnect){
            disconnectUser();
        }

        // ---- CONTROLLING THE VALUES ---- //
        if($changing_id){
            // Validate username
            if(empty(trim($_POST["username"]))){
                $username_err = "Veuillez saisir un nom d'utilisateur.";
            } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
                $username_err = "Les noms d'utilisateurs ne peuvent contenir que des lettres, des nombres et underscores.";
            } else{
                // Prepare a select statement
                $sql = "SELECT * FROM UTILISATEUR WHERE pseudo = ?";
                
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
                            $username_err = "Ce nom d'utilisateur est déjà utilisé.";
                        } else{
                            $username = trim($_POST["username"]);
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                } else {
                    echo "[ERROR] - ".$link->error;
                }
            }

        } else if($changing_password){
			
            // Validate password
            if(!empty(trim($_POST["ancient_password"]))){
                // Prepare a select statement
                $sql = "SELECT mdp FROM UTILISATEUR WHERE pseudo = ?";
                
                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $param_username);
                    
                    // Set parameters
                    $param_username = $_SESSION["username"];
                    $password = trim($_POST["ancient_password"]);
                    
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Store result
                        mysqli_stmt_store_result($stmt);
                        
                        // Check if username exists, if yes then verify password
                        if(mysqli_stmt_num_rows($stmt) == 1){             
                            // Bind result variables
                            mysqli_stmt_bind_result($stmt,$hashed_password);
                            if(mysqli_stmt_fetch($stmt)){
                                if(password_verify($password, $hashed_password)){  
                                    // Redirect user to welcome page
                                    $ancient_password_err = "";
                                } else{
                                    // Password is not valid, display a generic error message
                                    $ancient_password_err = "L'ancien mot de passe est incorrect.";
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

                // Validate new password
                if(empty(trim($_POST["new_password"]))){
                    $new_password_err = "Veuillez saisir un mot de passe.";     
                } elseif(strlen(trim($_POST["new_password"])) < 6){
                    $new_password_err = "Votre mot de passe doit contenir au moins 6 caractères.";
                } else{
                    $new_password = trim($_POST["new_password"]);
                }
                    
                // Validate confirm password
                if(empty(trim($_POST["new_password_confirm"]))){
                    $new_password_confirm_err = "Veuillez confirmer le mot de passe.";     
                } else{
                    $new_password_confirm = trim($_POST["new_password_confirm"]);
                    if(empty($password_err) && (strcmp($new_password,$new_password_confirm) != 0)){
                        $new_password_confirm_err = "Les mots de passe ne correspondent pas.";
                    }
                }
            } else {
                $ancient_password_err = "Renseignez votre ancien mot de passe.";
            }
        }
		

        // ---- UPDATING VALUES ---- //
		// Check input errors before inserting in database
		if($changing_id && empty($username_err)){
            update_id($link,$username);
		} else if($changing_password && empty($ancient_password_err) && empty($new_password_confirm_err) && empty($new_password_err)){
            update_password($link,$new_password);
        }
        $changing_id = false;
        $changing_password = false;
        $disconnecting = false;
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
        <link rel="stylesheet" type="text/css" href="Style/style.css?v=1">
    </head>

    <body>
        <header class="main-head">
            <nav>
                <a href="accueil.php">
                    <div class="header">
                        <img src="../ressources/Img/logoCM4.jpg" alt="logo" />
                    </div>
                    <h1 id="logo">CheapMarmiton</h1>
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
                        if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                            echo "<li><a href=\"connexion.php\"><img src=\"../ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Se connecter</a> </li>";
                        } else {
                            echo "<li><a href=\"moncompte.php\"><img src=\"../ressources/Img/icons/login.png\" alt=\"login\" style=\"width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:3px\" />Mon compte</a> </li>";
                        }
                    ?>
                    <li><a href="favoris.php"><img src="../ressources/Img/icons/favoris.png" alt="favoris" style="width:30px;height:30px;padding-left:-15px;margin-right: 7px;vertical-align:middle;margin-bottom:10px" />Mes favoris</a> </li>
                </ul>
            </nav>
        </header>

        <div class="fields">
            <h1>Changer mes informations de connexion</h1>

            <h3>Changer mon identifiant</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <!-- Formulaire de modification de l'identifiant -->
                <input type="hidden" name="action" value="changing_id">
                <div class="form-group">
					<label>Nouvel identifiant</label>
					<input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>">
					<span class="invalid-feedback"><?php echo $username_err; ?></span>
				</div>
                <div class="form-group">
					<input type="submit" class="btn btn-primary" value="Modifier l'identifiant">
				</div>
            </form>
            </br>

            <h3>Changer mon mot de passe</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <!-- Formulaire de modification de mot de passe -->
                <input type="hidden" name="action" value="changing_password">
                <div class="form-group">
					<label>Ancien mot de passe</label>
					<input type="password" name="ancient_password" class="form-control <?php echo (!empty($ancient_password_err)) ? 'is-invalid' : ''; ?>">
					<span class="invalid-feedback"><?php echo $ancient_password_err; ?></span>
				</div>
                <div class="form-group">
					<label>Nouveau mot de passe</label>
					<input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>">
					<span class="invalid-feedback"><?php echo $new_password_err; ?></span>
				</div>
                <div class="form-group">
					<label>Confirmation nouveau mot de passe</label>
					<input type="password" name="new_password_confirm" class="form-control <?php echo (!empty($new_password_confirm_err)) ? 'is-invalid' : ''; ?>">
					<span class="invalid-feedback"><?php echo $new_password_confirm_err; ?></span>
				</div>
                <div class="form-group">
					<input type="submit" class="btn btn-primary" value="Modifier le mot de passe">
				</div>
            </form>
            </br>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <input type="hidden" name="action" value="disconnect">
                <input type="submit" class="btn btn-primary" value="Se déconnecter">
            </form>
        </div>
    </body>
</html>