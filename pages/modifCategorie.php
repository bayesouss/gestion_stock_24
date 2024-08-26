<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php"; 

$idC = isset($_GET['idC']) ? (int)$_GET['idC'] : 0;
$nom_categorie = '';
$id_categorie = 0;

if ($idC) {
    $reqc = "SELECT * FROM categorie WHERE id_categorie = ?";
    $stmt = $bdd->prepare($reqc);
    $stmt->execute([$idC]);
    $categorie = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($categorie) {
        $id_categorie = $categorie['id_categorie'];
        $nom_categorie = $categorie['nom_categorie'];
    }
}

// Requête de mise à jour
if (isset($_POST['edit'])) {
    $nom_categorie = htmlspecialchars($_POST['nom_categorie']);
    $id_categorie = isset($_POST['id_categorie']) ? (int)$_POST['id_categorie'] : null;

    if (!empty($nom_categorie)) {
        if ($id_categorie) {
            // Update category
            $reqcat = $bdd->prepare("UPDATE categorie SET nom_categorie = ? WHERE id_categorie = ?");
            $reqcat->execute([$nom_categorie, $id_categorie]);
              header("Location: categorie.php?message=Categorie modifier avec sucées!");
            
            
             
        } else {
            // Insert category
            $reqCategorie = $bdd->prepare("INSERT INTO categorie (nom_categorie) VALUES (?)");
            $reqCategorie->execute([$nom_categorie]);

             header("Location: categorie.php?message=Categorie supprimer avec succès!");
            
        }
        header('location:categorie.php');
        exit;
    } else {
        $erreurs = "Please fill in all fields";
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

        <div class="col-sm-4">       
            <div class="card border-0">
                <div class="card-header">
                    <h5 class="card-title">
                        Add/Edit Category
                    </h5>
                </div>
               
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Id: <?php echo $id_categorie ?></label>
                            <input type="hidden" class="form-control" id="exampleFormControlInput1" name="id_categorie" value="<?php echo $id_categorie ?>">
                        </div> 
                        <div class="mb-3">                            	
                            <label for="exampleFormControlInput1" class="form-label">Nom Categorie</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" name="nom_categorie" placeholder="Name" value="<?php echo $nom_categorie ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="edit" class="btn btn-secondary mb-3">Modifier</button>
                            <button type="reset" class="btn btn-warning mb-3">Reinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require "../include/footer.php"; ?>
