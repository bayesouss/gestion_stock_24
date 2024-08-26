<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
$userinfo = $_SESSION['user'];
}else{
    header('location:../pages/login.php');
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Stock</title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../font/all.min.css">
    <!-- pour la recherche automatique -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <!-- Content For Sidebar -->
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">Gestion Achat et vente</a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Sen Boutique
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link">
                            <i class="fa-solid fa-list pe-2"></i>
                            Tableau de bord
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="produit.php" class="sidebar-link collapsed" data-bs-target="#pages"
                            data-bs-toggle="collapse" aria-expanded="false"><i class="fa-solid fa-file-lines pe-2"></i>
                            Produits
                        </a>
                        <ul id="pages" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="../pages/produit.php" class="sidebar-link">Produit</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="../pages/categorie.php" class="sidebar-link">Categorie</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-target="#posts" data-bs-toggle="collapse"
                            aria-expanded="false"><i class="fa-solid fa-sliders pe-2"></i>
                            Traitement
                        </a>
                        <ul id="posts" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="../pages/client.php" class="sidebar-link">Client</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="../pages/fournisseur.php" class="sidebar-link">Fournisseur</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="../pages/Achat.php" class="sidebar-link">Achats</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="../pages/vente.php" class="sidebar-link">Vente</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="../pages/formrecette.php" class="sidebar-link">Recette </a>
                            </li>
                            
                        </ul>
                    </li>
                     <li class="sidebar-item">
                <a href="../pages/login.php" class="sidebar-link">Se deconnecter</a>
            </li>

                   <?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté et que son rôle est 'admin'
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin') {
?>
    <li class="sidebar-item">
        <a href="#" class="sidebar-link collapsed" data-bs-target="#auth" data-bs-toggle="collapse"
            aria-expanded="false"><i class="fa-regular fa-user pe-2"></i>
            Gestion Utilisateur
        </a>
        <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
           
           
            <li class="sidebar-item">
                <a href="../pages/utilisateur.php" class="sidebar-link">Liste des utilisateur</a>
            </li>
        </ul>
    </li>
<?php
}
?>

                </ul>
            </div>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand px-3 border-bottom">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-collapse navbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                             
                             Bonjour:  &nbsp;<i class="fas fa-user"></i> <?php echo htmlspecialchars($userinfo['login']); ?>
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                 &nbsp;
                                <img src="../image/profile.jpg" class="avatar img-fluid rounded" alt="">
                              
                              
                               
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- <a href="#" class="dropdown-item">Profile</a>
                                <a href="#" class="dropdown-item">Setting</a> -->
                                <a href="../pages/deconnection.php" class="dropdown-item"> <i class="fa fa-sign-out" aria-hidden="true"></i></i>Se deconnecter</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

