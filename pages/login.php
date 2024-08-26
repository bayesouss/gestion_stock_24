<?php
session_start();

// Vérifiez si des erreurs existent dans la session et les assigner à la variable locale
if (isset($_SESSION['erreurLogin'])) $erreurs=$_SESSION['erreurLogin'];
// Effacez les erreurs de la session pour le prochain chargement de page
else{
    $erreurs="";
}
session_destroy();
// unset($_SESSION['erreurLogin']);


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <title>Se connecter</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Stock</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0; /* Changez cette valeur pour la couleur souhaitée */
        }
        h5 {
            font-size: 40px;
            color: #2980b9;
        }
        .card {
            margin-top: 70px;
        }
    </style>
</head>
<body>
    <div class="container col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
        <div class="card">
            <h5 class="card-header">Se connecter</h5>
            <?php if ($erreurs) { ?>
                <div class="alert alert-warning">
                    <strong>Message!</strong>
                    <?php echo $erreurs; ?>
                </div>
                <?php unset($_SESSION['erreurLogin']); ?>
            <?php } ?>
            <div class="card-body">
                <form action="seconnecter.php" method="post">
                    <div class="mb-3">
                        <input type="text" minlength="4" title="login au moins 4 caractères" name="login" placeholder="taper votre nom utilisateur" autocomplete="off" class="form-control">
                    </div>
                    <div class="mb-3">
                        <input type="password" minlength="3" title="le mot de passe doit contenir au moins 4 caractères" name="pwd" placeholder="Votre mot de passe" autocomplete="off" class="form-control">
                    </div>
                    <input type="submit"  class="btn btn-primary" value="Enregistrer">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
