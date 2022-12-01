<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Cheap Marmiton | Recettes de cocktails</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="Style/style.css?v=1">
		<?php require "helper.php"; ?>
	</head>
	<body>
		<header class="main-head">
			<nav>
			<div class="header">
				<img src="../ressources/Img/logoCM4.jpg" alt="logo" />
			</div>
				<h1 id="logo">CheapMarmiton</h1>
				<ul>
					<li><a href="#">Se connecter</a> </li>
					<li><a href="#">Mes favoris</a> </li>
				</ul>
			</nav>
		</header>

		<div class="searchbar">
			<p>Rechercher</p>
			<input type="text">
			<img src="../ressource/Img/icons/search.svg">
		</div>

		<div class="wrapper">
			<?php
				$link=connectToDatabase();
				query($link, "USE $base;");
				$Sql="SELECT * FROM RECETTES;";
				$result=query($link,$Sql);

				while($index=mysqli_fetch_row($result)){
					$image_name = scanTitle($index[1]);
					if(file_exists("../ressources/Photos/$image_name.jpg")){
						echo "<div class=\"box\">";
						echo "<img src=\"../ressources/Photos/$image_name.jpg\" alt=\"$image_name\"/>"."</br>";
						echo "<h2>"."$index[1]"."</h2>"."</br>";
						echo "</div>";
					}
				}
				mysqli_close($link);
			?>
		</div>
	</body>
</html>
