<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php"; 

// Fetch categories
$reqc = "SELECT * FROM categorie";
$result = $bdd->query($reqc);
$categories = $result->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['ok'])) {
    $id_categorie = isset($_POST['categorie_id']) ? (int)$_POST['categorie_id'] : null;
    $nom_produit = htmlspecialchars($_POST['nom_produit']);
      $quantite = htmlspecialchars($_POST['quantite']);
    $description = htmlspecialchars($_POST['description']);
    $photo = $_FILES['photo'];
     $prix_achat = htmlspecialchars($_POST['prix_achat']);
      $prix_vente = htmlspecialchars($_POST['prix_vente']);

    // Check for errors
    if ($id_categorie && !empty($nom_produit) && !empty($description) && $photo['error'] == 0) {
        // Handle photo upload
        $photo_name = basename($photo['name']);
        $upload_dir = '../uploads/';
        $upload_file = $upload_dir . $photo_name;

        if (move_uploaded_file($photo['tmp_name'], $upload_file)) {
            // Insert product into the database
            $reqProduct = $bdd->prepare("INSERT INTO produit (id_categorie, nom_produit, description,quantite, photo,prix_achat,prix_vente) VALUES (?, ?, ?, ?,?,?,?)");
            $reqProduct->execute([$id_categorie, $nom_produit, $description,$quantite, $photo_name, $prix_achat,$prix_vente]);
            $erreurs = "Produit Bien ajouter!";
        } else {
            $erreurs = "impossible de telecharger la photo";
        }
    } else {
        $erreurs = "Veuillez remplir tous les champs";
    }
}
}else{
    header('location:../pages/login.php');
}

require"../include/sidebar.php";
?>

<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Enregistrement Produits</h4>
        </div>
        <div class="row">
           
            
        </div>
      
 <?php if(isset($erreurs)) { ?>
                <div class="alert alert-warning"> 
                    <strong>Message!</strong>  
                    <?php echo $erreurs; ?>
                </div>
            <?php } ?>
        <!-- Table Element -->
        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">
                    Produits
                </h5>
            </div>
           

            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="categorySelect" class="form-label">Categorie</label>
                        <select name="categorie_id" class="form-select" id="categorySelect" aria-label="Default select example">
                            <option selected>Open this select menu</option>
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?= $category['id_categorie'] ?>"><?= htmlspecialchars($category['nom_categorie']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productName" class="form-label">Nom Produit</label>
                        <input type="text" name="nom_produit" class="form-control" id="productName" placeholder="Nom du produit">
                    </div>
                     <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" id="productDescription" placeholder="Description">
                    </div>
                     <div class="mb-3">
                        <label for="productDescription" class="form-label">Quantite</label>
                        <input type="number" name="quantite" class="form-control" id="productQuantiite" placeholder="quantite">
                    </div>
                    
                    <div class="mb-3">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        <label for="productPhoto" class="form-label">Photo</label>
                        <input  type="file" name="photo" class="form-control" id="productPhoto">
                    </div>
                     <div class="mb-3">
                        <label for="productDescription" class="form-label">Prix Achat</label>
                        <input type="number" name="prix_achat" class="form-control" id="prix_achat" placeholder="Prix Achat">
                    </div>
                     <div class="mb-3">
                        <label for="productDescription" class="form-label">Prix vente</label>
                        <input type="number" name="prix_vente" class="form-control" id="prix_vente" placeholder="prix_vente">
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="ok" class="btn btn-secondary mb-3">Enregistrer</button>
                        <button type="reset" class="btn btn-warning mb-3">Réinitialiser</button>
                        <a href="produit.php" class="btn btn-outline-warning mb-3">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require"../include/footer.php"; ?>
