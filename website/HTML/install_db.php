<?php // Création de la base de données

	require "Donnees.inc.php";

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
			CREATE TABLE SUPERCATEGORIE(
				id_ingredient INT,
				id_ingredientsupercategorie INT,
				FOREIGN KEY (id_ingredient) REFERENCES INGREDIENTS(id_ingredient),
				FOREIGN KEY (id_ingredient) REFERENCES INGREDIENTS(id_ingredient)
			);
			CREATE TABLE SOUSCATEGORIE(
				id_ingredient INT,
				id_ingredientsouscategorie INT,
				FOREIGN KEY (id_ingredient) REFERENCES INGREDIENTS(id_ingredient),
				FOREIGN KEY (id_ingredient) REFERENCES INGREDIENTS(id_ingredient)
			);";

		//DEBUG
		//echo $Sql."</br>";

		foreach(explode(';',$Sql) as $Requete) query($link,$Requete);
	}

	function implementData($link, $Recettes, $Hierarchie){
		// Recettes
		foreach($Recettes as $key => $value){
			// Special chars
			$titre = str_replace('"','""',$value['titre']);
			$titre = str_replace("'","''",$titre);

			$ingredients = str_replace('"','""',$value['ingredients']);
			$ingredients = str_replace("'","''",$ingredients);

			$preparation = str_replace('"','""',$value['preparation']);
			$preparation = str_replace("'","''",$preparation);


			// Easy values
			$Sql = "INSERT INTO RECETTES VALUES ($key,";
			$Sql = $Sql."'".$titre."',";
			$Sql = $Sql."'".$ingredients."',";
			$Sql = $Sql."'".$preparation."');";

			//DEBUG
			//echo $Sql."</br>";
			query($link, $Sql);

			// Harder values
			// -> index values
			foreach($value['index'] as $keyindex => $valueindex){

				// -- Ingredients table -- //
				$valuechanged = str_replace('"','""',$valueindex);
				$valuechanged = str_replace("'","''",$valuechanged);
				$query = "SELECT (id_ingredient) FROM INGREDIENTS WHERE (nom_ingredient='$valuechanged');";

				//DEBUG
				//echo $query."</br>";

				$result = mysqli_query($link, $query);
				$checkrows = mysqli_num_rows($result);

				if($checkrows == 0){
					//DEBUG
					//echo "$valueindex is not in Ingredients !"."</br>";
					$Sql = "INSERT INTO INGREDIENTS (nom_ingredient) VALUES ('$valuechanged');";
					query($link, $Sql);
				}
				mysqli_free_result($result);

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
		foreach($Hierarchie as $key => $value){
			
		}
	}

	$link=mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
	#Creation of database
	buildDatabase($link);
	#Adding data
	implementData($link, $Recettes, $Hierarchie);
	mysqli_close($link);

?>
