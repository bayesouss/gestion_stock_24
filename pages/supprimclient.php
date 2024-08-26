<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php";

// Check if 'idCl' parameter is set in the URL
if (isset($_GET['idCl'])) {
    $id_client = (int)$_GET['idCl'];

    // Prepare the delete statement
    $sql = "DELETE FROM client WHERE id_client = :id_client";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':id_client', $id_client, PDO::PARAM_INT);

    // Execute the delete statement
    if ($stmt->execute()) {
        // Redirect back to the client list with a success message
        header("Location: client.php?message=Client supprimé avec succès!");
        exit;
    } else {
        // Handle error
        $errorInfo = $stmt->errorInfo();
        echo "Erreur lors de la suppression: " . htmlspecialchars($errorInfo[2]);
    }
} else {
    // Redirect back if no id is provided
    header("Location: client.php?message=ID client manquant.");
    exit;
}
}else{
    header('location:../pages/login.php');
}
?>
