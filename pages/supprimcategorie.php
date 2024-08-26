<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
	require_once "../configuration/conect.php";

// Check if 'idf' parameter is set in the URL
if (isset($_GET['idC'])) {
    $id_categorie = (int)$_GET['idC'];

    // Prepare the delete statement
    $sql = "DELETE FROM categorie WHERE id_categorie = :id_categorie";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':id_categorie', $id_categorie, PDO::PARAM_INT);

    // Execute the delete statement
    if ($stmt->execute()) {
        // Redirect back to the product list with a success message
        header("Location: categorie.php?message=Categorie supprimer avec succès!");
        exit;
    } else {
        // Handle error
        header("Location: categorie.php?message=Impossible de supprimer cet enregistrement!");
        // echo "Impossible de supprimer cet enregistrement.";
    }
} else {
    // Redirect back if no id is provided
   header("Location: categorie.php");
    exit;
}
}else{
    header('location:../pages/login.php');
}
?>