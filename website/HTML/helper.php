
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

	function checkIfElementExists($link,$element,$table){
		$found = true;
		if(strcmp($table,"INGREDIENTS") == 0){
			$query = "SELECT id_ingredient FROM $table WHERE (nom_ingredient='$element');";
		} else if(strcmp($table,"ELEMENTCATEGORIE") == 0) {
			$query = "SELECT id_element FROM $table WHERE (nom_element='$element');";
		} else if(strcmp($table,"UTILISATEUR") == 0){
			$query = "SELECT id_utilisateur FROM $table WHERE (id_utilisateur='$element');";
		}
		
		$result = mysqli_query($link, $query);
		$checkrows = mysqli_num_rows($result);
		if($checkrows == 0){
			$found = false;
		}
		mysqli_free_result($result);
		return $found;
	}

    function query($link,$Sql){
		if(isset($Sql) && strcmp($Sql,"") != 0){
			$resultat=mysqli_query($link,$Sql) or die("$Sql : ".mysqli_error($link));
		    return($resultat);
		} 
	    return 0;
	}

    function scanTitle($title){
		$wanted_array = array(
			"S", "s", "Z", "z", "A", "A", "A", "A", "A", "A", "A", "C", "E", "E",
			"E", "E", "I", "I", "I", "I", "N", "O", "O", "O", "O", "O", "O", "U",
			"U", "U", "U", "Y", "B", "Ss", "a", "a", "a", "a", "a", "a", "a", "c",
			"e", "e", "e", "e", "i", "i", "i", "i", "o", "n", "o", "o", "o", "o",
			"o", "o", "u", "u", "u", "y", "b", "y"
		);
		$unwanted_array = array(    
			"Š", "š", "Ž", "ž", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É",
			"Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù",
			"Ú", "Û", "Ü", "Ý", "Þ" , "ß", "à", "á", "â", "ã", "ä", "å", "æ", "ç",
			"è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ",
			"ö", "ø", "ù", "ú", "û", "ý", "þ", "ÿ"
		);
		$formattedTitle = str_replace($unwanted_array,$wanted_array,utf8_encode($title));
		$formattedTitle = str_replace(" ","_", $formattedTitle);
        return utf8_encode($formattedTitle);
    }

	function connectToDatabase(){
        $link = mysqli_connect('127.0.0.1', 'root', '') or die("Erreur de connexion");
        return $link;
	}

    function transformStringToSQLCompatible($link,$string){
		return mysqli_real_escape_string($link,$string);
	}
?>