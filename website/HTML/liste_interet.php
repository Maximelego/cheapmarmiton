<?php
// debut session
require "helper.php";
session_start();

//connexion a la bdd
try {
    $ip = $GLOBALS["ip"];
    $username = $GLOBALS["username"];
    $password = $GLOBALS["password"];
    $bdd = new PDO("mysql:host=$ip;dbname=BDD_marmiton;charset=utf8", $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    echo $e->getMessage();
}


$term = $_GET['term'];

$requete = $bdd->prepare('SELECT * FROM RECETTES WHERE titre LIKE :term'); // j'effectue ma requête SQL grâce au mot-clé LIKE
$requete->execute(array('term' => '%' . $term . '%')); // <- caca

$array = array(); // on créé le tableau

while ($donnee = $requete->fetch()) // on effectue une boucle pour obtenir les données
{
    array_push($array, $donnee['titre']); // et on ajoute celles-ci à notre tableau
}

echo json_encode($array); // il n'y a plus qu'à convertir en JSON
