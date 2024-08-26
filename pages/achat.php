<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";

    // Récupérer et sécuriser la variable de recherche
    $cherche = isset($_GET['cherche']) ? trim($_GET['cherche']) : "";
    $cherche = htmlspecialchars($cherche);

    // Déterminer la page actuelle
    $currentpage = isset($_GET['page']) && !empty($_GET['page']) ? (int)strip_tags($_GET['page']) : 1;

    // Compter le nombre total d'achats avec le filtre de recherche
    $sql = "SELECT COUNT(*) AS nbachat FROM achat WHERE quantite LIKE :cherche OR total LIKE :cherche";
    $result = $bdd->prepare($sql);
    $result->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
    $result->execute();
    $nbTotalprod = $result->fetch();
    $total = (int)$nbTotalprod['nbachat'];

    // Calcul de la pagination
    $parpage = 4;
    $pages = ceil($total / $parpage);
    $premier = ($currentpage * $parpage) - $parpage;

    // Récupérer les achats avec le filtre de recherche et la pagination
    $sql = "SELECT p.id_achat, p.quantite, p.total, c.nom AS nom_fournisseur, c.prenom AS prenom_fournisseur, d.nom_produit 
            FROM achat p 
            JOIN fournisseur c ON p.id_fournisseur = c.id_fournisseur 
            JOIN produit d ON p.id_produit = d.id_produit 
            WHERE d.nom_produit LIKE :cherche OR p.quantite LIKE :cherche OR p.total LIKE :cherche 
            ORDER BY p.id_achat 
            LIMIT :premier, :parpage";
    $result1 = $bdd->prepare($sql);
    $result1->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
    $result1->bindValue(':premier', $premier, PDO::PARAM_INT);
    $result1->bindValue(':parpage', $parpage, PDO::PARAM_INT);
    $result1->execute();
    $achats = $result1->fetchAll(PDO::FETCH_ASSOC);

$total_achats = $bdd->query("SELECT COUNT(*) as total_sales FROM achat")->fetch(PDO::FETCH_ASSOC)['total_sales'];
$achat_total = $bdd->query("SELECT SUM(total) as total_gain FROM achat")->fetch(PDO::FETCH_ASSOC)['total_gain'];



} else {
    // Redirection vers la page de login si l'utilisateur n'est pas connecté
    header('location:../pages/login.php');
    exit;
}
?>


<?php require "../include/sidebar.php"; ?>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
           <!--  <h4>Admin Dashboard</h4> -->
        </div>
        <div class="row">
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill border-0 illustration">
                    <div class="card-body p-0 d-flex flex-fill">
                        <div class="row g-0 w-100">
                            <div class="col-6">
                                <div class="p-3 m-1">
                                   <h4>Bienvenue, <?php echo htmlspecialchars($userinfo['login']); ?></h4>
                                    <p class="mb-0">C'est ici la gestion des achats</p>
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
                        Dépense Total
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars(number_format($achat_total, 2)); ?> FCFA
                    </h4>
                   
                    <p class="mb-2">
                        Nombre Total d'achat
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($total_achats); ?> Achats
                    </h4>
                    
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
        <?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
?>

        <div class="card">
            <div class="card-header">
                Gestion achat
            </div>
            <div class="card-body">
                <form method="get" action="fournisseur.php">
                    <div class="row">
                        <div class="col">
                            <input class="form-control me-2" type="search" name="cherche" placeholder="Nom, Prenom ou Total" value="<?= htmlspecialchars($cherche) ?>">
                        </div>
                        <div class="col-auto">
                            <button name="valider" class="btn btn-outline-success" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="formachat.php"><i class="fas fa-plus"></i> Nouveau achat</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['message'])) { ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php } ?>

        <!-- Table Element -->
        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">
                    Liste des achats
                </h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                             <th scope="col">Prenom </th>
                            <th scope="col">Nom </th>
                            <th scope="col">Produit</th>
                            <th scope="col">Quantité</th>
                            <th scope="col">Total</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achats as $achat) { ?>
                            <tr>
                                <th scope="row"><?= htmlspecialchars($achat['id_achat']) ?></th>
                                <td><?= htmlspecialchars($achat['prenom_fournisseur']) ?></td>
                                <td><?= htmlspecialchars($achat['nom_fournisseur']) ?></td>
                                <td><?= htmlspecialchars($achat['nom_produit']) ?></td>
                                <td><?= htmlspecialchars($achat['quantite']) ?></td>
                                <td><?= htmlspecialchars($achat['total']) ?></td>
                                <td>
                                    <?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté et que son rôle est 'admin'
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin') {
?>
                                    <a href="modifachat.php?idP=<?= $achat['id_achat'] ?>"><i class="fas fa-edit"></i></a>
                                    &nbsp;&nbsp;
                                    <a onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette achat?')" href="supprimachat.php?idV=<?= $achat['id_achat'] ?>"><i class="fas fa-trash"></i></a>
                                </td>
                                <?php
}
?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-auto">
                        <ul class="pagination">
                            <li class="page-item <?= ($currentpage == 1) ? "disabled" : "" ?>">
                                <a href="achat.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage - 1 ?>" class="page-link">Précédent</a>
                            </li>
                            <?php for ($page = 1; $page <= $pages; $page++): ?>
                                <li class="page-item <?= ($currentpage == $page) ? "active" : "" ?>">
                                    <a href="achat.php?cherche=<?= urlencode($cherche) ?>&page=<?= $page ?>" class="page-link"><?= $page ?></a>
                                </li>
                            <?php endfor ?>
                            <li class="page-item <?= ($currentpage == $pages) ? "disabled" : "" ?>">
                                <a href="achat.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage + 1 ?>" class="page-link">Suivant</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require "../include/footer.php"; ?>
