<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: index.php"); // Redirige vers la page de connexion si non connecté
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <div class="dashboard-container">
            <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION["user"]["nom"]); ?> !</h1>
            <p>Vous êtes connecté.</p>

            <!-- Onglets sous le message de bienvenue -->
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="addTDRTab" data-bs-toggle="tab" href="ajout_tdr.php" role="tab" aria-controls="addTDR" aria-selected="true">Ajouter un TDR</a>
                </li><br> <br>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="listTDRTab" data-bs-toggle="tab" href="liste_tdr.php" role="tab" aria-controls="listTDR" aria-selected="false">Liste des TDR</a>
                </li><br> <br>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="userManagementTab" href="gestion_utilisateurs.php" role="tab" aria-controls="userManagement" aria-selected="false">Gestion des Utilisateurs</a>
                </li><br> <br>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="reportsTab" href="rapports.php" role="tab" aria-controls="reports" aria-selected="false">Rapports</a>
                </li><br> <br>
            </ul>

            <!-- Formulaire de déconnexion -->
            <form action="deconnexion.php" method="POST">
                <button type="submit" class="logout-button">Se déconnecter</button>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
