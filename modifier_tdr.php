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
    die("Erreur de connexion : " . $e->getMessage());
}

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idtdr = $_POST['idtdr'];
    $titre = $_POST['titreMission'];
    $objectif = $_POST['objectMission'];
    $reference = $_POST['refPlanTravail'];
    $activite = $_POST['activite'];
    $resultat = $_POST['resultatAttendu'];
    $duree = $_POST['dureeMission'];
    $chef = $_POST['chefMission'];
    $membres = $_POST['membreMission'];
    $itineraire = $_POST['itineraire'];
    $budget = $_POST['budgetMission'];

    $sql = "UPDATE ttdr SET 
                titreMission = :titre, 
                objectMission = :objectif,
                refPlanTravail = :reference,
                activite = :activite,
                resultatAttendu = :resultat,
                dureeMission = :duree, 
                chefMission = :chef, 
                membreMission = :membres, 
                itineraire = :itineraire, 
                budgetMission = :budget 
            WHERE idtdr = :idtdr";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titre' => $titre,
        ':objectif' => $objectif,
        ':reference' => $reference,
        ':activite' => $activite,
        ':resultat' => $resultat,
        ':duree' => $duree,
        ':chef' => $chef,
        ':membres' => $membres,
        ':itineraire' => $itineraire,
        ':budget' => $budget,
        ':idtdr' => $idtdr
    ]);

    header("Location: liste_tdr.php?msg=modification_reussie");
    exit();
}

// Si l'ID est fourni, on récupère les données du TDR
if (isset($_GET['id'])) {
    $idtdr = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM ttdr WHERE idtdr = :idtdr");
    $stmt->execute([':idtdr' => $idtdr]);
    $tdr = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tdr) {
        echo "TDR introuvable.";
        exit();
    }
} else {
    echo "ID manquant.";
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
            <label class="form-label">Titre de la mission</label>
            <input type="text" name="titreMission" class="form-control" value="<?php echo htmlspecialchars($tdr['titreMission']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Objectif de la mission</label>
            <textarea name="objectMission" class="form-control" rows="3" required><?php echo htmlspecialchars($tdr['objectMission']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Référence dans le plan de travail</label>
            <input type="text" name="refPlanTravail" class="form-control" value="<?php echo htmlspecialchars($tdr['refPlanTravail']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Activités</label>
            <textarea name="activite" class="form-control" rows="3" required><?php echo htmlspecialchars($tdr['activite']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Résultats attendus</label>
            <textarea name="resultatAttendu" class="form-control" rows="3" required><?php echo htmlspecialchars($tdr['resultatAttendu']); ?></textarea>
        </div>

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
