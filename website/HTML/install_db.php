<?php // Création de la base de données

	require "Donnees.inc.php";

	function transformStringToSQLCompatible($string){
		$valuechanged = str_replace('"','""',$string);
		$valuechanged = str_replace("'","''",$valuechanged);
		return $valuechanged;
	}

	function checkIfElementExists($link,$element,$table){
		$found = true;
		if(strcmp($table,"INGREDIENTS") == 0){
			$query = "SELECT id_ingredient FROM $table WHERE (nom_ingredient='$element');";
		} else if(strcmp($table,"ELEMENTCATEGORIE") == 0) {
			$query = "SELECT id_element FROM $table WHERE (nom_element='$element');";
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

	function buildDatabase($link){
		$base="BDD_Marmiton";
		$Sql="DROP DATABASE IF EXISTS $base;
			CREATE DATABASE $base;
			USE $base;
			CREATE TABLE INGREDIENTS(
				id_ingredient INT PRIMARY KEY AUTO_INCREMENT,
				nom_ingredient VARCHAR(2000)
			);
			CREATE TABLE RECETTES(
				id_recette INT PRIMARY KEY,
				titre VARCHAR(2000),
				ingredients VARCHAR(2000),
				preparation VARCHAR(2000)
			);
			CREATE TABLE RECETTECONTIENTINGREDIENT(
				id_recette INT,
				id_ingredient INT,
				FOREIGN KEY (id_recette) REFERENCES RECETTES(id_recette),
				FOREIGN KEY (id_ingredient) REFERENCES INGREDIENTS(id_ingredient)
			);
			CREATE TABLE ELEMENTCATEGORIE(
				id_element INT PRIMARY KEY AUTO_INCREMENT,
				nom_element VARCHAR(2000)
			);
			CREATE TABLE SUPERCATEGORIE(
				id_element INT,
				id_elementsupercategorie INT,
				FOREIGN KEY (id_element) REFERENCES ELEMENTCATEGORIE(id_element),
				FOREIGN KEY (id_element) REFERENCES ELEMENTCATEGORIE(id_element)
			);
			CREATE TABLE SOUSCATEGORIE(
				id_element INT,
				id_elementsouscategorie INT,
				FOREIGN KEY (id_element) REFERENCES ELEMENTCATEGORIE(id_element),
				FOREIGN KEY (id_element) REFERENCES ELEMENTCATEGORIE(id_element)
			);";

		//DEBUG
		//echo $Sql."</br>";

		foreach(explode(';',$Sql) as $Requete) query($link,$Requete);
	}

	function implementData($link, $Recettes, $Hierarchie){
		// Recettes
		foreach($Recettes as $key => $value){
			// Special chars
			$titre = transformStringToSQLCompatible($value['titre']);
			$ingredients = transformStringToSQLCompatible($value['ingredients']);
			$preparation = transformStringToSQLCompatible($value['preparation']);

			// Easy values
			$Sql = "INSERT INTO RECETTES VALUES ($key,"."'".$titre."',"."'".$ingredients."',"."'".$preparation."');";
			query($link, $Sql);

			// Harder values
			// -> index values
			foreach($value['index'] as $keyindex => $valueindex){
				$valuechanged = transformStringToSQLCompatible($valueindex);
				if(!checkIfElementExists($link, $valuechanged, 'INGREDIENTS')){
					$Sql = "INSERT INTO INGREDIENTS (nom_ingredient) VALUES ('$valuechanged');";
					query($link, $Sql);
				}

				// -- Link table for index -- //
				$query = "SELECT (id_ingredient) FROM INGREDIENTS WHERE (nom_ingredient='$valuechanged');";
				$result = mysqli_query($link, $query);
				$index = mysqli_fetch_row($result);
				$Sql = "INSERT INTO RECETTECONTIENTINGREDIENT VALUES ($key,$index[0]);";
				query($link, $Sql);
				mysqli_free_result($result);
			}
		}

		// Hiérarchie
		foreach($Hierarchie as $element => $caracteristiques){

			$valuechanged = transformStringToSQLCompatible($element);
			if(!checkIfElementExists($link, $valuechanged,'ELEMENTCATEGORIE')){
				$Sql = "INSERT INTO ELEMENTCATEGORIE (nom_element) VALUES ('$valuechanged');";
				query($link, $Sql);
			}

			if(array_key_exists("sous_categorie",$caracteristiques)){
				$souscat = $caracteristiques["sous-categorie"];
				
			}
			if(array_key_exists("super_categorie",$caracteristiques)){
				$surcat = $caracteristiques["super-categorie"];

			}
		}
	}




	// -- MAIN PROGRAM -- //
	$link=mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
	#Creation of database
	buildDatabase($link);
	#Adding data
	implementData($link, $Recettes, $Hierarchie);
	mysqli_close($link);
	// ----------------- //

?>
