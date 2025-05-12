<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<style>
        .btn-filtrage {
        display: block;
        width: 200px;
        /* margin: 00px auto; */
        padding: 10px;
        background-color: #2ecc71;
        color: white;
        border: none;
        font-size: 18px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
    }
            /* Annuler les soumignement de tous les boutons lien */
        a.btn{
        text-decoration: none !important;
    }

</style>


<h2>Liste des utilisateurs</h2>


<div class="table-responsive">
    <table class="tdr-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Service</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Date création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../connexion.php';
            $sql = "SELECT * FROM utilisateurs";
            $result = $conn->query($sql);
           
            while ( $row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nom']}</td>
                        <td>{$row['prenom']}</td>
                        <td>{$row['service']}</td>
                        <td>{$row['role']}</td>
                        <td>{$row['statut']}</td>
                        <td>{$row['date_creation']}</td>
                        <td>
                            <a href='modifier_utilisateur.php?id={$row['id']}' class='btn btn-warning btn-sm'>Modifier</a>
                            <a href='supprimer_utilisateur.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Supprimer cet utilisateur ?');\">Supprimer</a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div> <br><br><br>

<div class="bouton-container">
    <a href="ajouter_utilisateur.php" class="btn-filtrage">Ajouter un utilisateur</a> 
    <!-- <a href="dashboard.php" class="btn btn-primary">Retour au tableau de bord</a> -->
    <a href="filtrage_utilisateurs.php" class="btn-filtrage">Filtrer les utilisateurs</a>
</div>

</body>
</html>
