
<?php

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=tdr;charset=utf8", "root", "");

// Traitement du formulaire
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["idtdr"] ?? '';
    $duree = $_POST["dureeMission"] ?? '';
    $chef = $_POST["chefMission"] ?? '';
    $membre = $_POST["membreMission"] ?? '';
    $itineraire = $_POST["itineraire"] ?? '';

    $frais_mission = $_POST["fraisMission"] ?? 0;
    $carburation = $_POST["carburant"] ?? 0;
    $peage = $_POST["peage"] ?? 0;
    $autres = $_POST["autresFrais"] ?? 0;
    $budget = $frais_mission + $carburation + $peage + $autres;

    if (!empty($id)) {
        try {
            $sql = "INSERT INTO ttdr (idtdr, dureeMission, chefMission, membreMission, itineraire, budgetMission) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $duree, $chef, $membre, $itineraire, $budget]);

            // ✅ Si tout est bon
            header("Location: liste_tdr.php");
            exit;
        } catch (PDOException $e) {
            // ❌ Si erreur (par exemple doublon d'id)
            if ($e->getCode() == 23000) { // Code SQL pour "doublon clé primaire"
 
            header("Location: erreur.php?type=doublon");
            exit;
        }else{
            // ❌ Si ID vide
            header("Location: erreur.php?type=sql");
            exit;

        }
    }
    } else {
        header("Location: erreur.php?type=vide");
        exit;

    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un TDR</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Ajouter un TDR</h2>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="idtdr" class="form-label">ID du TDR</label>
            <input type="text" class="form-control" id="idtdr" name="idtdr" required>
        </div>

        <div class="mb-3">
            <label for="dureeMission" class="form-label">Durée de la mission</label>
            <input type="text" class="form-control" id="dureeMission" name="dureeMission" required>
        </div>

        <div class="mb-3">
            <label for="chefMission" class="form-label">Chef de mission</label>
            <input type="text" class="form-control" id="chefMission" name="chefMission" required>
        </div>

        <div class="mb-3">
            <label for="membreMission" class="form-label">Membres de la mission</label>
            <textarea class="form-control" id="membreMission" name="membreMission" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="itineraire" class="form-label">Itinéraire</label>
            <input type="text" class="form-control" id="itineraire" name="itineraire" required>
        </div>

        <div class="mb-4">
            <label class="form-label"><strong>Budget de la mission</strong></label>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Désignation</th>
                        <th>Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Frais de mission</td>
                        <td><input type="number" class="form-control" name="fraisMission" id="fraisMission" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td>Carburant</td>
                        <td><input type="number" class="form-control" name="carburant" id="carburant" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td>Péage</td>
                        <td><input type="number" class="form-control" name="peage" id="peage" step="0.01" required></td>
                    </tr>
                    <tr>
                        <td>Autres frais</td>
                        <td><input type="number" class="form-control" name="autresFrais" id="autresFrais" step="0.01" required></td>
                    </tr>
                    <tr class="table-success">
                        <th>Total estimé</th>
                        <th><input type="number" class="form-control" name="budgetMission" id="budgetMission" readonly></th>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-4 text-end">
            <a href="liste_tdr.php" class="btn btn-danger px-4">Annuler</a>
            <button type="submit" class="btn btn-success">Ajouter le TDR</button>
        </div>
    </form>
</div>

<script>
    function calculerTotal() {
        const frais = parseFloat(document.getElementById('fraisMission').value) || 0;
        const carburant = parseFloat(document.getElementById('carburant').value) || 0;
        const peage = parseFloat(document.getElementById('peage').value) || 0;
        const autres = parseFloat(document.getElementById('autresFrais').value) || 0;
        const total = frais + carburant + peage + autres;
        document.getElementById('budgetMission').value = total.toFixed(2);
    }

    ['fraisMission', 'carburant', 'peage', 'autresFrais'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculerTotal);
    });
</script>
</body>
</html>
