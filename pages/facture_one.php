<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";
// Activer le mode d'erreurs PDO
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si l'ID de la facture est présent dans l'URL et qu'il est valide
if (isset($_GET['id_vente']) && intval($_GET['id_vente']) > 0) {
    $id_vente = intval($_GET['id_vente']);
} else {
    die("Erreur : ID de la facture manquant ou invalide.");
}

// Requête pour récupérer les informations de vente
$reqvente = "SELECT vente.*, client.nom as client_nom, client.prenom as client_prenom, client.adresse as client_adresse, produit.nom_produit as produit_nom, produit.prix_vente as produit_prix 
             FROM vente 
             JOIN client ON vente.id_client = client.id_client
             JOIN produit ON vente.id_produit = produit.id_produit
             WHERE vente.id_vente = :id_vente";
$result = $bdd->prepare($reqvente);
$result->execute([':id_vente' => $id_vente]);

if ($result->rowCount() > 0) {
    $infos_vente = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("Aucune vente trouvée pour cette facture.");
}

$tva_rate = 0.18; // 18% de TVA

} else{
    header('Location: ../pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
        }
        .invoice-container {
            width: 80%;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .header, .footer {
            background-color: #f9f9f9;
            padding: 10px;
        }
        .header h1, .footer h3 {
            margin: 0;
        }
        .details, .totals {
            width: 100%;
            margin-top: 20px;
        }
        .details td, .totals td {
            padding: 8px;
            border: 1px solid #ccc;
        }
        .totals {
            margin-top: 40px;
        }
        .totals td {
            text-align: right;
        }
        .details th, .details td {
            text-align: left; /* or center, depending on your preference */
            padding: 8px;
            border: 1px solid #ccc;
        }

        .details th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .details td {
            text-align: right; /* Align the content to the right */
        }
    </style>
</head>
<body>
    <!-- onclick="window.print() -->
    <button id="btnPrint">Imprimer la facture</button>
    <div class="invoice-container">
        <div class="header">
            <h1>Niang et frere</h1>
            <p>Adresse | CP Ville: Ziguincor | Téléphone: 779481402 / Fax</p>
        </div>

       <table class="details">
        <?php foreach ($infos_vente as $infos) { ?>
            <tr>
                <td>Référence : <strong><?php echo htmlspecialchars($infos['id_vente']); ?></strong></td>
                <td>Date : <strong><?php echo htmlspecialchars($infos['date_vente']); ?></strong></td>
                <td>N° client : <strong><?php echo htmlspecialchars($infos['id_client']); ?></strong></td>
            </tr>
            <tr>
                <td>Nom du client : <strong><?php echo htmlspecialchars($infos['client_nom']); ?></strong></td>
                <td>Prenom du client : <strong><?php echo htmlspecialchars($infos['client_prenom']); ?></strong></td>
                <td>Adresse : <strong><?php echo htmlspecialchars($infos['client_adresse']); ?></strong></td>
            </tr>
        <?php } ?>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>Quantité</th>
                    <th>Désignation</th>
                    <th>Prix unitaire HT</th>
                    <th>Prix total HT</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($infos_vente as $infos) {
                    $prix_ht = $infos['quantite'] * $infos['produit_prix'];
                    $tva = $prix_ht * $tva_rate;
                    $prix_ttc = $prix_ht + $tva;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($infos['quantite']); ?></td>
                    <td><?php echo htmlspecialchars($infos['produit_nom']); ?></td>
                    <td><?php echo number_format($infos['produit_prix'], 2); ?> FCFA</td>
                    <td><?php echo number_format($prix_ht, 2); ?> FCFA</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Total Hors Taxe</td>
                <td><?php echo number_format($prix_ht, 2); ?> FCFA</td>
            </tr>
            <tr>
                <td>TVA à 18%</td>
                <td><?php echo number_format($tva, 2); ?> FCFA</td>
            </tr>
            <tr>
                <td>Total TTC en FCFA</td>
                <td><strong><?php echo number_format($prix_ttc, 2); ?> FCFA</strong></td>
            </tr>
        </table>

        <div class="footer">
            <h3>En votre aimable règlement, Cordialement,</h3>
        </div>
    </div>
   <script>
        var btnPrint = document.querySelector('#btnPrint');
        btnPrint.addEventListener("click", () => {
            window.print();
        });
    </script>
</body>
</html>


