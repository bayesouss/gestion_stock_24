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
$sql = "SELECT COUNT(*) AS nbcat FROM utilisateur WHERE nom_utilisateur LIKE :cherche";
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
$sql = "SELECT * FROM utilisateur WHERE login LIKE :cherche ORDER BY role LIMIT :premier, :parpage";
$result1 = $bdd->prepare($sql);
$result1->bindValue(':cherche', '%' . $cherche . '%', PDO::PARAM_STR);
$result1->bindValue(':premier', $premier, PDO::PARAM_INT);
$result1->bindValue(':parpage', $parpage, PDO::PARAM_INT);
$result1->execute();
$utilisateurs = $result1->fetchAll(PDO::FETCH_ASSOC);

$total_categorie = $bdd->query("SELECT COUNT(*) as total_sales FROM utilisateur")->fetch(PDO::FETCH_ASSOC)['total_sales'];

$iduserC = isset($_GET['iduserC']) ? (int)$_GET['iduserC'] : 0;
$iduser = 0;
$login = '';
$email = '';
$role = '';
$etat = 0; // Initialize $etat variable

if ($iduserC) {
    $reqc = "SELECT * FROM utilisateur WHERE iduser = ?";
    $stmt = $bdd->prepare($reqc);
    $stmt->execute([$iduserC]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($utilisateur) {
        $iduser = $utilisateur['iduser'];
        $login = $utilisateur['login'];
        $email = $utilisateur['email'];
        $role = $utilisateur['role'];
        $etat = $utilisateur['etat'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = htmlspecialchars($_POST['login']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);
    $etat = isset($_POST['etat']) ? 1 : 0; // Checkbox value handling
    $pwd = isset($_POST['pwd']) ? sha1($_POST['pwd']) : null;

    if (!empty($login) && !empty($email) && !empty($role)) {
        if (isset($_POST['edit'])) {
            // Update user
            $stmt = $bdd->prepare("UPDATE utilisateur SET login = ?, email = ?, role = ?, etat = ? WHERE iduser = ?");
            $stmt->execute([$login, $email, $role, $etat, $iduser]);
            $message = "Utilisateur modifié avec succès!";
        } else {
            // Insert new user
            $stmt = $bdd->prepare("INSERT INTO utilisateur (login, email, role, etat, pwd) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$login, $email, $role, $etat, $pwd]);
            $message = "Utilisateur ajouté avec succès!";
        }
        header("Location: utilisateur.php?message=" . urlencode($message));
        exit;
    } else {
        $erreurs = "Tous les champs obligatoires doivent être remplis.";
    }
}
}else{
    header('location:../pages/login.php');
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
                                    <p class="mb-0"></p>
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
                        Nombre Total d'utilisateurs
                    </p>
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($total_categorie); ?> Utilisateurs
                    </h4>
                    
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
        <?php if (isset($_GET['message'])) { ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-sm-4">       
                <div class="card border-0">
                    <div class="card-header">
                        <h5 class="card-title">Gestion des Utilisateurs</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <input type="hidden" name="iduser" value="<?php echo $iduser ?>">
                            <div class="mb-3">
                                <label for="login" class="form-label">Login</label>
                                <input type="text" value="<?php echo $login ?>" class="form-control" id="login" name="login" placeholder="Login">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" value="<?php echo $email ?>" class="form-control" id="email" name="email" placeholder="Email">
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label><br>
                                <input type="radio" id="admin" name="role" value="admin" <?php echo ($role == 'admin') ? 'checked' : '' ?>>
                                <label for="admin">Admin</label><br>
                                <input type="radio" id="visiteur" name="role" value="visiteur" <?php echo ($role == 'visiteur') ? 'checked' : '' ?>>
                                <label for="visiteur">Visiteur</label>
                            </div>
                            <div class="mb-3">
                                <label for="etat" class="form-label">Etat</label><br>
                                <input type="checkbox" id="etat" name="etat" value="1" <?php echo ($etat == 1) ? 'checked' : '' ?>>
                                <label for="etat">Actif</label>
                            </div>
                            <?php if (!$iduserC) { ?>
                                <div class="mb-3">
                                    <label for="pwd" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_pwd" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_pwd" name="confirm_pwd" placeholder="Confirm Password">
                                </div>
                            <?php } ?>
                            <div class="col-auto">
                                <?php if ($iduserC) { ?>
                                    <button type="submit" name="edit" class="btn btn-secondary mb-3">Modifier</button>
                                    <a href="utilisateur.php" class="btn btn-warning mb-3">Annuler</a>
                                    <a href="editpwd.php?iduserC=<?php echo $iduser ?>">Changer votre mot de passe!</a>
                                <?php } else { ?>
                                    <button type="submit" name="ok" class="btn btn-secondary mb-3">Enregistrer</button>
                                    <button type="reset" class="btn btn-warning mb-3">Réinitialiser</button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8"> 
                <div class="card">
                    <div class="card-header">Recherche des utilisateurs</div>
                    <div class="card-body">
                        <form method="get" action="utilisateur.php">
                            <div class="row">
                                <div class="col">
                                    <input class="form-control me-2" type="search" name="cherche" placeholder="Nom utilisateur" value="<?= htmlspecialchars($cherche) ?>">
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
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>iduser</th>
                            <th>login</th>
                            <th>email</th>
                            <th>role</th>
                            <th>Opérations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur) { ?>
                            <tr class="<?php echo $utilisateur['etat']==1?'success':'danger' ?>">
                                <td><?php echo $utilisateur['iduser'] ?></td>
                                <td><?php echo $utilisateur['login'] ?></td>
                                <td><?php echo $utilisateur['email'] ?></td>
                                <td><?php echo $utilisateur['role'] ?></td>
                                <td>
                                    <a href="utilisateur.php?iduserC=<?php echo $utilisateur['iduser'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    &nbsp;
                                    <a href="supprimuser.php?iduserC=<?php echo $utilisateur['iduser'] ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    &nbsp;
                                     <a href="activerUtilisateur.php?iduserC=<?php echo $utilisateur['iduser']; ?>&etat=<?php echo $utilisateur['etat']; ?>">
                                <?php 
                                   if($utilisateur['etat']==1)
                                    echo '<i class="fas fa-remove"></i>';
                                    else
                                      echo '<i class="fas fa-check"></i>';   
                                ?>
                            </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination">
                        <li class="page-item <?= ($currentpage == 1) ? 'disabled' : '' ?>">
                            <a href="utilisateur.php?page=<?= $currentpage - 1 ?>" class="page-link">Précédent</a>
                        </li>
                        <?php for ($page = 1; $page <= $pages; $page++) { ?>
                            <li class="page-item <?= ($currentpage == $page) ? 'active' : '' ?>">
                                <a href="utilisateur.php?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                            </li>
                        <?php } ?>
                        <li class="page-item <?= ($currentpage == $pages) ? 'disabled' : '' ?>">
                            <a href="utilisateur.php?page=<?= $currentpage + 1 ?>" class="page-link">Suivant</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</main>
<?php require "../include/footer.php"; ?>
