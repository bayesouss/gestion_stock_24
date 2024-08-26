<?php 
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php";

$iduserC = isset($_GET['iduserC']) ? (int)$_GET['iduserC'] : 0;
$iduser = 0;
$login='';
$email='';
$role='';

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
    }
}

// Requête de mise à jour
if (isset($_POST['edit'])) {
    $login = htmlspecialchars($_POST['login']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);
    $id_categorie = isset($_POST['iduser']) ? (int)$_POST['iduser'] : null;

    if (!empty($nom_categorie)) {
        if ($id_categorie) {
            // Update category
            $reqcat = $bdd->prepare("UPDATE utilisateur SET login = ?,email = ?, role = ? WHERE iduser = ?");
            $reqcat->execute([$nom_categorie, $id_categorie]);
              header("Location: categorie.php?message=Utilisateur modifier avec sucées!");
            
            
             
        } else {
            // Insert category
            $reqCategorie = $bdd->prepare("INSERT INTO utilisateur (login, email,role) VALUES (?,?,?)");
            $reqCategorie->execute([$nom_categorie]);

             header("Location: categorie.php?message=Utilisateur supprimer avec succès!");
            
        }
        header('location:utilisateur.php');
        exit;
    } else {
        $erreurs = "Tous les champs sont obligatoir";
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
                               Gestion des Utilisateurs
                            </h5>
                        </div>
                                    <div class="card-body">
                           

                            <form action="" method="post">
                                <div class="mb-3">
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Id: <?php echo $iduser ?></label>
        <input type="hidden" class="form-control" id="exampleFormControlInput1" name="id_categorie" value="<?php echo $iduser ?>">
                        </div> 
  <label for="exampleFormControlInput1" class="form-label">Login</label>
  <input type="text" class="form-control" value="<?php echo $login ?>" id="exampleFormControlInput1"name="login" placeholder="Login">
</div>
<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Email</label>
  <input type="email" value="<?php echo $email ?>" class="form-control" id="exampleFormControlInput1"name="email" placeholder="Email">
</div>
<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Role</label>
  <input type="text" value="<?php echo $role ?>" class="form-control" id="exampleFormControlInput1"name="role" placeholder="Role">
</div>



 <div class="col-auto">
    <button type="submit" name="ok" class="btn btn-secondary mb-3">Modifier</button>
    <a href="utilisateur.php" type="submit" class="btn btn-warning mb-3">Annuler</a>
</div>

                            </form>
                        </div>
                    </div>
</div>

                </div>
                </div> 
            </main>
            <?php require"../include/footer.php"; ?>