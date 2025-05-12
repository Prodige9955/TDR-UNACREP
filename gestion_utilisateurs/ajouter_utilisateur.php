<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tdrr', 'root', '');

// Traitement du formulaire à la soumission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $service = $_POST['service'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $statut = $_POST['statut'];

    $sql = "INSERT INTO utilisateurs (nom, prenom, service, password, role, statut)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $service, $password, $role, $statut]);

    header("Location: gestion_utilisateurs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" href="../style.css"> 
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
</head>
<body>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Ajouter un utilisateur</h2>
        <form method="POST" class="form-container">

            <div class="mb-3">
                <label class="form-label">Nom :</label>
                <input type="text" name="nom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Prénom :</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Service :</label>
                <input type="text" name="service" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe :</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rôle :</label>
                <select name="role" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="membre">Membre</option>
                    <option value="responsable">Responsable</option>
                    <option value="secretariat">Sécrétariat</option>
                    <option value="secretariat">Directeur</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Statut :</label>
                <select name="statut" class="form-select" required>
                    <option value="actif">actif</option>
                    <option value="inactif">inactif</option>
                </select>
            </div>
                <style>
                                        /* Stylisation spécifique de la section "Statut" */
                        .mb-3 select {
                            border-radius: 5px; /* Bordure arrondie pour le champ */
                            border: 1px solid #ced4da; /* Bordure grise claire */
                            padding: 10px; /* Espacement interne */
                            width: 100%; /* Prendre toute la largeur disponible */
                            font-size: 16px; /* Taille de la police */
                            background-color: #f8f9fa; /* Fond léger */
                            transition: all 0.3s ease; /* Transition pour effet au survol */
                        }

                        .mb-3 select:focus {
                            border-color: #28a745; /* Bordure verte lorsque le champ est focus */
                            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); /* Ombre verte */
                            outline: none; /* Retirer le contour par défaut */
                        }

                        .mb-3 label {
                            font-weight: bold; /* Rendre le label plus visible */
                            margin-bottom: 5px; /* Espacement sous le label */
                            display: block; /* Pour que le label occupe toute la ligne */
                            color: #495057; /* Couleur de texte légèrement grise */
                        }

                        .mb-3 {
                            margin-bottom: 20px; /* Espacement supplémentaire entre les sections */
                        }
                        form input,
                        form select,
                        button[type="submit"] {
                            width: 100%;
                            max-width: 400px;
                            margin: 0 auto;
                            display: block;
                        }
                        /*  centrer tout le formulaire */
                        .form-container {
                            max-width: 400px;
                            margin: 0 auto; 
                        }

                 </style>

            <div class="btn-retour-container">
                <button type="submit" class="btn-vert">Ajouter l'utilisateur </button>
            </div>
            <div class="btn-retour-container">
                <a href="gestion_utilisateurs.php" class="btn-blanc">Retour</a>
            </div>
    </div>
</body>
   
</body>
</html>
