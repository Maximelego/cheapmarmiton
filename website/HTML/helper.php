
<?php
	$base="BDD_marmiton";
    global $base;

	function collectObjectID($link, $object, $table){
		if(!checkIfElementExists($link, $object, $table)){
			return false;
		}
		// -- Link table for index -- //
		if(strcmp($table,"INGREDIENTS") == 0){
			$query = "SELECT (id_ingredient) FROM INGREDIENTS WHERE (nom_ingredient='$object');";
		} else if(strcmp($table,"ELEMENTCATEGORIE") == 0) {
			$query = "SELECT (id_element) FROM ELEMENTCATEGORIE WHERE (nom_element='$object');";
		}
		$result = mysqli_query($link, $query);
		$index = mysqli_fetch_row($result);
		mysqli_free_result($result);
		return $index;
	}

	function checkIfElementExists($link,$element,$table){
		$found = true;
		if(strcmp($table,"INGREDIENTS") == 0){
			$query = "SELECT id_ingredient FROM $table WHERE (nom_ingredient='$element');";
		} else if(strcmp($table,"ELEMENTCATEGORIE") == 0) {
			$query = "SELECT id_element FROM $table WHERE (nom_element='$element');";
		} else if(strcmp($table,"UTILISATEUR") == 0){
			$query = "SELECT id_utilisateur FROM $table WHERE (id_utilisateur='$element');";
		}

		$result = mysqli_query($link, $query);
		$checkrows = mysqli_num_rows($result);
		if($checkrows == 0){
			$found = false;
		}
		mysqli_free_result($result);
		return $found;
	}

    function query($link,$Sql){
		if(isset($Sql) && strcmp($Sql,"") != 0){
			$resultat=mysqli_query($link,$Sql) or die("$Sql : ".mysqli_error($link));
		    return($resultat);
		} 
	    return 0;
	}

    function scanTitle($title){
		$wanted_array = array(
			"S", "s", "Z", "z", "A", "A", "A", "A", "A", "A", "A", "C", "E", "E",
			"E", "E", "I", "I", "I", "I", "N", "O", "O", "O", "O", "O", "O", "U",
			"U", "U", "U", "Y", "B", "Ss", "a", "a", "a", "a", "a", "a", "a", "c",
			"e", "e", "e", "e", "i", "i", "i", "i", "o", "n", "o", "o", "o", "o",
			"o", "o", "u", "u", "u", "y", "b", "y"
		);
		$unwanted_array = array(    
			"Š", "š", "Ž", "ž", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É",
			"Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù",
			"Ú", "Û", "Ü", "Ý", "Þ" , "ß", "à", "á", "â", "ã", "ä", "å", "æ", "ç",
			"è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ",
			"ö", "ø", "ù", "ú", "û", "ý", "þ", "ÿ"
		);
		$formattedTitle = str_replace($unwanted_array,$wanted_array,utf8_encode($title));
		$formattedTitle = str_replace(" ","_", $formattedTitle);
        return utf8_encode($formattedTitle);
    }

	function connectToDatabase(){
        $link = mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
        return $link;
	}

	function disconnectUser(){
		// Unset all of the session variables
		$_SESSION = array();
		// Destroy the session.
		session_destroy();
		header("Location: accueil.php");
		exit();
	}

	function isUserConnected(){
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
			return true;
		} 
		return false;
	}

    function transformStringToSQLCompatible($link,$string){
		return mysqli_real_escape_string($link,$string);
	}

	function update_password($link,$password){
		$sql = "UPDATE UTILISATEUR SET mdp = ?;";
		if($stmt = mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt,"s",$param_password);
			$param_password = password_hash($password,  PASSWORD_DEFAULT);
			if(mysqli_stmt_execute($stmt)){
				// Do confirm stuff...
				// echo "password modified !";
			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		} else {
			echo "[ERROR] - ".$link->error;
		}
	}

	function update_id($link, $id){
		$sql = "UPDATE UTILISATEUR SET pseudo = ?;";
		if($stmt = mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt,"s",$param_id);
			$param_id = $id;
			if(mysqli_stmt_execute($stmt)){
				// Do confirm stuff...
				// echo "username modified !";

				// Changing user session
				$SESSION["username"] = $id;

			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		} else {
			echo "[ERROR] - ".$link->error;
		}
	}

	function checkIfReciepeInBasket($id_reciepe, $id_user){
		if($id_user != -1){
			$link = connectToDatabase();
			$b = $GLOBALS["base"];
			query($link,"USE $b");
			$query = "SELECT id_utilisateur FROM PANIER WHERE (id_utilisateur='$id_user' AND id_recette='$id_reciepe');";
			$result = mysqli_query($link, $query);
			$checkrows = mysqli_num_rows($result);
			mysqli_close($link);

			return !($checkrows==0);
		} else {
			if(!isset($_SESSION["favorites"])){
				return false;
			} 
			foreach($_SESSION["favorites"] as $value){
				if($value==$id_reciepe){
					return true;
				}
			}
			return false;
		}
	}


	function addToFavorites($id_reciepe, $id_user){
		// -> if user is logged in
		if($id_user != -1){
			$link = connectToDatabase();
			$b = $GLOBALS["base"];
			query($link, "USE $b");
			$Sql = "INSERT INTO PANIER (id_utilisateur, id_recette) VALUES ($id_user,$id_reciepe)";
			query($link,$Sql);
			mysqli_close($link);
		
		// -> if the user is not logged in
		} else {
			if(!isset($_SESSION["favorites"])){
				$_SESSION["favorites"] = array();
			}
			array_push($_SESSION["favorites"],$id_reciepe);
		}
		header("Location: favoris.php");
		exit();
	}

	function removeFromFavorites($id_reciepe, $id_user){
		if($id_user != -1){
			$link = connectToDatabase();
			$b = $GLOBALS["base"];
			query($link, "USE $b");
			$Sql = "DELETE FROM PANIER WHERE $id_user=id_utilisateur AND $id_reciepe=id_recette";
			query($link,$Sql);
			mysqli_close($link);
		} else {
			if(isset($_SESSION["favorites"])){
				foreach($_SESSION["favorites"] as $key => $value){
					if($value==$id_reciepe){
						unset($_SESSION["favorites"][$key]);
						$_SESSION["favorites"] = array_values($_SESSION["favorites"]);
					}
				}
			}
		}
		header("Location: favoris.php");
		exit();
	}
?>