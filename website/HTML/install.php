<?php // Création de la base de données

	require "Donnees.inc.php";
	require "helper.php";

	function addElementToTable($link, $element, $table){
		if(strcmp($table,"INGREDIENTS") == 0){
			$Sql = "INSERT INTO INGREDIENTS (nom_ingredient) VALUES ('$element');";
			query($link, $Sql);
		} else if(strcmp($table,"ELEMENTCATEGORIE") == 0) {
			$Sql = "INSERT INTO ELEMENTCATEGORIE (nom_element) VALUES ('$element');";
			query($link, $Sql);
		}
	}

	function buildDatabase($link){
		$base=$GLOBALS["base"];
		$Sql="DROP DATABASE IF EXISTS $base;
			CREATE DATABASE $base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
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
				FOREIGN KEY (id_elementsouscategorie) REFERENCES ELEMENTCATEGORIE(id_element)
			);
			CREATE TABLE UTILISATEUR(
				id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
				pseudo VARCHAR(255),
				mdp VARCHAR(255),
				nom VARCHAR(255),
				prenom VARCHAR(255),
				mail VARCHAR(255)
			);
			CREATE TABLE PANIER(
				id_utilisateur INT,
				id_recette INT,
				FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
				FOREIGN KEY (id_recette) REFERENCES RECETTES(id_recette)
			);";

		//DEBUG
		//echo $Sql."</br>";

		foreach(explode(';',$Sql) as $Requete) query($link,$Requete);
	}

	function implementData($link, $Recettes, $Hierarchie){
		// Recettes
		foreach($Recettes as $key => $value){
			// Special chars
			$titre = transformStringToSQLCompatible($link,$value['titre']);
			$ingredients = transformStringToSQLCompatible($link,$value['ingredients']);
			$preparation = transformStringToSQLCompatible($link,$value['preparation']);

			// Easy values
			$Sql = "INSERT INTO RECETTES VALUES ($key,"."'".$titre."',"."'".$ingredients."',"."'".$preparation."');";
			query($link, $Sql);

			// Harder values
			// -> index values
			foreach($value['index'] as $valueindex){
				$valuechanged = transformStringToSQLCompatible($link,$valueindex);
				if(!checkIfElementExists($link, $valuechanged, 'INGREDIENTS')){
					addElementToTable($link, $valuechanged, "INGREDIENTS");
				}
				// -- Link table for index -- //
				$index = collectObjectID($link, $valuechanged,"INGREDIENTS");
				$Sql = "INSERT INTO RECETTECONTIENTINGREDIENT VALUES ($key,$index[0]);";
				query($link, $Sql);
			}
		}

		// Hiérarchie
		foreach($Hierarchie as $element => $caracteristiques){

			$valuechanged = transformStringToSQLCompatible($link,$element);
			if(!checkIfElementExists($link, $valuechanged,'ELEMENTCATEGORIE')){
				addElementToTable($link, $valuechanged, "ELEMENTCATEGORIE");
			}
			// Fetching the $element key from the table.
			$index = collectObjectID($link,$valuechanged,"ELEMENTCATEGORIE");

			if(array_key_exists("sous-categorie",$caracteristiques)){
				$souscat = $caracteristiques["sous-categorie"];
				foreach($souscat as $key => $value){
					// Searching if the value already exists
					$valuechanged2 = transformStringToSQLCompatible($link,$value);
					if(!checkIfElementExists($link,$valuechanged2,"ELEMENTCATEGORIE")){
						addElementToTable($link, $valuechanged2, "ELEMENTCATEGORIE");
					}
					// Linking
					$index_element_second = collectObjectID($link, $valuechanged2, "ELEMENTCATEGORIE");
					// -> Building link
					$Sql = "INSERT INTO SOUSCATEGORIE VALUES ($index[0],$index_element_second[0]);";
					query($link, $Sql);
				}
			}
			if(array_key_exists("super-categorie",$caracteristiques)){
				$surcat = $caracteristiques["super-categorie"];
				foreach($surcat as $key => $value){
					// Searching if the value already exists
					$valuechanged2 = transformStringToSQLCompatible($link,$value);
					if(!checkIfElementExists($link,$valuechanged2,"ELEMENTCATEGORIE")){
						addElementToTable($link,$valuechanged2,"ELEMENTCATEGORIE");
					}
					// Linking
					$index_element_second = collectObjectID($link, $valuechanged2, "ELEMENTCATEGORIE");
					// -> Building link
					$Sql = "INSERT INTO SUPERCATEGORIE VALUES ($index[0],$index_element_second[0]);";
					query($link, $Sql);
				}
			}
		}

	
		// -- TEST user -- //
		// -> User
		$password = password_hash("password", PASSWORD_DEFAULT);
		$Sql = "INSERT INTO UTILISATEUR (pseudo,mdp,nom,prenom,mail) VALUES ('testuser','$password','test','user','test@gmail.com');";
		query($link,$Sql);
		// -> Panier
		for($i=1; $i<=10; $i++){
			$Sql = "INSERT INTO PANIER (id_utilisateur, id_recette) VALUES (1,$i)";
			query($link,$Sql);
		}

	}



	// -- MAIN PROGRAM -- //
	$link=connectToDatabase();
	#Creation of database
	buildDatabase($link);
	#Adding data
	implementData($link, $Recettes, $Hierarchie);
	echo "<h1>"."Database installation successful !"."</h1>";

	mysqli_close($link);
	// ----------------- //

?>
