<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";

    // Récupérer le terme de recherche s'il est fourni
    $cherche = isset($_GET['cherche']) ? trim($_GET['cherche']) : "";
    $cherche = htmlspecialchars($cherche);

    // Déterminer la page courante
    $currentpage = isset($_GET['page']) && !empty($_GET['page']) ? (int)strip_tags($_GET['page']) : 1;

    // Compter le nombre total de ventes avec le filtre de recherche
    $sql = "SELECT COUNT(*) AS nbvent FROM vente WHERE quantite LIKE :cherche OR total LIKE :cherche";
    $result = $bdd->prepare($sql);
    $result->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
    $result->execute();
    $nbTotalprod = $result->fetch();
    $total = (int)$nbTotalprod['nbvent'];

    // Calcul de la pagination
    $parpage = 4;
    $pages = ceil($total / $parpage);
    $premier = ($currentpage * $parpage) - $parpage;

    // Récupérer les ventes avec le filtre de recherche et la pagination
    $sql = "SELECT p.id_vente, p.quantite, p.total, c.nom AS nom_client, c.prenom AS prenom_client, d.nom_produit 
            FROM vente p 
            JOIN client c ON p.id_client = c.id_client 
            JOIN produit d ON p.id_produit = d.id_produit 
            WHERE d.nom_produit LIKE :cherche OR p.quantite LIKE :cherche OR p.total LIKE :cherche 
            ORDER BY p.id_vente 
            LIMIT :premier, :parpage";
    $result1 = $bdd->prepare($sql);
    $result1->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
    $result1->bindValue(':premier', $premier, PDO::PARAM_INT);
    $result1->bindValue(':parpage', $parpage, PDO::PARAM_INT);
    $result1->execute();
    $ventes = $result1->fetchAll(PDO::FETCH_ASSOC);


$total_ventes = $bdd->query("SELECT COUNT(*) as total_sales FROM vente")->fetch(PDO::FETCH_ASSOC)['total_sales'];
$gain_total = $bdd->query("SELECT SUM(total) as total_gain FROM vente")->fetch(PDO::FETCH_ASSOC)['total_gain'];

} else {
    // Redirection vers la page de login si l'utilisateur n'est pas connecté
    header('Location: ../pages/login.php');
    exit;
}
?>


<?php require "../include/sidebar.php"; ?>
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
                                    <h4>Bienvenue, <?php echo htmlspecialchars($userinfo['login']); ?></h4>
                                    <p class="mb-0">C'est ici la gestion des ventes</p>
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
                        Gain Total
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars(number_format($gain_total, 2)); ?> FCFA
                    </h4>
                   
                    <p class="mb-2">
                        Nombre Total de Ventes
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($total_ventes); ?> Ventes
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
                Gestion Vente
            </div>
            <div class="card-body">
                <form method="get" action="client.php">
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
                            <a href="formvente.php"><i class="fas fa-plus"></i> Nouvelle Vente</a>
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
                    Liste des Ventes
                </h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                             <th scope="col">Nom Client</th>
                            <th scope="col">Nom Client</th>
                            <th scope="col">Produit</th>
                            <th scope="col">Quantité</th>
                            <th scope="col">Total</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventes as $vente) { ?>
                            <tr>
                                <th scope="row"><?= htmlspecialchars($vente['id_vente']) ?></th>
                                 <td><?= htmlspecialchars($vente['prenom_client']) ?></td>
                                <td><?= htmlspecialchars($vente['nom_client']) ?></td>
                                <td><?= htmlspecialchars($vente['nom_produit']) ?></td>
                                <td><?= htmlspecialchars($vente['quantite']) ?></td>
                                <td><?= htmlspecialchars($vente['total']) ?></td>
                               <td>
                                  <?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté et que son rôle est 'admin'
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin') {
?>
    <a href="modifvente.php?idP=<?= $vente['id_vente'] ?>"><i class="fas fa-edit"></i></a>
    &nbsp;&nbsp;
    <a onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vente?')" href="supprimvente.php?idV=<?= $vente['id_vente'] ?>"><i class="fas fa-trash"></i></a>
    &nbsp;&nbsp;

    <?php
}
?>
    <!-- Print button -->
    <a href="facture_one.php?id_vente=<?= $vente['id_vente'] ?>" target="_blank"><i class="fas fa-print"></i></a>
</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-auto">
                        <ul class="pagination">
                            <li class="page-item <?= ($currentpage == 1) ? "disabled" : "" ?>">
                                <a href="vente.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage - 1 ?>" class="page-link">Précédent</a>
                            </li>
                            <?php for ($page = 1; $page <= $pages; $page++): ?>
                                <li class="page-item <?= ($currentpage == $page) ? "active" : "" ?>">
                                    <a href="vente.php?cherche=<?= urlencode($cherche) ?>&page=<?= $page ?>" class="page-link"><?= $page ?></a>
                                </li>
                            <?php endfor ?>
                            <li class="page-item <?= ($currentpage == $pages) ? "disabled" : "" ?>">
                                <a href="vente.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage + 1 ?>" class="page-link">Suivant</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require "../include/footer.php"; ?>
