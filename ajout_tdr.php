<?php
// Connexion à la base de données
require 'connexion.php';
$pdo = new PDO("mysql:host=localhost;dbname=tdrr;charset=utf8", "root", "");
// Traitement du formulaire
$successMessage = "";
$errorMessage = "";

// Info véhicule
$vehicules = $conn->query("SELECT idvehicule,marque, matricule, conducteur_nom, conducteur_titre	 FROM vehicules")->fetchAll();
 // Récupération des membres
$personnel = $conn->query("SELECT * FROM personnel")->fetchAll();



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération des données du formulaire
    $titre = $_POST["titreMission"] ?? '';
    $objectif = $_POST["objectifMission"] ?? '';
    $activite = $_POST["activite"] ?? '';
    $duree = $_POST["dureeMission"] ?? '';
    $chef = $_POST["chefMission"] ?? '';
    $membre = $_POST["membreMission"] ?? '';
    $membres = $_POST["membres"] ?? [];
    $membre = implode(', ', $membres);  // transforme le tableau en chaîne séparée par virgules
    
    $itineraire = $_POST["itineraire"] ?? '';
    
    $frais_mission = $_POST["fraisMission"] ?? 0;
    $carburation = $_POST["carburant"] ?? 0;
    $peage = $_POST["peage"] ?? 0;
    $autres = $_POST["autresFrais"] ?? 0;
    $budget = $frais_mission + $carburation + $peage + $autres;
    $idvehicule = $_POST["idvehicule"] ?? null;

    // Etat initial de la mission
    $etat = "soumis";  // Par défaut, l'état est "soumis"

    // Récupérer le nom et prénom du chef avec une requête SQL avant d’insérer le TDR.

    // Récupère nom et prénom du chef
    $stmtChef = $pdo->prepare("SELECT nom, prenom FROM personnel WHERE idpersonnel = ?");
    $stmtChef->execute([$chef]);
    $chefData = $stmtChef->fetch(PDO::FETCH_ASSOC);

    // $chef_nom_complet = $chefData ? $chefData['prenom'] . ' ' . $chefData['nom'] : '';


    if (!empty($titre) && !empty($objectif) && !empty($activite) && !empty($duree) && !empty($chef) && !empty($membre) && !empty($itineraire)) {
        try {
            // Insertion dans la base de données
            $sql = "INSERT INTO ttdr (titreMission, objectifMission, activite, dureeMission, chefMission, itineraire, fraisMission, carburant, peage, autresFrais, budgetMission, etat_actuel,idvehicule) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $titre,
                $objectif,
                $activite,
                $duree,
                $chef,
                $itineraire,
                $frais_mission,
                $carburation,
                $peage,
                $autres,
                $budget,
                $etat,
                $idvehicule
            ]);

            $idtdr = $pdo->lastInsertId(); // CORRECTEMENT PLACÉ

            foreach ($membres as $idpersonnel) {
                $stmtMembre = $pdo->prepare("INSERT INTO membres_tdr (idtdr, idpersonnel) VALUES (?, ?)");
                $stmtMembre->execute([$idtdr, $idpersonnel]);
            }


            $successMessage = "✅ TDR ajouté avec succès.";
            // Redirection après insertion
            header("Location: validation_tdr/liste_tdr_membre.php");
            exit;


            $successMessage = "✅ TDR ajouté avec succès.";
            header("Location: validation_tdr/liste_tdr_membre.php");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMessage = "❌ Erreur : données déjà existantes (doublon).";
            } else {
                $errorMessage = "❌ Erreur SQL : " . $e->getMessage();
            }
        }
    } else {
        $errorMessage = "❌ Veuillez remplir tous les champs obligatoires.";
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

        <!-- Inclure Select2 et jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>

<style>
        .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 16px;
    }


    .btn-danger {
        display: block;
        width: 200px;
        padding: 10px;
        background-color: #d9534f;
        color: white;
        border: none;
        font-size: 18px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
    }
    .btn-danger:hover {
        background-color: #662422 !important;
    }


    .btn-success{
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
    .btn-success:hover {
        background-color:#218869 !important;
    }

    .table{
        width: 100% !important;
    }

    .text-justify {
        text-align: justify !important;
        padding-left: 40px;
        padding-right: 40px;
    }

    select {
        background-color: #ffffff;      /* fond blanc */
        color: #006400;                 /* texte vert foncé */
        border: 2px solid #006400;      /* bordure verte */
        border-radius: 8px;
        padding: 8px;
        font-size: 16px;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    select:focus {
        border-color: #32cd32;          /* vert clair quand sélectionné */
        box-shadow: 0 0 5px rgba(50, 205, 50, 0.5);
    }


</style>



<div class="container mt-5">
    <h2 class="mb-4">Ajouter un TDR</h2>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="titreMission" class="form-label">Titre de la mission</label>
            <input type="text" class="form-control" id="titreMission" name="titreMission" required>
        </div>

        <div class="mb-3">
            <label for="objectifMission" class="form-label">Objectif de la mission</label>
            <textarea class="form-control" id="objectifMission" name="objectifMission" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="activite" class="form-label">Activité</label>
            <textarea class="form-control" id="activite" name="activite" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="dureeMission" class="form-label">Durée de la mission en jours</label>
            <input type="number" class="form-control" id="dureeMission" name="dureeMission"  min="1" step="1" required>
        </div>

        <div class="mb-3">
            <label for="itineraire" class="form-label">Itinéraire</label>
            <input type="text" class="form-control" id="itineraire" name="itineraire" required>
        </div>

        <div class="mb-3">
            <label for="chefMission" class="form-label">Chef de mission</label>
            <div >
                    <div class="membre mb-2">
                        <select class="form-control select-membre" name="chefMission" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($personnel as $p): ?>
                                <option value="<?= $p['idpersonnel'] ?>">
                                    <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
        </div><br>


        <div class="mb-3">
            <label for="membreMission" class="form-label">Membres de la mission</label>
            <!-- <textarea class="form-control" id="membreMission" name="membreMission" rows="3" required></textarea> -->
       

                <div id="membres-container">
                    <div class="membre mb-2">
                        <select class="form-control select-membre" name="membres[]" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($personnel as $p): ?>
                                <option value="<?= $p['idpersonnel'] ?>">
                                    <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            <button type="button" class="btn btn-secondary" onclick="ajouterMembre()">+ Ajouter un membre</button>
        </div><br>



        <!-- Selectionner le conducteur -->
        <label for="conducteur">Choisir un conducteur :</label>

            <select id="idvehicule" name="idvehicule" class="form-label" onchange="afficherVehicule()" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($vehicules as $v): ?>
                    <option 
                        value="<?= $v['idvehicule'] ?>" 
                        data-marque="<?= htmlspecialchars($v['marque']) ?>" 
                        data-matricule="<?= htmlspecialchars($v['matricule']) ?>"
                    >
                        <?= htmlspecialchars($v['conducteur_nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <p><strong>Véhicule :</strong> <span id="vehicule_info">Aucun</span></p>
        

        <div class="mb-4">
            <label class="form-label"><strong>Budget de la mission</strong></label>

            <div class="table-responsive w-100">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Désignation</th>
                            <th>Montant (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class= "text-justify">Frais de mission</td>
                            <td><input type="number" class="form-control" name="fraisMission" id="fraisMission" step="1" required></td>
                        </tr>
                        <tr>
                            <td class= "text-justify">Carburant</td>
                            <td><input type="number" class="form-control" name="carburant" id="carburant" step="1" required></td>
                        </tr>
                        <tr>
                            <td class= "text-justify">Péage</td>
                            <td><input type="number" class="form-control" name="peage" id="peage" step="1" required></td>
                        </tr>
                        <tr>
                            <td class= "text-justify">Autres frais</td>
                            <td><input type="number" class="form-control" name="autresFrais" id="autresFrais" step="1" required></td>
                        </tr>
                        <tr class="table-success">
                            <th class="text-justify">Total estimé</th>
                            <th><input type="number" class="form-control" name="budgetMission" id="budgetMission" readonly></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> <br><br> 

        <div class="bouton-container">
            <button type="submit" class="btn-success">Ajouter le TDR</button>
            <a href="liste_tdr.php" class="btn-danger">Annuler</a>
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
<script>
    function afficherVehicule() {
        const select = document.getElementById('conducteur');
        const marque = select.options[select.selectedIndex].getAttribute('data-marque');
        const matricule = select.options[select.selectedIndex].getAttribute('data-matricule');
        
        if (marque && matricule) {
            document.getElementById('vehicule_info').textContent = `${marque} - ${matricule}`;
        } else {
            document.getElementById('vehicule_info').textContent = 'Aucun';
        }
    }
</script>

<script>
        function ajouterMembre() {
            const container = document.getElementById('membres-container');

            // Créer un nouvel élément select
            const nouveauSelect = document.createElement('div');
            nouveauSelect.className = 'membre mb-2';

            nouveauSelect.innerHTML = `
                <select class="form-control select-membre" name="membres[]" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($personnel as $p): ?>
                        <option value="<?= $p['idpersonnel'] ?>">
                            <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            `;

            container.appendChild(nouveauSelect);

            // Reinitialise Select2 sur les nouveaux éléments
            $('.select-membre').select2({
                placeholder: "Tapez un prénom ou un nom",
                allowClear: true
            });
        }
</script>


<!-- script de recherche -->

<script>
    $(document).ready(function() {
        $('.select-membre').select2({
            placeholder: "Tapez un prénom ou un nom",
            allowClear: true
        });
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('#membres-select').select2({
      placeholder: "Sélectionnez les membres",
      width: '100%'
    });
  });
</script>

</body>
</html>
