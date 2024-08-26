<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
	require_once "../configuration/conect.php";

// Check if 'idf' parameter is set in the URL
if (isset($_GET['idP'])) {
    $id_produit = (int)$_GET['idP'];

    // Prepare the delete statement
    $sql = "DELETE FROM produit WHERE id_produit = :id_produit";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);

    // Execute the delete statement
    if ($stmt->execute()) {
        // Redirect back to the product list with a success message
        header("Location: produit.php?message=Produit supprimer avec succès!");
        exit;
    } else {
        // Handle error
        
        echo "Impossible de supprimer cet enregistrement.";
    }
} else {
    // Redirect back if no id is provided
    header("Location: produit.php");
    exit;
}
}else{
    header('location:../pages/login.php');
}
?>


