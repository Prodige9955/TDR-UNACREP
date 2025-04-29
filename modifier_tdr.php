<?php
$host = "localhost";
$dbname = "tdr";
$username = "root";
$password = "";

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Redirection vers une page d'erreur en cas de problème de connexion
    header("Location: erreur.php?type=connexion");
    exit();
}

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idtdr = $_POST['idtdr'];
    $duree = $_POST['dureeMission'];
    $chef = $_POST['chefMission'];
    $membres = $_POST['membreMission'];
    $itineraire = $_POST['itineraire'];
    $budget = $_POST['budgetMission'];

    // Requête de mise à jour du TDR
    try {
        $sql = "UPDATE ttdr SET 
                    dureeMission = :duree, 
                    chefMission = :chef, 
                    membreMission = :membres, 
                    itineraire = :itineraire, 
                    budgetMission = :budget 
                WHERE idtdr = :idtdr";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':duree' => $duree,
            ':chef' => $chef,
            ':membres' => $membres,
            ':itineraire' => $itineraire,
            ':budget' => $budget,
            ':idtdr' => $idtdr
        ]);

        // Si mise à jour réussie, redirection avec message de succès
        header("Location: liste_tdr.php?msg=modification_reussie");
        exit();
    } catch (PDOException $e) {
        // Si erreur de requête SQL
        header("Location: erreur.php?type=update");
        exit();
    }
}

// Si l'ID est fourni, on récupère les données du TDR
if (isset($_GET['id'])) {
    $idtdr = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM ttdr WHERE idtdr = :idtdr");
        $stmt->execute([':idtdr' => $idtdr]);
        $tdr = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tdr) {
            // Si le TDR n'existe pas
            header("Location: erreur.php?type=inexistant");
            exit();
        }
    } catch (PDOException $e) {
        // Si erreur de requête SQL
        header("Location: erreur.php?type=sql");
        exit();
    }
} else {
    // Si ID manquant dans l'URL
    header("Location: erreur.php?type=vide");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un TDR</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 p-4 shadow rounded bg-white">
    <h2 class="text-center mb-4 text-primary">Modifier un TDR</h2>

    <form method="POST">
        <input type="hidden" name="idtdr" value="<?php echo $tdr['idtdr']; ?>">

        <div class="mb-3">
            <label class="form-label">Durée de la mission</label>
            <input type="text" name="dureeMission" class="form-control" value="<?php echo htmlspecialchars($tdr['dureeMission']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Chef de mission</label>
            <input type="text" name="chefMission" class="form-control" value="<?php echo htmlspecialchars($tdr['chefMission']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Membres de la mission</label>
            <textarea name="membreMission" class="form-control" rows="3" required><?php echo htmlspecialchars($tdr['membreMission']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Itinéraire</label>
            <input type="text" name="itineraire" class="form-control" value="<?php echo htmlspecialchars($tdr['itineraire']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Budget de la mission</label>
            <input type="number" name="budgetMission" class="form-control" value="<?php echo htmlspecialchars($tdr['budgetMission']); ?>" required>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success px-4"> Enregistrer </button>
            <a href="liste_tdr.php" class="btn btn-danger px-4">  Annuler  </a>
        </div>
    </form>
</div>

</body>
</html>

