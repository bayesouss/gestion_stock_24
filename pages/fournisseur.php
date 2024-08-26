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
$sql = "SELECT COUNT(*) AS nbfour FROM fournisseur WHERE nom LIKE :cherche OR prenom LIKE :cherche OR telephone LIKE :cherche";
$result = $bdd->prepare($sql);
$result->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result->execute();
$nbTotalclient = $result->fetch();
$total = (int)$nbTotalclient['nbfour'];

// Pagination calculation
$parpage = 4;
$pages = ceil($total / $parpage);
$premier = ($currentpage * $parpage) - $parpage;

// Fetch clients with search filter and pagination
$sql = "SELECT * FROM fournisseur WHERE nom LIKE :cherche OR prenom LIKE :cherche OR telephone LIKE :cherche ORDER BY id_fournisseur LIMIT :premier, :parpage";
$result1 = $bdd->prepare($sql);
$result1->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result1->bindValue(':premier', $premier, PDO::PARAM_INT);
$result1->bindValue(':parpage', $parpage, PDO::PARAM_INT);
$result1->execute();
$fournisseurs = $result1->fetchAll(PDO::FETCH_ASSOC);

$total_fournisseurs = $bdd->query("SELECT COUNT(*) as total_sales FROM fournisseur")->fetch(PDO::FETCH_ASSOC)['total_sales'];


}else{
    header('location:../pages/login.php');
}

?>

      
      <?php require"../include/sidebar.php"; ?>
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
                                               <!--  <p class="mb-0">Admin Dashboard, CodzSword</p> -->
                                            </div>
                                        </div>
                                        <div class="col-6 align-self-end text-end">
                                            <img src="image/customer-support.jpg" class="img-fluid illustration-img"
                                                alt="">
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
                        Nombre Total de fournisseur
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($total_fournisseurs); ?> Fournissseurs
                    </h4>
                    
                </div>
            </div>
        </div>
    </div>
</div>
                    </div>

        <div class="card">
         <div class="card-header">
            Gestion Fournisseurs
          </div>
  <div class="card-body">
   <form method="get" action="fournisseur.php">
    <div class="row">
        <div class="col">
            <input class="form-control me-2" type="search" name="cherche" placeholder="Nom Prenom ou telephone" value="<?= htmlspecialchars($cherche) ?>">
        </div>
        <div class="col-auto">
            <button name="valider" class="btn btn-outline-success" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="col-auto">
            <a href="formfournisseur.php"><i class="fas fa-plus"></i>Nouveau Fournissseur</a>
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
                                Liste des Fourniiseurs
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prenom</th>
                                        
                                         <th scope="col">Telephone</th>
                                         <th scope="col">Email</th>
                                         <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                          <?php foreach($fournisseurs as $fournisseur) {
                            ?>
                                    <tr>
                                        <th scope="row"><?php echo $fournisseur['id_fournisseur'] ?></th>
                                        <td><?= $fournisseur['nom'] ?></td>
                                         <td><?= $fournisseur['prenom'] ?></td>
                                         <td><?= $fournisseur['telephone'] ?></td>
                                          <td><?= $fournisseur['email'] ?></td>
                                         <td>
                                <a href="modifournisseur.php?idf=<?php echo $fournisseur['id_fournisseur'] ?>"><i class="fas fa-edit"></i></a>
                                &nbsp;
                                &nbsp;
                                <a onclick="return confirm ('Etes vous sur de vouloir supprimer le fournisseur ')" href="supprimfournisseur.php?idf=<?php echo $fournisseur['id_fournisseur']; ?>"><i class="fas fa-trash"></i></a>
                               </td>
                                    </tr>
                                 <?php  } ?>
                                </tbody>
                            </table>
     <div class="row">
        <div class="col-auto">        
           <ul class="pagination">
    <li class="page-item <?= ($currentpage == 1) ? "disabled" : "" ?>">
        <a href="fournisseur.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage - 1 ?>" class="page-link">Precedent</a>
    </li>
    <?php for($page = 1; $page <= $pages; $page++): ?>
        <li class="page-item <?= ($currentpage == $page) ? "active" : "" ?>">
            <a href="fournisseur.php?cherche=<?= urlencode($cherche) ?>&page=<?= $page ?>" class="page-link"><?= $page ?></a>
        </li>
    <?php endfor ?>
    <li class="page-item <?= ($currentpage == $pages) ? "disabled" : "" ?>">
        <a href="fournisseur.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage + 1 ?>" class="page-link">Suivant</a>
    </li>
</ul>
      
          </div>
          </div>

                        </div>
                    </div>
                </div>
            </main>
            <?php require"../include/footer.php"; ?>