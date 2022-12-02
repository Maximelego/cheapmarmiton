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
			echo "<h1>".utf8_encode($index[1])."</h1>";

			$image_name = scanTitle($index[1]);
			if (file_exists("../ressources/Photos/$image_name.jpg")) {
				echo "<img src=\"../ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/>"."</br>";
			} else {
				echo "<img src=\"../ressources/Photos/DEFAULT.jpg\" alt=\"DEFAULT\"/>";
			}
			echo "<h2>"."Ingrédients : "."</h2>";
			echo "<ul>";
			foreach(explode("|",$index[2]) as $str){
				echo "<li>".utf8_encode($str)."</li>";
			}
			echo "</ul>";
			echo "<h2>"."Recette : "."</h2>";
			echo "<h3>".utf8_encode($index[3])."</h3>";

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
