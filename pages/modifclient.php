<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
require_once "../configuration/conect.php"; 

$idC = isset($_GET['idC']) ? (int)$_GET['idC'] : 0;
$id_client = 0;
$nom = '';
$prenom = '';
$adresse = '';
$telephone = '';
$email = '';

if ($idC) {
    $reqc = "SELECT * FROM client WHERE id_client = ?";
    $stmt = $bdd->prepare($reqc);
    $stmt->execute([$idC]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($client) {
        $id_client = $client['id_client'];
        $nom = $client['nom'];
        $prenom = $client['prenom'];
        $adresse = $client['adresse'];
        $telephone = $client['telephone'];
        $email = $client['email'];
    }
}

// Requête de mise à jour
if (isset($_POST['edit'])) {
    $id_client = isset($_POST['id_client']) ? (int)$_POST['id_client'] : null;
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $email = htmlspecialchars($_POST['email']);

    if (!empty($nom) && !empty($prenom) && !empty($adresse) && !empty($telephone) && !empty($email)) {
        if ($id_client) {
            // Update client
            $reqcat = $bdd->prepare("UPDATE client SET nom = ?, prenom = ?, adresse = ?, telephone = ?, email = ? WHERE id_client = ?");
            $reqcat->execute([$nom, $prenom, $adresse, $telephone, $email, $id_client]);
            $erreurs="Client modifié avec succès!";
            header("Location: client.php?message=Client modifié avec succès!");
            exit;
        } else {
            // Insert client
            $reqClient = $bdd->prepare("INSERT INTO client (nom, prenom, adresse, telephone, email) VALUES (?, ?, ?, ?, ?)");
            $reqClient->execute([$nom, $prenom, $adresse, $telephone, $email]);
            header("Location: client.php?message=Client bien enregistré avec succès!");
            exit;
        }
    } else {
        $erreurs = "Échec de l'enregistrement. Tous les champs sont obligatoires.";
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

             
            <div class="card ">
                <div class="card-header">
                    <h5 class="card-title">
                        Add/Edit Client
                    </h5>
                </div>
    <div class="card-body">
    <form action="" method="post">
    <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Id:</label>
    <div class="col-sm-10">
         :<?php echo $id_client ?>
    <input type="hidden" class="form-control" id="id_client" name="id_client" value="<?php echo $id_client ?>">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Nom</label>
    <div class="col-sm-10">
    <input type="text" value="<?php echo $nom ?>" class="form-control" id="nom" name="nom" placeholder="Nom">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Prenom</label>
    <div class="col-sm-10">
     <input type="text" value="<?php echo $prenom ?>" name="prenom" class="form-control" id="prenom" placeholder="Prenom">
    </div>
  </div>
        <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Adresse</label>
    <div class="col-sm-10">
     <input type="text" value="<?php echo $adresse ?>" name="adresse" class="form-control" id="adresse" placeholder="Adresse">
    </div>
  </div> 
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">telephone</label>
    <div class="col-sm-10">
     <input type="text" value="<?php echo $telephone ?>" name="telephone" class="form-control" id="telephone" placeholder="telephone">
    </div>
  </div> 
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">email</label>
    <div class="col-sm-10">
     <input type="text" value="<?php echo $email ?>" name="email" class="form-control" id="email" placeholder="email">
    </div>
  </div> 
   <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Mettre a jour </label>
    <div class="col-sm-10">
     <button type="submit" name="edit" class="btn btn-secondary mb-3">Mettre à jour</button>
                            <button type="reset" class="btn btn-warning mb-3">Réinitialiser</button>
    </div>
  </div> 
   
                    </form>
                </div>
            </div>
       
    </div>
</main>
<?php require "../include/footer.php"; ?>
