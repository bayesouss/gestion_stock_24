<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";

    $reqfournisseur = "SELECT * FROM fournisseur";
    $result = $bdd->query($reqfournisseur);
    $fournisseurs = $result->fetchAll(PDO::FETCH_ASSOC);

    $reqProd = "SELECT * FROM produit";
    $result = $bdd->query($reqProd);
    $produits = $result->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['ok'])) {
        // Récupérer les données du formulaire
        $id_fournisseur = isset($_POST['fournisseur_id']) ? (int) $_POST['fournisseur_id'] : null;
        $produit_ids = isset($_POST['produit_id']) ? $_POST['produit_id'] : [];
        $quantites = isset($_POST['quantite']) ? $_POST['quantite'] : [];
        $prix_achats = isset($_POST['prix_achat']) ? $_POST['prix_achat'] : [];
        $prix_ventes = isset($_POST['prix_vente']) ? $_POST['prix_vente'] : [];
        $total = htmlspecialchars($_POST['total']);
        $total_achats = htmlspecialchars($_POST['total_achats']);
        $date_achat = $_POST['date'];

        // Définir id_client pour l'insertion
        $id_client = 0; // Définir une valeur par défaut ou récupérer selon votre logique

        // Vérifier que les données nécessaires sont présentes
        if ($id_fournisseur && !empty($produit_ids) && !empty($quantites) && !empty($total) && !empty($date_achat) && !empty($total_achats)) {
            $bdd->beginTransaction(); // Début de la transaction

            try {
                $id_achats = [];

                foreach ($produit_ids as $index => $id_produit) {
                    $quantite = htmlspecialchars($quantites[$index]);
                    $prix_achat = htmlspecialchars($prix_achats[$index]);
                    // $prix_vente = htmlspecialchars($prix_ventes[$index]);

                    // Préparer et exécuter la requête pour insérer l'achat
                    $stmt = $bdd->prepare("INSERT INTO achat (id_fournisseur, id_produit, quantite, prix_achat, total, date_achat, total_achats) VALUES (:id_fournisseur, :id_produit, :quantite, :prix_achat, :total, :date_achat, :total_achats)");
                    $stmt->bindParam(':id_fournisseur', $id_fournisseur);
                    $stmt->bindParam(':id_produit', $id_produit);
                    $stmt->bindParam(':quantite', $quantite);
                    $stmt->bindParam(':prix_achat', $prix_achat);
                    $stmt->bindParam(':total', $total);
                    $stmt->bindParam(':date_achat', $date_achat);
                    $stmt->bindParam(':total_achats', $total_achats);
                    $stmt->execute();
                    $id_achats[] = $bdd->lastInsertId();

                    // Mettre à jour la table produit (ajouter la quantité achetée au stock existant)
                    $stmt_update = $bdd->prepare("UPDATE produit SET quantite = quantite + :quantite WHERE id_produit = :id_produit");
                    $stmt_update->bindParam(':quantite', $quantite);
                    $stmt_update->bindParam(':id_produit', $id_produit);
                    $stmt_update->execute();
                }
// Code d'achat
$total_ventes = 0;

// Récupérer total_ventes pour la période donnée
$stmt_ventes = $bdd->prepare("SELECT SUM(total_ventes) as total_ventes FROM vente WHERE date_vente = :date_achat");
$stmt_ventes->bindParam(':date_achat', $date_achat);
$stmt_ventes->execute();
$result_ventes = $stmt_ventes->fetch(PDO::FETCH_ASSOC);
if ($result_ventes) {
    $total_ventes = $result_ventes['total_ventes'];
}

// Calculer la recette comme la différence entre total_ventes et total_achats
$stmt_recette = $bdd->prepare("
    INSERT INTO recette (period, period_type, total_achats, revenue) 
    VALUES (:date_achat, 'daily', :total_achats, :total_achats) 
    ON DUPLICATE KEY UPDATE 
        total_achats = total_achats + :total_achats,
        revenue = revenue - :total_achats
");
$stmt_recette->bindParam(':date_achat', $date_achat);
$stmt_recette->bindParam(':total_achats', $total_achats);

$stmt_recette->execute();


                $bdd->commit(); // Valider la transaction
                $erreurs = "Achat bien ajouté !";

            } catch (Exception $e) {
                $bdd->rollBack();
                $erreurs = "Erreur : " . $e->getMessage();
            }
        } else {
            $erreurs = "Veuillez remplir tous les champs obligatoires.";
        }
    }
} else {
    header('location:../pages/login.php');
}
require "../include/sidebar.php";
?>

<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Enregistrement achats</h4>
        </div>

        <?php if (isset($erreurs)) { ?>
            <div class="alert alert-warning">
                <strong>Message!</strong>
                <?= htmlspecialchars($erreurs); ?>
            </div>
        <?php } ?>

        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">Insertion des achats</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3 row">
                        <label for="fournisseur" class="col-sm-2 col-form-label">Fournisseur</label>
                        <div class="col-sm-10">
                            <select name="fournisseur_id" class="form-select" id="fournisseurSelect" aria-label="Default select example">
                                <option selected>Open this select menu</option>
                                <?php foreach ($fournisseurs as $fournisseur) { ?>
                                    <option value="<?= $fournisseur['id_fournisseur'] ?>"><?= htmlspecialchars($fournisseur['nom']) ?> <?= htmlspecialchars($fournisseur['prenom']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div id="productEntries">
                        <div class="productEntry mb-3 row">
                            <label for="produit" class="col-sm-2 col-form-label">Produit</label>
                            <div class="col-sm-10">
                                <select name="produit_id[]" class="form-select productSelect" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <?php foreach ($produits as $produit) { ?>
                                        <option value="<?= $produit['id_produit'] ?>" data-price="<?= $produit['prix_achat'] ?>"><?= htmlspecialchars($produit['nom_produit']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label for="quantite" class="col-sm-2 col-form-label">Quantité</label>
                            <div class="col-sm-10">
                                <input type="number" name="quantite[]" class="form-control quantite" placeholder="Quantité">
                            </div>
                            <label for="prix" class="col-sm-2 col-form-label">Prix d'achat</label>
                            <div class="col-sm-10">
                                <input type="number" name="prix_achat[]" step="0.01" class="form-control prix" placeholder="Prix d'achat" readonly>
                            </div>
                            
                            <input type="hidden" name="total[]" class="form-control total">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="button" class="btn btn-secondary" id="addProduct">Ajouter un autre produit</button>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="total" class="col-sm-2 col-form-label">Total</label>
                        <div class="col-sm-10">
                            <input type="text" name="total" id="total" class="form-control" placeholder="Total" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="total_achats" class="col-sm-2 col-form-label">Total achats</label>
                        <div class="col-sm-10">
                            <input type="text" name="total_achats" class="form-control" id="total_achats" placeholder="Total achats" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="date" class="col-sm-2 col-form-label">Date</label>
                        <div class="col-sm-10">
                            <input type="date" name="date" class="form-control" id="date">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" name="ok" class="btn btn-primary mb-3">Enregistrer</button>
                            <a href="achat.php" class="btn btn-outline-warning mb-3">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('addProduct').addEventListener('click', function() {
        let productEntry = document.querySelector('.productEntry').cloneNode(true);
        productEntry.querySelectorAll('input').forEach(input => input.value = '');
        document.getElementById('productEntries').appendChild(productEntry);
        calculateTotal();
        calculateTotalSale();
    });

    document.addEventListener('change', function(event) {
        if (event.target.matches('.productSelect')) {
            let price = event.target.options[event.target.selectedIndex].dataset.price;
            let entry = event.target.closest('.productEntry');
            entry.querySelector('.prix').value = price;
            let quantity = parseFloat(entry.querySelector('.quantite').value) || 0;
            entry.querySelector('.total').value = (price * quantity).toFixed(2);
        }
        calculateTotal();
        calculateTotalSale();
    });

    document.addEventListener('input', function(event) {
        if (event.target.matches('.quantite')) {
            let entry = event.target.closest('.productEntry');
            let price = parseFloat(entry.querySelector('.prix').value) || 0;
            let quantity = parseFloat(event.target.value) || 0;
            entry.querySelector('.total').value = (price * quantity).toFixed(2);
        }
        calculateTotal();
        calculateTotalSale();
    });

    document.addEventListener('click', function(event) {
        if (event.target.matches('.removeProduct')) {
            let entry = event.target.closest('.productEntry');
            entry.remove();
            calculateTotal();
            calculateTotalSale();
        }
    });

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.productEntry').forEach(entry => {
            let quantity = parseFloat(entry.querySelector('.quantite').value) || 0;
            let price = parseFloat(entry.querySelector('.prix').value) || 0;
            total += quantity * price;
        });
        document.getElementById('total').value = total.toFixed(2);
    }

    function calculateTotalSale() {
        let total_achats = 0;
        document.querySelectorAll('.productEntry').forEach(entry => {
            let total = parseFloat(entry.querySelector('.total').value) || 0;
            total_achats += total;
        });
        document.getElementById('total_achats').value = total_achats.toFixed(2);
    }
});
</script>

<?php require "../include/footer.php"; ?>

