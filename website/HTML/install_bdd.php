<?php // Création de la base de données

  function query($link,$requete){
    $resultat=mysqli_query($link,$requete) or die("$requete : ".mysqli_error($link));
		return($resultat);
  }


$mysqli=mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
$base="Recettes";
$Sql="
		DROP DATABASE IF EXISTS $base;
		CREATE DATABASE $base;
		USE $base;
		CREATE TABLE region (id INT AUTO_INCREMENT PRIMARY KEY, lib VARCHAR(255) NOT NULL);
		CREATE TABLE departement (id INT AUTO_INCREMENT PRIMARY KEY, region INT NOT NULL, lib VARCHAR(255) NOT NULL);
		""

foreach(explode(';',$Sql) as $Requete) query($mysqli,$Requete);

mysqli_close($mysqli);
?>
