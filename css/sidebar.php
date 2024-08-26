<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Admin Dashboard</title>
   <!--  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"> -->
    <!-- <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script> -->
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
       <link rel="stylesheet" type="text/css" href="../font/all.min.css"> 
     <!-- <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> -->
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
                        <a href="pages/produit.php" class="sidebar-link collapsed" data-bs-target="#pages" data-bs-toggle="collapse"
                            aria-expanded="false"><i class="fa-solid fa-file-lines pe-2"></i>
                            Produits
                        </a>
                        <ul id="pages" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="pages/produit.php" class="sidebar-link">Produit</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="pages/categorie.php" class="sidebar-link">Categorie</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-target="#posts" data-bs-toggle="collapse"
                            aria-expanded="false"><i class="fa-solid fa-sliders pe-2"></i>
                            Traitement et Colobarateurs
                        </a>
                        <ul id="posts" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="pages/client.php" class="sidebar-link">Client</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="pages/fournisseur.php" class="sidebar-link">Fournisseur</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="pages/achat.php" class="sidebar-link">Achats</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="pages/vente.php" class="sidebar-link">Vente</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-target="#auth" data-bs-toggle="collapse"
                            aria-expanded="false"><i class="fa-regular fa-user pe-2"></i>
                            Authentification
                        </a>
                        <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link">Login</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link">Register</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link">Forgot Password</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-header">
                        Multi Level Menu
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-target="#multi" data-bs-toggle="collapse"
                            aria-expanded="false"><i class="fa-solid fa-share-nodes pe-2"></i>
                            Multi Dropdown
                        </a>
                        <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link collapsed" data-bs-target="#level-1"
                                    data-bs-toggle="collapse" aria-expanded="false">Level 1</a>
                                <ul id="level-1" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="#" class="sidebar-link">Level 1.1</a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="#" class="sidebar-link">Level 1.2</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
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
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                <img src="image/profile.jpg" class="avatar img-fluid rounded" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">Profile</a>
                                <a href="#" class="dropdown-item">Setting</a>
                                <a href="#" class="dropdown-item">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>