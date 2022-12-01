
<?php
    $base="BDD_marmiton";

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