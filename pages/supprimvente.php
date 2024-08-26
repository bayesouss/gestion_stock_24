<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
	require_once "../configuration/conect.php";

// Check if 'idf' parameter is set in the URL
if (isset($_GET['idV'])) {
    $id_vente = (int)$_GET['idV'];

    // Prepare the delete statement
    $sql = "DELETE FROM vente WHERE id_vente = :id_vente";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':id_vente', $id_vente, PDO::PARAM_INT);

    // Execute the delete statement
    if ($stmt->execute()) {
        // Redirect back to the product list with a success message
        header("Location: vente.php?message=Vente supprimer avec succès!");
        exit;
    } else {
        // Handle error
        
        echo "Impossible de supprimer cet enregistrement.";
    }
} else {
    // Redirect back if no id is provided
    header("Location: vente.php");
    exit;
}
}else{
    header('location:../pages/login.php');
}
?>


