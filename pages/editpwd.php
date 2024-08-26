<?php
require_once(__DIR__ . '/../pages/identifier.php');
require_once "../configuration/conect.php";

$erreurs = "";
$login = "";

// Vérifier si l'ID de l'utilisateur est passé dans l'URL
if (isset($_GET['iduserC'])) {
    $iduserC = $_GET['iduserC'];

    // Requête pour récupérer le login de l'utilisateur sélectionné
    $stmt = $bdd->prepare("SELECT login FROM utilisateur WHERE iduser = ?");
    $stmt->execute([$iduserC]);
    $user = $stmt->fetch();

    if ($user) {
        $login = $user['login']; // Stocker le login pour l'afficher dans le formulaire
    } else {
        $erreurs = "Utilisateur non trouvé.";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ancien_password = sha1($_POST['ancien_password']); // Hash l'ancien mot de passe saisi
        $nouveau_password = sha1($_POST['nouveau_password']); // Hash le nouveau mot de passe saisi

        // Requête pour récupérer le mot de passe actuel de l'utilisateur sélectionné
        $stmt = $bdd->prepare("SELECT pwd FROM utilisateur WHERE iduser = ?");
        $stmt->execute([$iduserC]);
        $user = $stmt->fetch();

        // Vérifier si l'ancien mot de passe correspond à celui de la base de données
        if ($user && $user['pwd'] === $ancien_password) {
            // Mise à jour du mot de passe avec le nouveau
            $stmt = $bdd->prepare("UPDATE utilisateur SET pwd = ? WHERE iduser = ?");
            if ($stmt->execute([$nouveau_password, $iduserC])) {
                $erreurs = "Mot de passe modifié avec succès.";
            } else {
                $erreurs = "Erreur lors de la mise à jour du mot de passe.";
            }
        } else {
            $erreurs = "Ancien mot de passe incorrect.";
        }
    }
} else {
    $erreurs = "Aucun utilisateur sélectionné.";
}
?>

<?php require "../include/sidebar.php"; ?>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <?php if (!empty($erreurs)) { ?>
            <div class="alert alert-warning"> 
                <strong>Message!</strong>  
                <?php echo $erreurs; ?>
            </div>
        <?php } ?>
        <div class="row">
           <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">Modifier Mot de passe</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <!-- Afficher le login de l'utilisateur sélectionné -->
                    <label for="exampleFormControlInput1" class="form-label">Compte sélectionné :&nbsp;<?php echo htmlspecialchars($login); ?></label>
                    <div class="mb-3 row">
                        <label for="ancien_password" class="col-sm-2 col-form-label">Taper ancien mot de passe</label>
                        <div class="col-sm-10">
                          <input type="password" name="ancien_password" class="form-control" placeholder="Taper ancien mot de passe" required>
                      </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="nouveau_password" class="col-sm-2 col-form-label">Taper votre nouveau mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" name="nouveau_password" class="form-control" placeholder="Taper votre nouveau mot de passe" required>
                  </div>
              </div>
              <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-secondary mb-3">Modifier le mot de passe</button>
                <a href="utilisateur.php" class="btn btn-outline-warning mb-3">Retour</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</main>
<?php require "../include/footer.php"; ?>
