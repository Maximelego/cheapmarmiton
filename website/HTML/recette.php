<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Cheap Marmiton | Recettes de cocktails</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="Style/style.css?v=1">
		<?php require "helper.php"; ?>
	</head>
		<?php
			$id = $_GET['id_recette'];
			$link = connectToDatabase();
			$Sql = "USE $base";
			query($link, $Sql);
			$Sql = "SELECT * FROM RECETTES WHERE id_recette=$id;";
			$result = query($link,$Sql);
			$index = mysqli_fetch_row($result);

			// -- Classical values -- //
			foreach($index as $key => $value){
				if($key != 0){
					if($key ==  1){
						echo "<h1>".$value."</h1>";
					} else if($key == 2){
						echo "<h2>"."Ingrédients : "."</h2>";
						echo "<ul>";
						foreach(explode("|",$value) as $str){
							echo "<li>".$str."</li>";
						}
						echo "</ul>";
					} else if($key == 3){
						echo "<h2>"."Recette : "."</h2>";
						echo "<h3>".$value."</h3>";
					}
				}
			}

			// -- Ingredients list -- //
			/*echo "<ul>";
			$Sql = "SELECT * FROM RECETTECONTIENTINGREDIENT INNER JOIN INGREDIENTS USING (id_ingredient) WHERE id_recette=$id;";
			$result = query($link,$Sql);
			while($index = mysqli_fetch_row($result)){
				echo "<li>".$index[2]."</li>"."</br>";
			}
			echo "</ul>";*/

		?>
	</body>
</html>
