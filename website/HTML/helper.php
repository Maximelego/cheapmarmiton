
<?php
    $base="BDD_marmiton";

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

    function query($link,$Sql){
		if(isset($Sql) && strcmp($Sql,"") != 0){
			$resultat=mysqli_query($link,$Sql) or die("$Sql : ".mysqli_error($link));
		    return($resultat);
		} 
	    return 0;
	}

    function scanTitle($title){
        $formattedTitle = str_replace(" ","_", $title);
        return $formattedTitle;
    }

	function connectToDatabase(){
        $link = mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
        return $link;
	}

    function transformStringToSQLCompatible($link,$string){
		return mysqli_real_escape_string($link,$string);
	}
?>