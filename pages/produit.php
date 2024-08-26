<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php";

$cherche = isset($_GET['cherche']) ? trim($_GET['cherche']) : "";
$cherche = htmlspecialchars($cherche);

$currentpage = isset($_GET['page']) && !empty($_GET['page']) ? (int)strip_tags($_GET['page']) : 1;

// Count total clients with search filter
$sql = "SELECT COUNT(*) AS nbprod FROM produit WHERE nom_produit LIKE :cherche OR quantite LIKE :cherche OR prix_achat LIKE :cherche OR prix_vente LIKE :cherche ";
$result = $bdd->prepare($sql);
$result->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result->execute();
$nbTotalprod = $result->fetch();
$total = (int)$nbTotalprod['nbprod'];

// Pagination calculation
$parpage = 4;
$pages = ceil($total / $parpage);
$premier = ($currentpage * $parpage) - $parpage;

// Fetch clients with search filter and pagination
$sql = "SELECT p.id_produit, p.nom_produit, p.description, p.quantite, p.prix_achat, p.prix_vente, p.photo, c.nom_categorie as nom_categorie 
        FROM produit p 
        JOIN categorie c ON p.id_categorie = c.id_categorie 
        WHERE p.nom_produit LIKE :cherche OR p.quantite LIKE :cherche OR p.prix_achat LIKE :cherche 
        ORDER BY p.id_produit 
        LIMIT :premier, :parpage";
$result1 = $bdd->prepare($sql);
$result1->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result1->bindValue(':premier', $premier, PDO::PARAM_INT);
$result1->bindValue(':parpage', $parpage, PDO::PARAM_INT);
$result1->execute();
$produits = $result1->fetchAll(PDO::FETCH_ASSOC);

$total_produits = $bdd->query("SELECT COUNT(*) as total_sales FROM produit")->fetch(PDO::FETCH_ASSOC)['total_sales'];


// Debugging: Print the query and the fetched results
// echo "<pre>";
// print_r($result1->errorInfo()); // Print any SQL errors
// print_r($produits); // Print the fetched results
// echo "</pre>";
}else{
    header('location:../pages/login.php');
}

?>
      
<?php require "../include/sidebar.php"; ?>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Gestion des produits</h4>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill border-0 illustration">
                    <div class="card-body p-0 d-flex flex-fill">
                        <div class="row g-0 w-100">
                            <div class="col-6">
                                <div class="p-3 m-1">
                                   <h4>Bienvenue, <?php echo htmlspecialchars($userinfo['login']); ?></h4>
                                   
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
                    <p class="mb-2">
                        Nombre Total de produit
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($total_produits); ?> Produits
                    </h4>
                    
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="card">
            <div class="card-header">
                Recherche Produits
            </div>
            <div class="card-body">
                <form method="get" action="produit.php">
                    <div class="row">
                        <div class="col">
                            <input class="form-control me-2" type="search" name="cherche" placeholder="recherche nom produit  quantite ou prix" value="<?= htmlspecialchars($cherche) ?>">
                        </div>
                        <div class="col-auto">
                            <button name="valider" class="btn btn-outline-success" type="search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="formproduit.php"><i class="fas fa-plus"></i>Nouvelle Produit</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
            <?php
if (isset($_GET['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}
?>

        <!-- Table Element -->
        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">
                    Liste des Produits

                </h5>
            </div>
              
            <div class="card-body">
                <!-- how to delete product in this table -->
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nom Categorie</th>
                            <th scope="col">Nom Produit</th>
                            <th scope="col">Description</th>
                            <th scope="col">Quantite</th>
                             <th scope="col">Photo</th>
                             <th scope="col">Prix Achat</th>
                             <th scope="col">Prix Vente</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($produits as $produit) { ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($produit['id_produit']) ?></th>
                                <td><?= htmlspecialchars($produit['nom_categorie']) ?></td>
                                <td><?= htmlspecialchars($produit['nom_produit']) ?></td>
                                <td><?= htmlspecialchars($produit['description']) ?></td>
                                <td><?= htmlspecialchars($produit['quantite']) ?></td>
                                
                                 <td><img src="../uploads/<?= $produit['photo']; ?>"width="70px" height="70px"></td>
                                 <td><?= htmlspecialchars($produit['prix_achat']) ?></td>
                                 <td><?= htmlspecialchars($produit['prix_vente']) ?></td>

                                <td>
                                    <a href="modifprod.php?idP=<?php echo $produit['id_produit']; ?>"><i class="fas fa-edit"></i></a>
                                    &nbsp;
                                    &nbsp;
                                    <a onclick="return confirm('Etes vous sur de vouloir supprimer le produit?')" href="supprimprod.php?idP=<?php  echo $produit['id_produit']; ?>"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-auto">
                        <ul class="pagination">
                            <li class="page-item <?= ($currentpage == 1) ? "disabled" : "" ?>">
                                <a href="produit.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage - 1 ?>" class="page-link">Precedent</a>
                            </li>
                            <?php for ($page = 1; $page <= $pages; $page++): ?>
                                <li class="page-item <?= ($currentpage == $page) ? "active" : "" ?>">
                                    <a href="produit.php?cherche=<?= urlencode($cherche) ?>&page=<?= $page ?>" class="page-link"><?= $page ?></a>
                                </li>
                            <?php endfor ?>
                            <li class="page-item <?= ($currentpage == $pages) ? "disabled" : "" ?>">
                                <a href="produit.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage + 1 ?>" class="page-link">Suivant</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require "../include/footer.php"; ?>
