<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if ($_SESSION['user']['role']=='admin') {
    require_once "../configuration/conect.php";

    $daily_revenues = $bdd->query("
        SELECT DATE_FORMAT(period, '%Y-%m-%d') AS day, SUM(revenue) AS revenue 
        FROM recette 
        WHERE period_type = 'daily'
        GROUP BY day
        ORDER BY day DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $monthly_revenues = $bdd->query("
        SELECT DATE_FORMAT(period, '%Y-%m') AS month, SUM(revenue) AS revenue 
        FROM recette 
        WHERE period_type = 'daily'
        GROUP BY month
        ORDER BY month DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $annual_revenues = $bdd->query("
        SELECT DATE_FORMAT(period, '%Y') AS year, SUM(revenue) AS revenue 
        FROM recette 
        WHERE period_type = 'daily'
        GROUP BY year
        ORDER BY year DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    require "../include/sidebar.php";
} else {

   if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $erreurs = "Vous n'êtes pas autorisé à accéder à cette page!";
    header('Location: ../index.php?erreurs=' . urlencode($erreurs));
    exit; // Assurez-vous que le script s'arrête après la redirection
}
    
}
?>

<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Admin </h4>
        </div>

        <?php if (isset($erreurs)) { ?>
            <div class="alert alert-warning">
                <strong>Message!</strong>
                <?= htmlspecialchars($erreurs); ?>
            </div>
        <?php } ?>

        <div class="card border-0">
            <div class="card-header" >
                <h5 class="card-title">Rapport sur les revenus</h5>
            </div>
            <div class="card-body">
                <!-- Chiffre d'affaire journalier -->
                <h2 class="text-bg-secondary p-3">Chiffre d'affaire journalier</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_revenues as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['day']); ?></td>
                            <td><?php echo number_format($row['revenue'], 2); ?> &nbsp; FCFA</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Chiffre d'affaire Mensuel -->
                <h2 class="text-bg-secondary p-3">Chiffre d'affaire Mensuel</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_revenues as $month): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($month['month']); ?></td>
                            <td><?php echo number_format($month['revenue'], 2); ?> &nbsp; FCFA</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Chiffre d'affaire annuel -->
                <h2 class="text-bg-secondary p-3">Chiffre d'affaire annuel</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Année</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($annual_revenues as $year): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($year['year']); ?></td>
                            <td><?php echo number_format($year['revenue'], 2); ?>  &nbsp;FCFA</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require"../include/footer.php"; ?>
