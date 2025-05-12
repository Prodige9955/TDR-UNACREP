<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== 'admin') {
    header("Location: authentification.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin - Tableau de Bord</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
</head>


<style>
      /* Annuler les soumignement de tous les boutons lien */
    a{
        text-decoration: none !important;
    }
    a:hover {
        text-decoration: underline !important;
    }
    /* Justifier les nav-link(les <li>) */
    .nav-link {
        cursor: pointer;
        display: block;
        text-align: center;
    }

    /* annuler les points devant chaque <li> dans la liste <ul> */
    ul {
        list-style-type: none;
        padding-left : 0;
    }

</style>



<body>
    <div class="container my-5">
        <div class="dashboard-container">
            <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION["user"]["nom"]); ?> !</h1>
            <p>Vous êtes connecté.</p>

            <!-- Onglets sous le message de bienvenue -->
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="addTDRTab" data-bs-toggle="tab" href="../ajout_tdr.php" role="tab" aria-controls="addTDR" aria-selected="true" class="no-underline">Ajouter un TDR</a>
                </li><br> <br>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="listTDRTab" data-bs-toggle="tab" href="../liste_tdr.php" role="tab" aria-controls="listTDR" aria-selected="false">Liste des TDR</a>
                </li><br> <br>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="userManagementTab" href="../gestion_utilisateurs/gestion_utilisateurs.php" role="tab" aria-controls="userManagement" aria-selected="false">Gestion des Utilisateurs</a>
                </li><br> <br>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="reportsTab" href="rapports.php" role="tab" aria-controls="reports" aria-selected="false">Rapports</a>
                </li><br> <br>
            </ul>

            <!-- Formulaire de déconnexion -->
            <form action="../deconnexion.php" method="POST">
                <button type="submit" class="logout-button">Se déconnecter</button>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
        // Envoie une requête toutes les 10 minutes pour garder la session active pour garder la section active
        setInterval(function() {
            fetch('../keep_alive.php'); // le ".." remonte d’un dossier
        }, 600000); // 10 minutes
    </script>
</body>

</html>
