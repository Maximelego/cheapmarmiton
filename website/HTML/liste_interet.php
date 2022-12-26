<?php
// debut session
session_start();

//connexion a la bdd
try {
    $bdd = new PDO('mysql:host=localhost;dbname=bdd_marmiton;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


$term = $_GET['term'];

$requete = $bdd->prepare('SELECT * FROM recettes WHERE titre LIKE :term'); // j'effectue ma requête SQL grâce au mot-clé LIKE
$requete->execute(array('term' => '%' . $term . '%'));

$array = array(); // on créé le tableau

while ($donnee = $requete->fetch()) // on effectue une boucle pour obtenir les données
{
    array_push($array, $donnee['titre']); // et on ajoute celles-ci à notre tableau
}

echo json_encode($array); // il n'y a plus qu'à convertir en JSON
