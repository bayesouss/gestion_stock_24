<?php
// Démarrer une session si aucune session n'a encore été démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté, sinon redirigez vers la page de connexion
if (isset($_SESSION['user'])) {
    require_once "../configuration/conect.php";

    // Récupérer l'ID de l'achat à modifier (par exemple via une requête GET)
    $id_achat = isset($_GET['idP']) ? (int) $_GET['idP'] : null;

    // Vérifiez si l'ID de l'achat est valide
    if ($id_achat) {
        // Récupérer les données de l'achat à modifier
        $reqAchat = $bdd->prepare("SELECT * FROM achat WHERE id_achat = :id_achat");
        $reqAchat->bindParam(':id_achat', $id_achat);
        $reqAchat->execute();
        $achat = $reqAchat->fetch(PDO::FETCH_ASSOC);

        if ($achat) {
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
                $totals = isset($_POST['total']) ? $_POST['total'] : [];
                $total_achats = 0;
                $date_achat = $_POST['date'];

                // Appliquer htmlspecialchars à chaque valeur
                foreach ($totals as $key => $value) {
                    $totals[$key] = htmlspecialchars($value);
                }

                // Calculer total_achats
                $total_achats = array_sum($totals);

                // Vérifier que les données nécessaires sont présentes
                if ($id_fournisseur && !empty($produit_ids) && !empty($quantites) && !empty($totals) && !empty($date_achat) && $total_achats > 0) {
                    $bdd->beginTransaction(); // Début de la transaction

                    try {
                        // Mettre à jour l'achat existant
                        foreach ($produit_ids as $index => $id_produit) {
                            $quantite = htmlspecialchars($quantites[$index]);
                            $prix_achat = htmlspecialchars($prix_achats[$index]);

                            // Mettre à jour la table achat
                            $stmt = $bdd->prepare("UPDATE achat SET id_fournisseur = :id_fournisseur, id_produit = :id_produit, quantite = :quantite, prix_achat = :prix_achat, total = :total, date_achat = :date_achat, total_achats = :total_achats WHERE id_achat = :id_achat");
                            $stmt->bindParam(':id_fournisseur', $id_fournisseur);
                            $stmt->bindParam(':id_produit', $id_produit);
                            $stmt->bindParam(':quantite', $quantite);
                            $stmt->bindParam(':prix_achat', $prix_achat);
                            $stmt->bindParam(':total', $totals[$index]);
                            $stmt->bindParam(':date_achat', $date_achat);
                            $stmt->bindParam(':total_achats', $total_achats);
                            $stmt->bindParam(':id_achat', $id_achat);
                            $stmt->execute();

                            // Mettre à jour la table produit (ajuster la quantité dans le stock)
                            $stmt_update = $bdd->prepare("UPDATE produit SET quantite = quantite + :quantite WHERE id_produit = :id_produit");
                            $stmt_update->bindParam(':quantite', $quantite);
                            $stmt_update->bindParam(':id_produit', $id_produit);
                            $stmt_update->execute();
                        }

                        $bdd->commit(); // Valider la transaction

                        $_SESSION['success_message'] = "Achat mis à jour avec succès !";
                        header('location:../pages/achat.php');
                    } catch (Exception $e) {
                        $bdd->rollBack();
                        $erreurs = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $erreurs = "Veuillez remplir tous les champs obligatoires.";
                }

            }
        } else {
            $erreurs = "Achat non trouvé.";
        }
    } else {
        $erreurs = "ID d'achat non valide.";
    }
} else {
    header('location:../pages/login.php');
}
require "../include/sidebar.php";
?>

<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h4>Modifier Achat</h4>
        </div>

        <?php if (isset($erreurs)) { ?>
            <div class="alert alert-warning">
                <strong>Message!</strong>
                <?= htmlspecialchars($erreurs); ?>
            </div>
        <?php } ?>

        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title">Modification des achats</h5>
            </div>
            <div class="card-body">
                <?php if (isset($achat)) { ?>
                <form action="" method="POST">
                    <div class="mb-3 row">
                        <label for="fournisseur" class="col-sm-2 col-form-label">Fournisseur</label>
                        <div class="col-sm-10">
                            <select name="fournisseur_id" class="form-select" id="fournisseurSelect" aria-label="Default select example">
                                <option selected>Choisir un fournisseur</option>
                                <?php foreach ($fournisseurs as $fournisseur) { ?>
                                    <option value="<?= $fournisseur['id_fournisseur'] ?>" <?= $fournisseur['id_fournisseur'] == $achat['id_fournisseur'] ? 'selected' : '' ?>><?= htmlspecialchars($fournisseur['nom']) ?> <?= htmlspecialchars($fournisseur['prenom']) ?></option>
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
                                        <option value="<?= $produit['id_produit'] ?>" data-price="<?= $produit['prix_achat'] ?>" <?= $produit['id_produit'] == $achat['id_produit'] ? 'selected' : '' ?>><?= htmlspecialchars($produit['nom_produit']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label for="quantite" class="col-sm-2 col-form-label">Quantité</label>
                            <div class="col-sm-10">
                                <input type="number" name="quantite[]" class="form-control quantite" value="<?= htmlspecialchars($achat['quantite']) ?>" placeholder="Quantité">
                            </div>
                            <label for="prix" class="col-sm-2 col-form-label">Prix d'achat</label>
                            <div class="col-sm-10">
                                <input type="number" name="prix_achat[]" step="0.01" class="form-control prix" value="<?= htmlspecialchars($achat['prix_achat']) ?>" placeholder="Prix d'achat" readonly>
                            </div>
                            
                            <input type="hidden" name="total[]" class="form-control total" value="<?= htmlspecialchars($achat['total']) ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="button" class="btn btn-secondary" id="addProduct">Ajouter un autre produit</button>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="total_achats" class="col-sm-2 col-form-label">Total des achats</label>
                        <div class="col-sm-10">
                            <input type="number" name="total_achats" class="form-control" id="totalAchats" value="<?= htmlspecialchars($achat['total_achats']) ?>" placeholder="Total des achats" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="date" class="col-sm-2 col-form-label">Date d'achat</label>
                        <div class="col-sm-10">
                            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($achat['date_achat']) ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" name="ok" class="btn btn-primary  mb-3 ">Modifier</button>
                             <a href="achat.php" class="btn btn-outline-warning mb-3">Annuler</a>
                        </div>
                    </div>
                </form>
                <?php } else { ?>
                    <div class="alert alert-danger">
                        Achat non trouvé.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productEntries = document.getElementById('productEntries');
    const totalAchatsInput = document.getElementById('totalAchats');

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.total').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalAchatsInput.value = total.toFixed(2);
    }

    function addProductEntry() {
        const productEntry = document.querySelector('.productEntry').cloneNode(true);
        productEntries.appendChild(productEntry);

        // Réinitialiser les champs de la nouvelle entrée de produit
        productEntry.querySelector('.quantite').value = '';
        productEntry.querySelector('.prix').value = '';
        productEntry.querySelector('.total').value = '';
        productEntry.querySelector('.productSelect').value = '';

        // Attacher les écouteurs d'événements aux champs
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

        // Mise à jour du total lors de la modification du prix
        prixInput.addEventListener('input', function () {
            totalInput.value = (parseFloat(prixInput.value) * parseFloat(quantiteInput.value || 0)).toFixed(2);
            updateTotal();
        });
    }

    // Attacher les écouteurs aux entrées de produit existantes
    document.querySelectorAll('.productEntry').forEach(attachListeners);

    // Ajouter une nouvelle entrée de produit
    document.getElementById('addProduct').addEventListener('click', addProductEntry);

    // Mettre à jour le total général lors du chargement initial
    updateTotal();
});

</script>

<?php require "../include/footer.php"; ?>
