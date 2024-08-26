<?php 

function  recherche_par_login($login){
	global $bdd;
	$requete=$bdd->prepare("select * from utilisateur where login = ?");
	$requete->execute(array($login));
	return $requete->rowCount();
}

function  recherche_par_email($email){
	global $bdd;
	$requete=$bdd->prepare("select * from utilisateur where email = ?");
	$requete->execute(array($email));
	return $requete->rowCount();
}


?>