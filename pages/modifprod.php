<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php"; 

// Fetch product details if ID is set
if (isset($_GET['idP'])) {
    $id_produit = (int)$_GET['idP'];
    
    // Fetch product details
    $sql = "SELECT * FROM produit WHERE id_produit = :id_produit";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
    $stmt->execute();
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produit) {
        echo "Product not found.";
        exit;
    }
    
    // Fetch categories
    $sql = "SELECT * FROM categorie";
    $stmt = $bdd->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (isset($_POST['ok'])) {
        $nom_produit = $_POST['nom_produit'];
        $description = $_POST['description'];
        $quantite = $_POST['quantite'];
        $prix_achat = $_POST['prix_achat'];
         $prix_vente = $_POST['prix_vente'];
        $categorie_id = $_POST['categorie_id'];
        
        // Update product details
        $sql = "UPDATE produit SET 
                nom_produit = :nom_produit,
                description = :description,
                quantite = :quantite,
                prix_achat = :prix_achat,
                 prix_vente = :prix_vente,
                id_categorie = :id_categorie
                WHERE id_produit = :id_produit";
                
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':nom_produit', $nom_produit, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        $stmt->bindValue(':prix_achat', $prix_achat, PDO::PARAM_INT);
         $stmt->bindValue(':prix_vente', $prix_vente, PDO::PARAM_INT);
        $stmt->bindValue(':id_categorie', $categorie_id, PDO::PARAM_INT);
        $stmt->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Redirect to the product list with a success message
            header("Location: produit.php?message=Produit bien modifier");
           
            exit;
        } else {
            echo "Erreur sur le modification.";
        }
    }
} else {
    echo "Id produit invalide.";
    exit;
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


        <!-- Edit Form -->
        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">
                    Modifier Produit
                </h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="categorySelect" class="form-label">Categorie</label>
                        <select name="categorie_id" class="form-select" id="categorySelect" aria-label="Default select example">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?= $category['id_categorie'] ?>" <?= $category['id_categorie'] == $produit['id_categorie'] ? 'selected' : '' ?>><?= htmlspecialchars($category['nom_categorie']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productName" class="form-label">Nom Produit</label>
                        <input type="text" name="nom_produit" class="form-control" id="productName" placeholder="Nom du produit" value="<?php echo htmlspecialchars($produit['nom_produit']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" id="productDescription" placeholder="Description" value="<?php echo htmlspecialchars($produit['description']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="productQuantite" class="form-label">Quantite</label>
                        <input type="number" name="quantite" class="form-control" id="productQuantite" placeholder="Quantite" value="<?php echo htmlspecialchars($produit['quantite']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="productPrix" class="form-label">Prix d'achat</label>
                        <input type="number" name="prix_achat" class="form-control" id="productPrix" placeholder="Prix" value="<?php echo htmlspecialchars($produit['prix_achat']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="productPrix" class="form-label">Prix vente</label>
                        <input type="number" name="prix_vente" class="form-control" id="productPrix" placeholder="Prix" value="<?php echo htmlspecialchars($produit['prix_vente']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="productPhoto" class="form-label">Photo</label>
                         <img src="../image/<?php echo $produit['photo']; ?>" width="70px" height="70px">
                        <input type="file" name="photo" class="form-control" id="productPhoto">
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
<?php require "../include/footer.php"; ?>
