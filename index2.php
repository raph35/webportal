<?php 

	require("core/Routeur.php");
	$routeur=new Routeur();
	$etudiant=new Etudiant();
	$action=$etudiant->authentification();
	if (method_exists($routeur, $action)) {
		$routeur->$action($etudiant);
	}else
	{
		echo"<h1>action not found</h1>";
	}
 ?>