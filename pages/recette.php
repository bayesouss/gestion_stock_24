<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";

    // Function to insert revenue into recette table
    function insertRevenue($bdd, $period_type, $period, $revenue) {
        $stmt = $bdd->prepare("INSERT INTO recette (period_type, period, total_ventes, total_achats, revenue) VALUES (:period_type, :period, :total_ventes, :total_achats, :revenue)");
        $stmt->bindParam(':period_type', $period_type);
        $stmt->bindParam(':period', $period);
        $stmt->bindParam(':total_ventes', $total_ventes);
        $stmt->bindParam(':total_achats', $total_achats);
        $stmt->bindParam(':revenue', $revenue);
        $stmt->execute();
    }

    // Clear the recette table before inserting new values
    $bdd->exec("TRUNCATE TABLE recette");

    // Daily Revenue
    $stmt_daily = $bdd->query("SELECT DATE(date_vente) AS sale_date, SUM(total_ventes) AS total_ventes, SUM(total_achats) AS total_achats, SUM(total_ventes) - SUM(total_achats) AS daily_revenue FROM vente GROUP BY DATE(date_vente)");
    $daily_revenues = $stmt_daily->fetchAll(PDO::FETCH_ASSOC);
    foreach ($daily_revenues as $row) {
        insertRevenue($bdd, 'daily', $row['sale_date'], $row['daily_revenue']);
    }

    // Monthly Revenue
    $stmt_monthly = $bdd->query("SELECT DATE_FORMAT(date_vente, '%Y-%m') AS sale_month, SUM(total_ventes) AS total_ventes, SUM(total_achats) AS total_achats, SUM(total_ventes) - SUM(total_achats) AS monthly_revenue FROM vente GROUP BY DATE_FORMAT(date_vente, '%Y-%m')");
    $monthly_revenues = $stmt_monthly->fetchAll(PDO::FETCH_ASSOC);
    foreach ($monthly_revenues as $row) {
        insertRevenue($bdd, 'monthly', $row['sale_month'], $row['monthly_revenue']);
    }

    // Annual Revenue
    $stmt_annual = $bdd->query("SELECT YEAR(date_vente) AS sale_year, SUM(total_ventes) AS total_ventes, SUM(total_achats) AS total_achats, SUM(total_ventes) - SUM(total_achats) AS annual_revenue FROM vente GROUP BY YEAR(date_vente)");
    $annual_revenues = $stmt_annual->fetchAll(PDO::FETCH_ASSOC);
    foreach ($annual_revenues as $row) {
        insertRevenue($bdd, 'annual', $row['sale_year'], $row['annual_revenue']);
    }

    echo "Revenues calculated and inserted into recette table.";
} else {
    header('location:../pages/login.php');
}
?>
