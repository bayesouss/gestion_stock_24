<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) { 
require_once "../configuration/conect.php";
require_once"../les_fonction/fonction.php";

// echo 'Nombre de user1 : ' .reccherche_par_login('user1');
// echo 'Nombre de user1@gmail.com : ' .reccherche_par_email('user1@gmail.com');

if($_SERVER['REQUEST_METHOD']=='POST'){
    $login = $_POST['login'];
    $pwd1 = $_POST['pwd1'];
    $pwd2 = $_POST['pwd2'];
    $email = $_POST['email'];

    if(isset($login)){
        $filtredlogin = filter_var($login, FILTER_SANITIZE_STRING);
        if(strlen($filtredlogin) < 4){
            $erreurs = "Erreur! le login doit contenir au moins plus de 4 Lettres.";
        }
    }

    if(isset($pwd1) && isset($pwd2)){
        if(empty($pwd1)){
            $erreurs = "Erreur! Le mot de passe ne doit pas etre vide.";
        }
        if(sha1($pwd1) !== sha1($pwd2)){
            $erreurs = "Erreur! Les deux mot de passe ne sont pas identiques.";   
        }
    }

    if(isset($email)){
        $filtredemail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!$filtredemail){
            $erreurs = "Erreur! Votre email n'est pas valide."; 
        }
    }

    if(empty($erreurs)){
        if(recherche_par_login($login) == 0 && recherche_par_email($email) == 0){
            $requete = $bdd->prepare("INSERT INTO utilisateur(login, email, role, etat, pwd) VALUES (:plogin, :pemail, :prole, :petat, :ppwd)");
            $requete->execute(array(
                'plogin' => $login,
                'pemail' => $email,
                'prole' => 'VISITEUR',
                'petat' => 0,
                'ppwd' => sha1($pwd1),
            ));
            $erreurs = "Votre compte est créé mais temporairement inactif jusqu'à activation par l'Admin!";
        } else {
            if(recherche_par_login($login) > 0){
                $erreurs = "Le login est déjà utilisé";
            }
            if(recherche_par_email($email) > 0){
                $erreurs = "L'email est déjà utilisé !";
            }
        }
    }
}
}else{
    header('location:../pages/login.php');
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <title>Nouvelle utilisateur</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Stock</title>
    <link rel="stylesheet" type="text/css" href="../css/style1.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../font/all.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        body {
            background-color: #f0f0f0; /* Changez cette valeur pour la couleur souhaitée */
        }
        h1 {
            font-size: 40px;
            color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container col-md-6 col-md-offset-3">
        <h1>Creation d'un nouveau compte utilisateur</h1>

        <?php if (isset($erreurs)) { ?>
            <div class="alert alert-warning"> 
                <strong>Message!</strong>  
                <?php echo $erreurs; ?>
            </div>
        <?php } ?>
        
        <form action="" method="post">
            <div class="mb-3">
                <input type="text" minlength="4" title="login au moins 4 caractères" name="login" placeholder="taper votre nom utilisateur" autocomplete="off" class="form-control">
            </div>
            <div class="mb-3">
                <input type="password" minlength="4" title="le mot de passe doit contenir au moins 4 caractères" name="pwd1" placeholder="Votre mot de passe" autocomplete="off" class="form-control">
            </div>
            <div class="mb-3">
                <input type="password" minlength="4" name="pwd2" placeholder="Confirmer mot de passe" autocomplete="off" class="form-control">
            </div>
            <div class="mb-3">
                <input type="email" minlength="4" name="email" placeholder="Votre email" autocomplete="off" class="form-control">
            </div>

            <input type="submit" class="btn btn-primary" value="Enregistrer">
        </form>
    </div>
</body>
</html>
