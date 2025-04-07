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
    $budget = $_POST["budgetMission"] ?? 0;

    // Vérifier que l'ID n'est pas vide
    if (!empty($id)) {
        $sql = "INSERT INTO ttdr (idtdr, dureeMission, chefMission, membreMission, itineraire, budgetMission) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$id, $duree, $chef, $membre, $itineraire, $budget])) {
            $successMessage = "Le TDR a été ajouté avec succès.";
            header("Location: liste_tdr.php");
        } else {
            $errorMessage = "Erreur lors de l'ajout du TDR (ID peut-être déjà existant ?).";
        }
    } else {
        $errorMessage = "Veuillez saisir un identifiant TDR.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un TDR</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
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

        <div class="mb-3">
            <label for="budgetMission" class="form-label">Budget de la mission</label>
            <input type="number" class="form-control" id="budgetMission" name="budgetMission" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-success">Ajouter le TDR</button>
    </form>
</div>
</body>
</html>
