<?php 
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php";

$iduser=isset($_GET['iduserC'])?$_GET['iduserC']:0;
$etat=isset($_GET['etat'])?$_GET['etat']:0;

if($etat==1)
	$newEtat=0;
else
	$newEtat=1;
$requete="update utilisateur set etat=? where iduser=?";
$params=array($newEtat,$iduser);


$result=$bdd->prepare($requete);
$result->execute($params);
header('location:utilisateur.php');
}else
{
	header('location:../pages/login.php');
}

?>