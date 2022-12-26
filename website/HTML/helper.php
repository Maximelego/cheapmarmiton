
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
		$formattedTitle = str_replace($unwanted_array,$wanted_array,$title);
		$formattedTitle = str_replace(" ","_", $formattedTitle);
        return $formattedTitle;
    }

	function connectToDatabase(){
        $link = mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
		$link->set_charset("utf8mb4");
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
		$sql = "UPDATE UTILISATEUR SET mdp = ? WHERE id_utilisateur = ?;";
		if($stmt = mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt,"si",$param_password,$param_id_utilisateur);
			$param_id_utilisateur = $_SESSION["id"];
			$param_password = password_hash($password,PASSWORD_DEFAULT);
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

	function update_id($link, $pseudo){
		$sql = "UPDATE UTILISATEUR SET pseudo = ? WHERE id_utilisateur = ?;";
		if($stmt = mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt,"si",$param_pseudo,$param_id_utilisateur);
			$param_id_utilisateur = $_SESSION["id"];
			$param_pseudo = $pseudo;
			if(mysqli_stmt_execute($stmt)){
				// Do confirm stuff...
				// echo "username modified !";

				// Changing user session
				$SESSION["username"] = $pseudo;

			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		} else {
			echo "[ERROR] - ".$link->error;
		}
	}

	function update_personnal_infos($link,$info_array){
		$id = $_SESSION["id"];
		$sql = "SELECT nom,prenom,mail,sexe,num_rue,nom_rue,ville,code_postal FROM UTILISATEUR WHERE id_utilisateur=$id;";
		$result = query($link, $sql);
		if(!(mysqli_num_rows($result) == 0)){
			$index = mysqli_fetch_array($result,MYSQLI_ASSOC);

			$sql = "UPDATE UTILISATEUR SET nom = ?,prenom = ?,mail = ?,sexe = ?,num_rue = ?,nom_rue = ?,ville = ?,code_postal = ? WHERE id_utilisateur=$id;";
			$stmt = mysqli_prepare($link, $sql);
			mysqli_stmt_bind_param($stmt, "ssssissi",$name_param, $firstname_param, $mail_param, $sex_param, $num_rue_param, $nom_rue_param, $ville_param,$code_postal_param);
			// Name
			if(!empty($info_array["name"])){
				$name_param = $info_array["name"];
			} else {
				$name_param = $index["name"];
			}
			// Firstname
			if(!empty($info_array["firstname"])){
				$firstname_param = $info_array["firstname"];
			} else {
				$firstname_param = $index["firstname"];
			}

			// Mail
			if(!empty($info_array["mail"])){
				$mail_param = $info_array["mail"];
			} else {
				$mail_param = $index["mail"];
			}

			// Sex
			if(!empty($info_array["sex"])){
				$sex_param = $info_array["sex"];
			} else {
				$sex_param = $index["sexe"];
			}

			// Address
			if(!empty($info_array["num_rue"]) && !empty($info_array["nom_rue"]) && !empty($info_array["ville"]) && !empty($info_array["code_postal"])){
				$num_rue_param = $info_array["num_rue"];
				$nom_rue_param = $info_array["nom_rue"];
				$ville_param = $info_array["ville"];
				$code_postal_param = $info_array["code_postal"];
			} else {
				$num_rue_param = $index["num_rue"];
				$nom_rue_param = $index["nom_rue"];
				$ville_param = $index["ville"];
				$code_postal_param = $index["code_postal"];
			}
			// Attempt to execute the prepared statement
			if (mysqli_stmt_execute($stmt)) {
				// Do confirm stuff ...
				echo "Changes applied successfully !";
			} else {
				echo "Oops! Something went wrong. Please try again later.";
				echo "[ERROR] - " . $link->error;
			}
			mysqli_stmt_close($stmt);
		} else {
			echo "Oops! Something went wrong. Please try again later.";
		}
		mysqli_free_result($result);
	}

	function checkIfReciepeInBasket($id_reciepe, $id_user){
		if($id_user != -1){
			$link = connectToDatabase();
			$b = $GLOBALS["base"];
			query($link,"USE $b");
			$query = "SELECT id_utilisateur FROM PANIER WHERE (id_utilisateur='$id_user' AND id_recette='$id_reciepe');";
			$result = mysqli_query($link, $query);
			$checkrows = mysqli_num_rows($result);
			mysqli_free_result($result);
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

	function displayReciepeList($image_name, $id_reciepe, $title){
		echo "<div class=\"box\">";
		echo "<a href=\"recette.php?id_recette=$id_reciepe\">";
		// Comparaison avec la liste d'images disponibles.
		if(file_exists("./ressources/Photos/$image_name.jpg")){
			echo "<img src=\"./ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/>" . "</br>";
		} else {
			echo "<img src=\"./ressources/Img/DEFAULT.png\" alt=\"DEFAULT\"/>" . "</br>";
		}
		echo "<h2>" . $title . "</h2>" . "</br>";
		echo "</a>";
		echo "</div>";
	}

	function requestAllSubElements($link,$id_element){
		$sql = "SELECT id_element,nom_element
		FROM ELEMENTCATEGORIE
		WHERE id_element IN (
			SELECT id_elementsouscategorie
			FROM SOUSCATEGORIE
			WHERE id_element=$id_element
		);";

		return query($link,$sql);
	}

	function queryAllReciepieFromElementID($link,$id_element){
		$sql = "SELECT r.*
		FROM RECETTES r
		JOIN RECETTECONTIENTINGREDIENT rci ON r.id_recette = rci.id_recette
		JOIN INGREDIENTS i ON rci.id_ingredient = i.id_ingredient
		JOIN ELEMENTCATEGORIE ec ON i.id_ingredient = ec.id_element
		JOIN SUPERCATEGORIE sc ON ec.id_element = sc.id_elementsupercategorie
		JOIN SOUSCATEGORIE ss ON ec.id_element = ss.id_elementsouscategorie
		WHERE sc.id_elementsupercategorie = $id_element OR ss.id_elementsouscategorie = $id_element;";
		return query($link,$sql);
	}
?>