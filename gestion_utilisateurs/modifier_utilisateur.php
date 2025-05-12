<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tdrr', 'root', '');

// Récupération de l'utilisateur à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $utilisateur = $stmt->fetch();

    if (!$utilisateur) {
        die("Utilisateur introuvable.");
    }
} else {
    die("ID utilisateur non fourni.");
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $service = $_POST['service'];
    $role = $_POST['role'];
    $statut = $_POST['statut'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateurs SET nom=?, prenom=?, service=?, password=?, role=?, statut=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $service, $password, $role, $statut, $id]);
    } else {
        $sql = "UPDATE utilisateurs SET nom=?, prenom=?, service=?, role=?, statut=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $service, $role, $statut, $id]);
    }
    echo "<script>alert('Modifications enregistrées avec succès.'); window.location.href='gestion_utilisateurs.php';</script>";
    exit;
    
    header("Location: gestion_utilisateurs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h2>Modifier un utilisateur</h2>

    <div class="form-container">
        <form method="POST">
            <label for="nom">Nom :</label>
            <input type="text" class="form-control" name="nom" id="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" class="form-control" name="prenom" id="prenom" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>

            <label for="service">Service :</label>
            <input type="text" class="form-control" name="service" id="service" value="<?= htmlspecialchars($utilisateur['service']) ?>" required>

            <label for="password">Mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" class="form-control" name="password" id="password">

            <label for="role">Rôle :</label>
            <select class="form-control" name="role" id="role" required>
                <option value="admin" <?= $utilisateur['role'] == 'admin' ? 'selected' : '' ?>>admin</option>
                <option value="responsable" <?= $utilisateur['role'] == 'responsable' ? 'selected' : '' ?>>responsable</option>
                <option value="secretariat" <?= $utilisateur['role'] == 'secretariat' ? 'selected' : '' ?>>sécrétariat</option>
                <option value="membre" <?= $utilisateur['role'] == 'membre' ? 'selected' : '' ?>>membre</option>
                <option value="directeur" <?= $utilisateur['role'] == 'directeur' ? 'selected' : '' ?>>Directeur</option>
            </select>

            <label for="statut">Statut :</label>
            <select class="form-control" name="statut" id="statut" required>
                <option value="actif" <?= $utilisateur['statut'] == 'actif' ? 'selected' : '' ?>>actif</option>
                <option value="inactif" <?= $utilisateur['statut'] == 'inactif' ? 'selected' : '' ?>>inactif</option>
            </select>

            <button type="submit" class="btn-vert">Enregistrer les modifications</button>
            <div class="btn-retour-container">
                <a href="gestion_utilisateurs.php" class="btn-blanc">Retour</a>
            </div>
        </form>
    </div>

</body>
</html>
