<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";

    // Récupérer l'ID de la vente à modifier (par exemple via une requête GET)
    $id_vente = isset($_GET['idP']) ? (int) $_GET['idP'] : null;

    // Vérifiez si l'ID de la vente est valide
    if ($id_vente) {
        // Récupérer les données de la vente à modifier
        $reqVente = $bdd->prepare("SELECT * FROM vente WHERE id_vente = :id_vente");
        $reqVente->bindParam(':id_vente', $id_vente);
        $reqVente->execute();
        $vente = $reqVente->fetch(PDO::FETCH_ASSOC);

        if ($vente) {
            $reqClient = "SELECT * FROM client";
            $result = $bdd->query($reqClient);
            $clients = $result->fetchAll(PDO::FETCH_ASSOC);

            $reqProd = "SELECT * FROM produit";
            $result = $bdd->query($reqProd);
            $produits = $result->fetchAll(PDO::FETCH_ASSOC);

            if (isset($_POST['ok'])) {
                // Récupérer les données du formulaire
                $id_client = isset($_POST['client_id']) ? (int) $_POST['client_id'] : null;
                $produit_ids = isset($_POST['produit_id']) ? $_POST['produit_id'] : [];
                $quantites = isset($_POST['quantite']) ? $_POST['quantite'] : [];
                $prix_ventes = isset($_POST['prix_vente']) ? $_POST['prix_vente'] : [];
                $totals = isset($_POST['total']) ? $_POST['total'] : [];
                $total_ventes = 0;
                $date_vente = $_POST['date'];

                // Appliquer htmlspecialchars à chaque valeur
                foreach ($totals as $key => $value) {
                    $totals[$key] = htmlspecialchars($value);
                }

                // Calculer total_ventes
                $total_ventes = array_sum($totals);

                // Vérifier que les données nécessaires sont présentes
                if ($id_client && !empty($produit_ids) && !empty($quantites) && !empty($totals) && !empty($date_vente) && $total_ventes > 0) {
                    $bdd->beginTransaction(); // Début de la transaction

                    try {
                        // Mettre à jour la vente existante
                        foreach ($produit_ids as $index => $id_produit) {
                            $quantite = htmlspecialchars($quantites[$index]);
                            $prix_vente = htmlspecialchars($prix_ventes[$index]);

                            // Mettre à jour la table vente
                            $stmt = $bdd->prepare("UPDATE vente SET id_client = :id_client, id_produit = :id_produit, quantite = :quantite, prix_vente = :prix_vente, total = :total, date_vente = :date_vente, total_ventes = :total_ventes WHERE id_vente = :id_vente");
                            $stmt->bindParam(':id_client', $id_client);
                            $stmt->bindParam(':id_produit', $id_produit);
                            $stmt->bindParam(':quantite', $quantite);
                            $stmt->bindParam(':prix_vente', $prix_vente);
                            $stmt->bindParam(':total', $totals[$index]);
                            $stmt->bindParam(':date_vente', $date_vente);
                            $stmt->bindParam(':total_ventes', $total_ventes);
                            $stmt->bindParam(':id_vente', $id_vente);
                            $stmt->execute();

                            // Mettre à jour la table produit (ajuster la quantité dans le stock)
                            $stmt_update = $bdd->prepare("UPDATE produit SET quantite = quantite - :quantite WHERE id_produit = :id_produit");
                            $stmt_update->bindParam(':quantite', $quantite);
                            $stmt_update->bindParam(':id_produit', $id_produit);
                            $stmt_update->execute();
                        }

                        $bdd->commit(); // Valider la transaction
                        $_SESSION['success_message'] = "Vente mise à jour avec succès !";
                        header('location:../pages/vente.php');
                    } catch (Exception $e) {
                        $bdd->rollBack();
                        $erreurs = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $erreurs = "Veuillez remplir tous les champs obligatoires.";
                }
            }
        } else {
            $erreurs = "Vente non trouvée.";
        }
    } else {
        $erreurs = "ID de vente non valide.";
    }
} else {
    header('location:../pages/login.php');
}
require "../include/sidebar.php";
?>

<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Modifier Vente</h4>
        </div>

        <?php if (isset($erreurs)) { ?>
            <div class="alert alert-warning">
                <strong>Message!</strong>
                <?= htmlspecialchars($erreurs); ?>
            </div>
        <?php } ?>

        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">Modification des ventes</h5>
            </div>
            <div class="card-body">
                <?php if (isset($vente)) { ?>
                <form action="" method="POST">
                    <div class="mb-3 row">
                        <label for="client" class="col-sm-2 col-form-label">Client</label>
                        <div class="col-sm-10">
                            <select name="client_id" class="form-select" id="clientSelect" aria-label="Default select example">
                                <option selected>Choisir un client</option>
                                <?php foreach ($clients as $client) { ?>
                                    <option value="<?= $client['id_client'] ?>" <?= $client['id_client'] == $vente['id_client'] ? 'selected' : '' ?>><?= htmlspecialchars($client['nom']) ?> <?= htmlspecialchars($client['prenom']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div id="productEntries">
                        <div class="productEntry mb-3 row">
                            <label for="produit" class="col-sm-2 col-form-label">Produit</label>
                            <div class="col-sm-10">
                                <select name="produit_id[]" class="form-select productSelect" aria-label="Default select example">
                                    <option selected>Choisir un produit</option>
                                    <?php foreach ($produits as $produit) { ?>
                                        <option value="<?= $produit['id_produit'] ?>" data-price="<?= $produit['prix_vente'] ?>" <?= $produit['id_produit'] == $vente['id_produit'] ? 'selected' : '' ?>><?= htmlspecialchars($produit['nom_produit']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label for="quantite" class="col-sm-2 col-form-label">Quantité</label>
                            <div class="col-sm-10">
                                <input type="number" name="quantite[]" class="form-control quantite" value="<?= htmlspecialchars($vente['quantite']) ?>" placeholder="Quantité">
                            </div>
                            <label for="prix" class="col-sm-2 col-form-label">Prix de vente</label>
                            <div class="col-sm-10">
                                <input type="number" name="prix_vente[]" step="0.01" class="form-control prix" value="<?= htmlspecialchars($vente['prix_vente']) ?>" placeholder="Prix de vente" readonly>
                            </div>
                            
                            <input type="hidden" name="total[]" class="form-control total" value="<?= htmlspecialchars($vente['total']) ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="button" class="btn btn-secondary" id="addProduct">Ajouter un autre produit</button>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="total_ventes" class="col-sm-2 col-form-label">Total des ventes</label>
                        <div class="col-sm-10">
                            <input type="number" name="total_ventes" class="form-control" id="totalVentes" value="<?= htmlspecialchars($vente['total_ventes']) ?>" placeholder="Total des ventes" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="date" class="col-sm-2 col-form-label">Date de la vente</label>
                        <div class="col-sm-10">
                            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($vente['date_vente']) ?>" required>
                        </div>
                    </div>
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" name="ok" class="btn btn-primary mb-3">Modifier la vente</button>
                        <a href="vente.php" class="btn btn-outline-warning mb-3">Annuler</a>
                    </div>
                </form>
                <?php } else { ?>
                    <div class="alert alert-warning">
                        Vente non trouvée.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const productEntries = document.getElementById('productEntries');
    const totalVentesInput = document.getElementById('totalVentes');

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.total').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalVentesInput.value = total.toFixed(2);
    }

    function addProductEntry() {
        const productEntry = document.querySelector('.productEntry').cloneNode(true);
        productEntries.appendChild(productEntry);
        productEntry.querySelector('.quantite').value = '';
        productEntry.querySelector('.prix').value = '';
        productEntry.querySelector('.total').value = '';
        productEntry.querySelector('.productSelect').value = '';
        attachListeners(productEntry);
    }

    function attachListeners(entry) {
        const quantiteInput = entry.querySelector('.quantite');
        const prixInput = entry.querySelector('.prix');
        const totalInput = entry.querySelector('.total');
        const productSelect = entry.querySelector('.productSelect');

        productSelect.addEventListener('change', function () {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            prixInput.value = price;
            totalInput.value = (parseFloat(price) * parseFloat(quantiteInput.value || 0)).toFixed(2);
            updateTotal();
        });

        quantiteInput.addEventListener('input', function () {
            totalInput.value = (parseFloat(prixInput.value) * parseFloat(quantiteInput.value || 0)).toFixed(2);
            updateTotal();
        });
    }

    document.getElementById('addProduct').addEventListener('click', addProductEntry);
    document.querySelectorAll('.productEntry').forEach(attachListeners);
    updateTotal();
});
</script>
