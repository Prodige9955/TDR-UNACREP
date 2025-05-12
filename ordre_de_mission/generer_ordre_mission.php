<?php
require '../vendor/autoload.php'; // Assurez-vous que PHPWord est installé via Composer
require '../connexion.php';

use PhpOffice\PhpWord\TemplateProcessor;

if (!isset($_GET['idtdr'])) {
    die("ID de TDR non spécifié.");
}

$idtdr = $_GET['idtdr'];

// Récupération des infos principales du TDR
$sql = "SELECT 
            t.idtdr,
            t.titreMission,
            t.objectifMission,
            t.activite,
            t.dureeMission,
            t.chefMission,
            t.itineraire,
            t.fraisMission,
            t.carburant,
            t.peage,
            t.autresFrais,
            t.budgetMission,
            t.etat_actuel,
            v.matricule,
            v.marque,
            v.conducteur_titre,
            v.conducteur_nom,
            p.nom AS membre_nom,
            p.prenom AS membre_prenom,
            p.titre AS membre_titre
        FROM
            ttdr t
        LEFT JOIN vehicules v ON t.idvehicule = v.idvehicule
        LEFT JOIN membres_tdr mt ON mt.idtdr = t.idtdr
        LEFT JOIN personnel p ON mt.idpersonnel = p.idpersonnel
        WHERE
            t.idtdr = ?
        ORDER BY
            p.nom"; 



$stmt = $conn->prepare($sql);
$stmt->execute([$idtdr]);
$tdr = $stmt->fetch();

$membres=[];

if (!$tdr) {
    die("TDR non trouvé.");
}

// Récupération des membres de mission
$sqlMembres = "
SELECT p.nom, p.prenom, p.titre
FROM membres_tdr mt
JOIN personnel p ON mt.idpersonnel = p.idpersonnel
WHERE mt.idtdr = ?
ORDER BY mt.idmembre ASC
";

// ***********Recuperation des informations  du chef mission***********

// Récupérer le nom complet du chef depuis ttdr
$stmtTdr = $conn->prepare("SELECT chefMission FROM ttdr WHERE idtdr = ?");
$stmtTdr->execute([$idtdr]);
$rowTdr = $stmtTdr->fetch(PDO::FETCH_ASSOC);

$chef_nom_complet = $rowTdr['chefMission'];

// Rechercher dans personnel par nom complet
$stmtChef = $conn->prepare("SELECT nom, prenom, titre FROM personnel WHERE CONCAT(prenom, ' ', nom) = ?");
$stmtChef->execute([$chef_nom_complet]);
$chefData = $stmtChef->fetch(PDO::FETCH_ASSOC);

if ($chefData) {
    $chef_prenom = $chefData['prenom'];
    $chef_nom = $chefData['nom'];
    $chef_titre = $chefData['titre'];
}



$stmtMembres = $conn->prepare($sqlMembres);
$stmtMembres->execute([$idtdr]);
$membres = $stmtMembres->fetchAll();

// Charger le modèle Word
$templateProcessor = new TemplateProcessor('../modele/ordre_mission_template.docx');

// Durée de la mission (ex. : 5 jours)
$duree = intval($tdr['dureeMission']); // on sécurise avec intval

// Calcul de la date de départ (aujourd'hui + 1 jour)
$dateDepart = (new DateTime())->modify('+1 day');

// Calcul de la date de retour (date départ + durée mission)
$dateRetour = clone $dateDepart;
$dateRetour->modify("+{$duree} days");

// Formatage des dates
$date_depart_formatee = $dateDepart->format('d/m/Y');
$date_retour_formatee = $dateRetour->format('d/m/Y');

// Insertion dans le modèle
$templateProcessor->setValue('date_depart', $date_depart_formatee);
$templateProcessor->setValue('date_retour', $date_retour_formatee);
$templateProcessor->setValue('date_actuelle', date('d/m/Y'));


// Remplir les champs simples
$templateProcessor->setValue('idtdr', $tdr['idtdr']);
$templateProcessor->setValue('titre', htmlspecialchars($tdr['titreMission']??''));
$templateProcessor->setValue('objectif', htmlspecialchars($tdr['objectifMission']??''));
$templateProcessor->setValue('objet', htmlspecialchars($tdr['objectifMission']??''));
$templateProcessor->setValue('activite', htmlspecialchars($tdr['activite']??''));
$templateProcessor->setValue('duree', $tdr['dureeMission']??'');
$templateProcessor->setValue('itineraire', htmlspecialchars($tdr['itineraire']??''));
$templateProcessor->setValue('frais', $tdr['fraisMission']??'');
$templateProcessor->setValue('carburant', $tdr['carburant']??'');
$templateProcessor->setValue('peage', $tdr['peage']??'');
$templateProcessor->setValue('autres', $tdr['autresFrais']??'');
$templateProcessor->setValue('budget', $tdr['budgetMission']??'');

$templateProcessor->setValue('chef_nom', htmlspecialchars($chefData['nom']??''));
$templateProcessor->setValue('chef_prenom', htmlspecialchars($chefData['prenom']??''));
$templateProcessor->setValue('chef_titre', htmlspecialchars($chefData['titre']??''));

$templateProcessor->setValue('vehicule_marque', htmlspecialchars($tdr['vehicule_marque']??''));
$templateProcessor->setValue('vehicule_matricule', htmlspecialchars($tdr['vehicule_matricule']??''));
$templateProcessor->setValue('conducteur_nom', htmlspecialchars($tdr['conducteur_nom']??''));
$templateProcessor->setValue('conducteur_titre', htmlspecialchars($tdr['conducteur_titre']??''));

// $templateProcessor->setValue('date_depart', date('d/m/Y', strtotime($tdr['date_depart']??'')));
// $templateProcessor->setValue('date_retour', date('d/m/Y', strtotime($tdr['date_retour']??'' )));
$templateProcessor->setValue('date_actuelle', date('d/m/Y'));


// Gestion dynamique des membres dans un tableau
if (!empty($membres)) {
    $templateProcessor->cloneRow('membres.nom', count($membres));
    foreach ($membres as $index => $membre) {
    $row = $index + 1;
    $templateProcessor->setValue("membres.nom#{$row}", htmlspecialchars($membre['nom']));
    $templateProcessor->setValue("membres.prenom#{$row}", htmlspecialchars($membre['prenom']));
    $templateProcessor->setValue("membres.titre#{$row}", htmlspecialchars($membre['titre']));
    }
    }

// Génération du fichier Word temporaire
$cheminTemporaire = "ordre_temporaire.docx";
$templateProcessor->saveAs($cheminTemporaire);

// Téléchargement
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=ordre_de_mission_tdr_{$idtdr}.docx");
header("Cache-Control: max-age=0");
readfile($cheminTemporaire);

// Nettoyage
unlink($cheminTemporaire);
exit;
