<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
	require_once "../configuration/conect.php";

// Check if 'idf' parameter is set in the URL
if (isset($_GET['iduserC'])) {
    $iduser = (int)$_GET['iduserC'];

    // Prepare the delete statement
    $sql = "DELETE FROM utilisateur WHERE iduser = :iduser";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':iduser', $iduser, PDO::PARAM_INT);

    // Execute the delete statement
    if ($stmt->execute()) {
        // Redirect back to the product list with a success message
        header("Location: utilisateur.php?message=Categorie supprimer avec succès!");
        exit;
    } else {
        // Handle error
        header("Location: utilisateur.php?message=Impossible de supprimer cet enregistrement!");
        // echo "Impossible de supprimer cet enregistrement.";
    }
} else {
    // Redirect back if no id is provided
   header("Location: utilisateur.php");
    exit;
}
}else{
    header('location:../pages/login.php');
}
?>