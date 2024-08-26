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
$sql = "SELECT COUNT(*) AS nbcat FROM categorie WHERE nom_categorie LIKE :cherche ";
$result = $bdd->prepare($sql);
$result->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result->execute();
$nbTotalclient = $result->fetch();
$total = (int)$nbTotalclient['nbcat'];

// Pagination calculation
$parpage = 4;
$pages = ceil($total / $parpage);
$premier = ($currentpage * $parpage) - $parpage;

// Fetch clients with search filter and pagination
$sql = "SELECT * FROM categorie WHERE nom_categorie LIKE :cherche ORDER BY id_categorie LIMIT :premier, :parpage";
$result1 = $bdd->prepare($sql);
$result1->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result1->bindValue(':premier', $premier, PDO::PARAM_INT);
$result1->bindValue(':parpage', $parpage, PDO::PARAM_INT);
$result1->execute();
$categories = $result1->fetchAll(PDO::FETCH_ASSOC);

$total_categorie = $bdd->query("SELECT COUNT(*) as total_sales FROM categorie")->fetch(PDO::FETCH_ASSOC)['total_sales'];

if(isset($_POST['ok'])){
     $nom_categorie=htmlspecialchars($_POST['nom_categorie']) ;

    if(!empty($_POST['nom_categorie'])){

        require_once"../configuration/conect.php";
        $reqCategorie=$bdd->prepare("INSERT INTO categorie VALUES (null,?)");
        $reqCategorie->execute([$nom_categorie]);
        $erreurs=" Enregistre bien ajouter"; 
        
        // Après l'insertion, rediriger vers la même page pour un "refresh"
         header("Location: " . $_SERVER['PHP_SELF']);
    }
    else
    { 
         $erreurs="Veuillez remplir tous les champs";
    }
     
}
}else{
    header('location:../pages/login.php');
}

 ?>
 

      
      <?php require"../include/sidebar.php"; ?>
    <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="mb-3">
                        <h4>Gestion des categories</h4>
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
                        Nombre Total de categorie
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($total_categorie); ?> Categories
                    </h4>
                    
                </div>
            </div>
        </div>
    </div>
</div>
                    </div>
                     <?php if(isset($erreurs)) { ?>
                <div class="alert alert-warning"> 
                    <strong>Message!</strong>  
                    <?php echo $erreurs; ?>
                </div>
            <?php } ?>
                     <?php
if (isset($_GET['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}
?>
                         <!-- Table Element -->
                   <div class="row">
                   <div class="col-sm-4">       
                    <div class="card border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                Enregistrement Categorie
                            </h5>
                        </div>
                                    <div class="card-body">
                           

                            <form action="" method="post">
                                <div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Nom Categorie</label>
  <input type="text" class="form-control" id="exampleFormControlInput1"name="nom_categorie" placeholder="Nom">
</div>

 <div class="col-auto">
    <button type="submit" name="ok" class="btn btn-secondary mb-3">Enregistrer</button>
    <button type="reset" class="btn btn-warning mb-3">Réinitialiser</button>
</div>

                            </form>
                        </div>
                    </div>
</div>
 <div class="col-sm-8"> 
        <div class="card">
         <div class="card-header">
            Recherche de Categories
          </div>
  <div class="card-body">
   <form method="get" action="categorie.php">
    <div class="row">
        <div class="col">
            <input class="form-control me-2" type="search" name="cherche" placeholder="Nom Categorie" value="<?= htmlspecialchars($cherche) ?>">
        </div>
        <div class="col-auto">
            <button name="valider" class="btn btn-outline-success" type="search">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</form>

  </div>
</div>

                    <!-- Table Element -->
                    <div class="card border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                Liste des Categories
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nom</th>
                                         <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                          <?php foreach($categories as $categorie) {
                            ?>
                                    <tr>
                                        <th scope="row"><?php echo $categorie['id_categorie'] ?></th>
                                        <td><?= $categorie['nom_categorie'] ?></td>
                             <td>
                                <a href="modifCategorie.php?idC=<?php echo $categorie['id_categorie']; ?>"><i class="fas fa-edit"></i></a>
                                &nbsp;
                                &nbsp;
                                <a onclick="return confirm ('Etes vous sur de vouloir supprimer le filiere ')" href="supprimcategorie.php?idC=<?php  echo $categorie['id_categorie']; ?>"><i class="fas fa-trash"></i></a>
                             </td>
                                    </tr>
                                 <?php  } ?>
                                </tbody>
                            </table>
     <div class="row">
        <div class="col-auto">        
           <ul class="pagination">
    <li class="page-item <?= ($currentpage == 1) ? "disabled" : "" ?>">
        <a href="categorie.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage - 1 ?>" class="page-link">Precedent</a>
    </li>
    <?php for($page = 1; $page <= $pages; $page++): ?>
        <li class="page-item <?= ($currentpage == $page) ? "active" : "" ?>">
            <a href="categorie.php?cherche=<?= urlencode($cherche) ?>&page=<?= $page ?>" class="page-link"><?= $page ?></a>
        </li>
    <?php endfor ?>
    <li class="page-item <?= ($currentpage == $pages) ? "disabled" : "" ?>">
        <a href="categorie.php?cherche=<?= urlencode($cherche) ?>&page=<?= $currentpage + 1 ?>" class="page-link">Suivant</a>
    </li>
</ul>
      
          </div>
          </div>

                        </div>
                    </div>
                    </div>
                </div>
                </div> 
            </main>
            <?php require"../include/footer.php"; ?>