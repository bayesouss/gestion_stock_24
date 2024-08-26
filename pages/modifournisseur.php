<?php 
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php"; 

$idf = isset($_GET['idf']) ? (int)$_GET['idf'] : 0;
$id_fournisseur = 0;
$nom = '';
$prenom = '';
$telephone = '';
$email = '';

if ($idf) {
    $reqc = "SELECT * FROM fournisseur WHERE id_fournisseur = ?";
    $stmt = $bdd->prepare($reqc);
    $stmt->execute([$idf]);
    $fournisseur = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fournisseur) {
        $id_fournisseur = $fournisseur['id_fournisseur'];
        $nom = $fournisseur['nom'];
        $prenom = $fournisseur['prenom'];
        $telephone = $fournisseur['telephone'];
        $email = $fournisseur['email'];
    }
}

// Requête de mise à jour
if (isset($_POST['edit'])) {
    $id_fournisseur = isset($_POST['id_fournisseur']) ? (int)$_POST['id_fournisseur'] : null;
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $email = htmlspecialchars($_POST['email']);

    if (!empty($nom) && !empty($prenom) && !empty($telephone) && !empty($email)) {
        try {
            if ($id_fournisseur) {
                // Update fournisseur
                $reqcat = $bdd->prepare("UPDATE fournisseur SET nom = ?, prenom = ?, telephone = ?, email = ? WHERE id_fournisseur = ?");
                $reqcat->execute([$nom, $prenom, $telephone, $email, $id_fournisseur]);
                $erreurs = "fournisseur modifié avec succès!";
                header("Location: fournisseur.php?message=fournisseur modifié avec succès!");
                exit;
            } else {
                // Insert fournisseur
                $reqfournisseur = $bdd->prepare("INSERT INTO fournisseur (nom, prenom, telephone, email) VALUES (?, ?, ?, ?)");
                $reqfournisseur->execute([$nom, $prenom, $telephone, $email]);
                header("Location: fournisseur.php?message=fournisseur bien enregistré avec succès!");
                exit;
            }
        } catch (Exception $e) {
            $erreurs = "Erreur lors de la mise à jour: " . $e->getMessage();
        }
    } else {
        $erreurs = "Échec de l'enregistrement. Tous les champs sont obligatoires.";
    }
}
}else{
    header('location:../pages/login.php');
}

require "../include/sidebar.php"; 
?>

<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Admin Dashboard</h4>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill border-0 illustration">
                    <div class="card-body p-0 d-flex flex-fill">
                        <div class="row g-0 w-100">
                            <div class="col-6">
                                <div class="p-3 m-1">
                                    <h4>Welcome Back, Admin</h4>
                                    <p class="mb-0">Admin Dashboard, CodzSword</p>
                                </div>
                            </div>
                            <div class="col-6 align-self-end text-end">
                                <img src="image/customer-support.jpg" class="img-fluid illustration-img" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill border-0">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h4 class="mb-2">
                                    $ 78.00
                                </h4>
                                <p class="mb-2">
                                    Total Earnings
                                </p>
                                <div class="mb-0">
                                    <span class="badge text-success me-2">
                                        +9.0%
                                    </span>
                                    <span class="text-muted">
                                        Since Last Month
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($erreurs)) { ?>
            <div class="alert alert-warning"> 
                <strong>Message!</strong>  
                <?php echo $erreurs; ?>
            </div>
        <?php } ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Add/Edit fournisseur
                </h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3 row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Id:</label>
                        <div class="col-sm-10">
                            :<?php echo $id_fournisseur ?>
                            <input type="hidden" class="form-control" id="id_fournisseur" name="id_fournisseur" value="<?php echo $id_fournisseur ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nom" class="col-sm-2 col-form-label">Nom</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $nom ?>" class="form-control" id="nom" name="nom" placeholder="Nom">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="prenom" class="col-sm-2 col-form-label">Prenom</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $prenom ?>" name="prenom" class="form-control" id="prenom" placeholder="Prenom">
                        </div>
                    </div> 
                    <div class="mb-3 row">
                        <label for="telephone" class="col-sm-2 col-form-label">Telephone</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $telephone ?>" name="telephone" class="form-control" id="telephone" placeholder="Telephone">
                        </div>
                    </div> 
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $email ?>" name="email" class="form-control" id="email" placeholder="Email">
                        </div>
                    </div> 
                    <div class="mb-3 row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Mettre a jour </label>
                        <div class="col-sm-10">
                            <button type="submit" name="edit" class="btn btn-secondary mb-3">Modifier</button>
                            <a class="btn btn-warning mb-3" href="fournisseur.php">Retour</a>
                        </div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</main>
<?php require "../include/footer.php"; ?>
