<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tdrr', 'root', '');

// Récupération des filtres
$filters = [];
$params = [];

if (!empty($_GET['filtre_role'])) {
    $filters[] = "role = ?";
    $params[] = $_GET['filtre_role'];
}

if (!empty($_GET['filtre_statut'])) {
    $filters[] = "statut = ?";
    $params[] = $_GET['filtre_statut'];
}


// Construction de la requête SQL
$sql = "SELECT * FROM utilisateurs";

if (!empty($filters)) {
    $sql .= " WHERE " . implode(" AND ", $filters);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$utilisateurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .container {
            margin-top: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2e7d32;
        }

        .form-control, .form-select {
            margin-bottom: 15px;
        }

        .btn {
            margin-right: 10px;
        }

        table {
            margin-top: 30px;
        }

        .table th {
            background-color: #2e7d32;
            color: white;
        }

        .table td {
            vertical-align: middle;
        }

        .filter-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .btn-container {
            display: flex;
            justify-content: flex-start;
        }

        .container {
            margin-top: 40px;
            max-width: 1000px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: start;
            min-height: 100vh;
            padding-top: 30px;
        }
    </style>


</head>
<body>

<div class="container">
    <h2>Gestion des utilisateurs</h2>

    <div class="filter-section">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="filtre_role" class="form-label">Filtrer par rôle :</label>
                <select name="filtre_role" id="filtre_role" class="form-select">
                    <option value="">Tous les rôles</option>
                    <option value="admin">Admin</option>
                    <option value="membre">Membre</option>
                    <option value="responsable">Responsable</option>
                    <option value="secretariat">Sécrétariat</option>
                    <option value="secretariat">Directeur</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="filtre_statut" class="form-label">Filtrer par statut :</label>
                <select name="filtre_statut" id="filtre_statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>
            </div>

            <div class="col-md-4 btn-container align-self-end">
                <button type="submit" class="btn btn-success">Filtrer</button>
                <a href="gestion_utilisateurs.php" class="btn btn-secondary">Voir tous les Utilisateurs</a>
            </div>
        </form>
    </div>

    <!-- Tableau des utilisateurs -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Service</th>
                <th>Rôle</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($utilisateurs) > 0): ?>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['service']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Aucun utilisateur trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

