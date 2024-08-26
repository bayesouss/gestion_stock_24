<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
if(isset($_POS['ok'])){
	$nom=htmlspecialchars($_POST['nom']) ;
	$prenom=htmlspecialchars($_POST['prenom']) ;
	$adresse=htmlspecialchars($_POST['adresse']) ;
	$telephone=htmlspecialchars($_POST['telephone']);
	$email=htmlspecialchars($_POST['email']);
	if(!empty($nom) || !empty($prenom) || !empty($adresse) || !empty($telephone) || !empty($email)){

		$erreurs="Veuillez remplir tous les champs";
	}
	 if(filter_var($email,FILTER_VALIDATE_EMAIL)){

	 	$erreurs="Votre email n'est pas valide ";
	 }

	 if(empty($erreurs)){
	 	require"../configuration/conect.php";

	 }
}
}else{
	header('location:../pages/login.php');
}

  ?>