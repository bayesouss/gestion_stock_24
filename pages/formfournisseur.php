<?php 
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
if(isset($_POST['ok'])){
	$nom=htmlspecialchars($_POST['nom']);
	$prenom=htmlspecialchars($_POST['prenom']);
	$telephone=htmlspecialchars($_POST['telephone']);
	$email=htmlspecialchars($_POST['email']);

	if(!empty($_POST['nom']) AND !empty($_POST['prenom']) AND !empty($_POST['telephone']) AND !empty($_POST['email'])){
        require_once"../configuration/conect.php";
        $reqfournisseur=$bdd->prepare("INSERT INTO fournisseur VALUES(null,?,?,?,?)");
        $reqfournisseur->execute([$nom,$prenom,$telephone,$email]);
        $erreurs="fournisseur bien enregistrer";
}else{
	$erreurs="Veuillez remplir tous les champs!";
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
                        <h4>Enregistrement Fournisseurs</h4>
                    </div>
                    <div class="row">
                        
                       
                    </div>
                    <?php if(isset($erreurs))
              {
                 ?>
                    <div class="alert alert-warning"> 
                        <strong>Message!</strong>  
                           <?php echo $erreurs; ?>
                    </div>
                 <?php 
              }
            ?>
                    <!-- Table Element -->
                    <div class="card border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                Fournisseurs
                            </h5>
                        </div>
                        <div class="card-body">
                           <form action="" method="post">
   
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Nom</label>
    <div class="col-sm-10">
    <input type="text"  class="form-control" id="nom" name="nom" placeholder="Nom">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Prenom</label>
    <div class="col-sm-10">
     <input type="text" name="prenom" class="form-control" id="prenom" placeholder="Prenom">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">telephone</label>
    <div class="col-sm-10">
     <input type="text" name="telephone" class="form-control" id="telephone" placeholder="telephone">
    </div>
  </div> 
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
     <input type="email" name="email" class="form-control" id="email" placeholder="email">
    </div>
  </div> 
  
   <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Ajout </label>
    <div class="col-sm-10">
     <button type="submit" name="ok" class="btn btn-secondary mb-3">Enregistre</button>
      <!-- <button type="reset" class="btn btn-warning mb-3">Réinitialiser</button> -->
      <a class="btn btn-warning mb-3" href="fournisseur.php">Retour</a>
    </div>
  </div> 
   
                    </form>
                        </div>
                    </div>
                </div>
            </main>
            <?php require"../include/footer.php"; ?>