<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$erreurs = ""; // Initialiser la variable erreurs

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require "include/sidebar.php";
} else {
   
    header('location:../pages/login.php');
    exit; // Assurez-vous que le script s'arrête après la redirection
}
?>
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Admin Dashboard</h4>
           <?php
if (isset($_GET['erreurs'])) {
    $erreurs = htmlspecialchars($_GET['erreurs']);
    echo '<div class="alert alert-warning">';
    echo '<strong>Erreur!</strong> ' . $erreurs;
    echo '</div>';
}
?>
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
    </div>
</main>
<?php require "include/footer.php"; ?>
