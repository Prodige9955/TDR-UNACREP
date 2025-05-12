<?php
require 'connexion.php';

$successMessage = "";
$errorMessage = "";
$tdrData = null;
$tdrMembres = [];
$idTdr = $_GET['id'] ?? null;

if (!$idTdr) {
    header("Location: erreur.php?type=vide");
    exit();
}

try {
    // Récupérer les informations du TDR
    $stmt = $conn->prepare("SELECT * FROM ttdr WHERE idtdr = :idtdr");
    $stmt->execute([':idtdr' => $idTdr]);
    $tdrs = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tdrs) {
        header("Location: erreur.php?type=inexistant");
        exit();
    }

    // Membres déjà associés
    $stmtMembres = $conn->prepare("SELECT idpersonnel FROM membres_tdr WHERE idtdr = :idtdr");
    $stmtMembres->execute([':idtdr' => $idTdr]);
    $tdrMembres = $stmtMembres->fetchAll(PDO::FETCH_COLUMN);

    // Liste des véhicules et personnels
    $vehicules = $conn->query("SELECT idvehicule, marque, matricule, conducteur_nom, conducteur_titre FROM vehicules")->fetchAll();
    $personnel = $conn->query("SELECT * FROM personnel")->fetchAll();

    // Traitement de la mise à jour si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $titre = $_POST['titreMission'];
        $objectif = $_POST['objectifMission'];
        $activite = $_POST['activite'];
        $duree = $_POST['dureeMission'];
        $chef = $_POST['chefMission'];
        $membres = $_POST['membres'] ?? [];
        $itineraire = $_POST['itineraire'];
        $idvehicule = $_POST['idvehicule'];
        $frais = $_POST['fraisMission'];
        $carburant = $_POST['carburant'];
        $peage = $_POST['peage'];
        $autres = $_POST['autresFrais'];
        $budget = $_POST['budgetMission'];
        $membres_string = implode(',', $membres);

        // Mise à jour du TDR
        $sql = "UPDATE ttdr SET 
                    titreMission = :titre,
                    objectifMission = :objectif,
                    activite = :activite,
                    dureeMission = :duree,
                    chefMission = :chef,
                    -- membreMission = :membres,
                    itineraire = :itineraire,
                    idvehicule = :idvehicule,
                    fraisMission = :frais,
                    carburant = :carburant,
                    peage = :peage,
                    autresFrais = :autres,
                    budgetMission = :budget
                WHERE idtdr = :idtdr";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':titre' => $titre,
            ':objectif' => $objectif,
            ':activite' => $activite,
            ':duree' => $duree,
            ':chef' => $chef,
            // ':membres' => $membres_string,
            ':itineraire' => $itineraire,
            ':idvehicule' => $idvehicule,
            ':frais' => $frais,
            ':carburant' => $carburant,
            ':peage' => $peage,
            ':autres' => $autres,
            ':budget' => $budget,
            ':idtdr' => $idTdr
        ]);

        // Mettre à jour les membres liés
        $delete_stmt = $conn->prepare("DELETE FROM membres_tdr WHERE idtdr = :idtdr");
        $delete_stmt->execute([':idtdr' => $idTdr]);

        if (!empty($membres)) {
            $insert_stmt = $conn->prepare("INSERT INTO membres_tdr (idtdr, idpersonnel) VALUES (:idtdr, :idpersonnel)");
            foreach ($membres as $idpersonnel) {
                $insert_stmt->execute([
                    ':idtdr' => $idTdr,
                    ':idpersonnel' => $idpersonnel
                ]);
            }
        }

        header("Location: liste_tdr.php?msg=modification_reussie");
        exit();
    }
} catch (PDOException $e) {
    $errorMessage = "Erreur lors du chargement ou de la mise à jour du TDR: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le TDR</title>
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
    <h2 class="mb-4">Modifier le TDR</h2>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="">

        <input type="hidden" name="id" value="<?= $tdrs['idtdr'] ?>">

        <div class="mb-3">
            <label for="titreMission" class="form-label">Titre de la mission</label>
            <input type="text" class="form-control" id="titreMission" name="titreMission" required  value="<?= htmlspecialchars($tdrs['titreMission']) ?>">
        </div>

        <div class="mb-3">
            <label for="objectifMission" class="form-label">Objectif de la mission</label>
            <textarea class="form-control" id="objectifMission" name="objectifMission" rows="3" required><?= htmlspecialchars($tdrs['objectifMission']) ?></textarea>

        </div>

        <div class="mb-3">
            <label for="activite" class="form-label">Activité</label>
            <textarea class="form-control" id="activite" name="activite" rows="3" required> <?= htmlspecialchars($tdrs['activite']) ?> </textarea>
        </div>

        <div class="mb-3">
            <label for="dureeMission" class="form-label">Durée de la mission en jours</label>
            <input type="number" class="form-control" id="dureeMission" name="dureeMission"  min="1" step="1" required value="<?= htmlspecialchars($tdrs['dureeMission']) ?>">
        </div>

        <div class="mb-3">
            <label for="itineraire" class="form-label">Itinéraire</label>
            <input type="text" class="form-control" id="itineraire" name="itineraire" required value="<?= htmlspecialchars($tdrs['itineraire']) ?>">
        </div>

        <div class="mb-3">
        <label for="chefMission" class="form-label">Chef de mission</label>
            <div >
                    <div class="membre mb-2">
                        <select class="form-control select-membre" name="chefMission" id="chefMission" required>
                        <?php foreach ($personnel as $p): ?>
                            <option value="<?= $p['idpersonnel'] ?>" <?= ($p['idpersonnel'] == $tdrs['chefMission']) ? 'selected' : '' ?>>
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

                        <select name="membres[]" multiple class="form-control" id="membres-select">
                            <?php foreach ($personnel as $pers): ?>
                                <option value="<?= $pers['idpersonnel'] ?>" 
                                    <?= in_array($pers['idpersonnel'], $tdrMembres) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pers['prenom'] . ' ' . $pers['nom']?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

            <!-- <button type="button" class="btn btn-secondary" onclick="ajouterMembre()">+ Ajouter un membre</button> -->
        </div><br>



        <!-- Selectionner le conducteur -->
        <label for="conducteur">Choisir un conducteur :</label>
            
            <select id="conducteur" name="idvehicule" class="form-label" onchange="afficherVehicule()" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($vehicules as $vehicule): ?>
                    <option 
                        value="<?= $vehicule['idvehicule'] ?>"
                        data-marque="<?= htmlspecialchars($vehicule['marque']??'') ?>"
                        data-matricule="<?= htmlspecialchars($vehicule['matricule']??'') ?>"
                        <?= ($vehicule['idvehicule'] == $tdrs['idvehicule']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($vehicule['conducteur_nom']) ?>
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
                            <td><input type="number" class="form-control" name="fraisMission" id="fraisMission" value="<?php echo htmlspecialchars($tdrs['fraisMission']); ?>" step="1" required></td>
                        </tr>
                        <tr>
                            <td class= "text-justify">Carburant</td>
                            <td><input type="number" class="form-control" name="carburant" id="carburant" value="<?php echo htmlspecialchars($tdrs['carburant']); ?>" step="1" required></td>
                        </tr>
                        <tr>
                            <td class= "text-justify">Péage</td>
                            <td><input type="number" class="form-control" name="peage" id="peage" value="<?php echo htmlspecialchars($tdrs['peage']); ?>" step="1" required></td>
                        </tr>
                        <tr>
                            <td class= "text-justify">Autres frais</td>
                            <td><input type="number" class="form-control" name="autresFrais" id="autresFrais" value="<?php echo htmlspecialchars($tdrs['autresFrais']); ?>" step="1" required></td>
                        </tr>
                        <tr class="table-success">
                            <th class="text-justify">Total estimé</th>
                            <th><input type="number" class="form-control" name="budgetMission" id="budgetMission" value="<?php echo htmlspecialchars($tdrs['budgetMission']); ?>" readonly></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> <br><br> 

        <?php
            // Vérifie si la page précédente existe pour le bouton ANNULER
            $previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'liste.php';
        ?>

        <div class="bouton-container">
            <button type="submit" class="btn-success"> Enregistrer </button>
            <a href="<?= htmlspecialchars($previousPage); ?>" class="btn-danger">  Annuler  </a>
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
        window.addEventListener('DOMContentLoaded', function () {
        afficherVehicule(); // Met à jour les infos véhicule dès le chargement de la page
    });
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
