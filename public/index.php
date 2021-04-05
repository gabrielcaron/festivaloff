﻿<?php
/**
 * @file Prototype projet Festival OFF - page d'accueil
 * @author Gabriel Caron <1861095@cegep-ste-foy.qc.ca>
 * @version 1.0
 */


// Connexion à la base de données
$connexionBD = new PDO(
    'mysql:host=localhost;dbname=19_pwem2_off', '19_off', 'tim_19_off');

$connexionBD->exec("SET CHARACTER SET utf8");


// Récupère les actualités dans la base de données

$objResultat = $connexionBD->query("
SELECT titre,
CONCAT(SUBSTRING(article, 1, 140-LOCATE(' ',REVERSE(SUBSTRING(article,1,140)))), ' ...')
AS actualite
FROM actualites
LIMIT 3");

// Affecte les lignes de résultats dans arrActualites
$arrActualites = array();
foreach ($objResultat as $ligne) {
    array_push($arrActualites, $ligne);
}

// Récupère les artistes dans la base de données

$objResultat = $connexionBD->query("
SELECT artistes.nom,
       artistes.id
FROM artistes");

// Affecte les lignes de résultats dans arrActualites
$arrArtistes = array();
foreach ($objResultat as $ligne) {
    array_push($arrArtistes, $ligne);
}

// Ferme le curseur, afin de libérer de l'espace mémoire
$objResultat->closeCursor();

?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Festival OFF Québec</title>
	<link rel="icon"
		  type="image/png"
		  href="liaisons/images/favicon.png">
	<link href="liaisons/css/styles.css" rel="stylesheet">
</head>

<body>
<?php include ("../ressources/vues/fragments/entete.inc.php"); ?>
<h1>Festival OFF</h1>
<h2>En vedette</h2>
<?php $intNombreElements = count($arrArtistes);
$intNombreHasard = rand(0, $intNombreElements - 1) ?>
<a href="artistes.php?id=<?php echo $arrArtistes[$intNombreHasard]["id"]; ?>">
    <?php echo $arrArtistes[$intNombreHasard]["nom"]; ?></a>
<!--            --><?php //var_dump($intNombreHasard); ?>
<h2>Actualités</h2>
<article>
    <?php for ($intCompteur = 0; $intCompteur < count($arrActualites); $intCompteur++) { ?>
		<h3><?php echo $arrActualites[$intCompteur]['titre']; ?></h3>
		<p><?php echo $arrActualites[$intCompteur]['actualite']; ?></p>
    <?php } ?>
</article>
<p><a href="programmation.php">Voir la programmation complète</a></p>
<?php include ("../ressources/vues/fragments/pied.inc.php"); ?>
<script src="liaisons/js/_menu.js"></script>
<script>
	//ajout de la classe js, pour la gestion de la feuille de style, si le JavaScript est actif
	document.body.classList.add('js');
</script>
<?php //var_dump($arrActualites) ?>
<?php //var_dump($arrArtistes) ?>
<?php //var_dump($strCategorie) ?>
</body>

