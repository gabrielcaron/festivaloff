<?php
/**
 * @file Prototype projet Festival OFF - page programmation
 * @author Gabriel Caron <1861095@cegep-ste-foy.qc.ca>
 * @version 1.0
 */


// Connexion à la base de données
$connexionBD = new PDO(
    'mysql:host=localhost;dbname=19_pwem2_off', '19_off', 'tim_19_off');

$connexionBD->exec("SET CHARACTER SET utf8");

// Récupère les dates du festival dans la base de données

$objResultat = $connexionBD->query("
SELECT DISTINCT
DATE_FORMAT(date_et_heure, '%e') AS jour,
DATE_FORMAT(date_et_heure, '%c') AS mois,
DATE_FORMAT(date_et_heure, '%Y') AS annee
FROM evenements
ORDER BY DAYOFMONTH(date_et_heure), MONTH(date_et_heure), HOUR(date_et_heure), MINUTE(date_et_heure)");

// Affecte les lignes de résultats dans arrDatesFestival
$arrDatesFestival = array();
foreach ($objResultat as $arrLigne) {
    array_push($arrDatesFestival, $arrLigne);
}

// Récupération de la queryString

if (isset($_GET['jour'])) {
    $intJour = $_GET["jour"];
    $intMois = $_GET["mois"];
    $intAnnee = $_GET["annee"];
}
// Sinon affiche le premier jour du festival dans le tableau arrDatesFestival
// en affichant la première ligne
else {
    $intJour = $arrDatesFestival[0]["jour"];
    $intMois = $arrDatesFestival[0]["mois"];
    $intAnnee = $arrDatesFestival[0]["annee"];
}

// Récupère les événements du festival dans la base de données à partir de la queryString

$objResultat = $connexionBD->query("
SELECT
CONCAT(DATE_FORMAT(date_et_heure, '%H'), 'h',
DATE_FORMAT(date_et_heure, '%i')) AS heure,
CONCAT(DATE_FORMAT(date_et_heure, '%H'), ':',
DATE_FORMAT(date_et_heure, '%i')) AS balise_time,
artistes.id AS id_artistes,
artistes.nom AS artistes,
lieux.nom AS lieux
FROM evenements
INNER JOIN artistes ON evenements.artiste_id = artistes.id
INNER JOIN lieux ON evenements.lieu_id = lieux.id
WHERE DAYOFMONTH(date_et_heure) =" . $intJour . "
ORDER BY lieux.nom, DAYOFMONTH(date_et_heure), MONTH(date_et_heure), HOUR(date_et_heure), MINUTE(date_et_heure), artistes.nom");


// Affecte les lignes de résultats dans arrEvenements
$arrEvenements = array();
foreach ($objResultat as $arrLigne) {
    array_push($arrEvenements, $arrLigne);
}

// Récupère la date du jour pour le datetime du h2

$objResultat = $connexionBD->query("
SELECT DISTINCT
CONCAT(DATE_FORMAT(date_et_heure, '%Y'), '-',
DATE_FORMAT(date_et_heure, '%m'), '-',
DATE_FORMAT(date_et_heure, '%d')) AS datetime_jour
FROM evenements
WHERE DAYOFMONTH(date_et_heure) = 9
ORDER BY DAYOFMONTH(date_et_heure), MONTH(date_et_heure), HOUR(date_et_heure), MINUTE(date_et_heure)");


// Affecte les lignes de résultats dans arrEvenements
$arrDateJour = array();
foreach ($objResultat as $arrLigne) {
    array_push($arrDateJour, $arrLigne);
}

// Ferme le curseur, afin de libérer de l'espace mémoire
$objResultat->closeCursor();


// Tableau des mois
$arrMois = array("janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");


?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Programmation - Festival OFF</title>
	<link rel="icon"
		  type="image/png"
		  href="liaisons/images/favicon.png">
	<link href="liaisons/css/styles.css" rel="stylesheet">
</head>
<body>
<?php include("../ressources/vues/fragments/entete.inc.php"); ?>
<h1>Programmation</h1>
<!--Sections Dates-->
<h2>
	<time datetime="<?php echo $arrDateJour[0]["datetime_jour"] ?>"><?php echo $intJour . " " . $arrMois[$intMois - 1] . " " . $intAnnee ?></time>
</h2>

<?php for ($intCompteur = 0; $intCompteur < count($arrDatesFestival); $intCompteur++) { ?>
	<a href="?jour=<?php echo $arrDatesFestival[$intCompteur]['jour']; ?>&mois=<?php echo $arrDatesFestival[$intCompteur]['mois'] ?>&annee=<?php echo $arrDatesFestival[$intCompteur]['annee'] ?>">
        <?php echo $arrDatesFestival[$intCompteur]['jour']; ?></a>
<?php } ?>

<!--Section Événements-->
<?php $strLieuCourant = "";

// Au départ, si le lieu courant est vide par rapport au lieu suivant, début d'un ul
for ($intCompteur = 0;
$intCompteur < count($arrEvenements);
$intCompteur++) { ?>
<?php if ($strLieuCourant == "") { ?>
<ul>
	<li>
		<!--Affichage du premier li du premier lieu-->
        <?php echo $arrEvenements[$intCompteur]['lieux'];
        $strLieuCourant = $arrEvenements[$intCompteur]['lieux'];
        $strLieuSuivant = $arrEvenements[$intCompteur + 1]['lieux']; ?>
		<ul> <!--Début ul imbriqué des événements-->
			<li>
				<time datetime="<?php echo $arrEvenements[$intCompteur]['balise_time']; ?>"><?php echo $arrEvenements[$intCompteur]['heure']; ?></time>
				,
				<a href="artistes.php?id=<?php echo $arrEvenements[$intCompteur]['id_artistes']; ?>">
                    <?php echo $arrEvenements[$intCompteur]['artistes']; ?></a>,
                <?php
                // Aller chercher les styles de l'artiste dans la bd
                $objResultat = $connexionBD->query("
						SELECT styles.nom
						FROM styles_artistes
						INNER JOIN styles ON styles_artistes.style_id = styles.id
						WHERE artiste_id =" . $arrEvenements[$intCompteur]['id_artistes'] . "");
                // Affecte les lignes de résultats dans arrStylesArtiste
                $arrStylesArtiste = array();
                foreach ($objResultat as $arrLigne) {
                    array_push($arrStylesArtiste, $arrLigne);
                }
                // Converti le tableau multidirectionnel en un tableau simple, puis en string
                echo $strStyles = implode(', ', array_map(function ($ligne) {
                    return $ligne['nom'];
                }, $arrStylesArtiste));
                $arrEvenements[$intCompteur]['styles'] = $strStyles;
                ?>
			</li>
            <?php }    // Fin du if
            else {
            //             echo " strLieuCourant: " . $strLieuCourant, " strLieuSuivant: " . $strLieuSuivant;

            // Si le lieu courant est différent du lieu suivant, on commence un nouvel li
            if ($strLieuCourant != $strLieuSuivant) { ?>
		</ul> <!--Fermeture du ul des événements-->
	</li> <!--Fermeture du li du premier lieu-->
	<li> <!--Début d'un nouvel li pour le lieu suivant-->
        <?php echo $arrEvenements[$intCompteur]['lieux'];
        $strLieuCourant = $arrEvenements[$intCompteur]['lieux'];
        $strLieuSuivant = $arrEvenements[$intCompteur + 1]['lieux'];

        ?>
		<ul> <!--Début ul imbriqué des événements-->
			<li>
				<time datetime="<?php echo $arrEvenements[$intCompteur]['balise_time']; ?>"><?php echo $arrEvenements[$intCompteur]['heure']; ?></time>
				,
				<a href="artistes.php?id=<?php echo $arrEvenements[$intCompteur]['id_artistes']; ?>">
                    <?php echo $arrEvenements[$intCompteur]['artistes']; ?></a>,
                <?php
                // Aller chercher les styles de l'artiste dans la bd
                $objResultat = $connexionBD->query("
						SELECT styles.nom
						FROM styles_artistes
						INNER JOIN styles ON styles_artistes.style_id = styles.id
						WHERE artiste_id =" . $arrEvenements[$intCompteur]['id_artistes'] . "");
                // Affecte les lignes de résultats dans arrStylesArtiste
                $arrStylesArtiste = array();
                foreach ($objResultat as $arrLigne) {
                    array_push($arrStylesArtiste, $arrLigne);
                }
                // Converti le tableau multidirectionnel en un tableau simple, puis en string
                echo $strStyles = implode(', ', array_map(function ($ligne) {
                    return $ligne['nom'];
                }, $arrStylesArtiste));
                $arrEvenements[$intCompteur]['styles'] = $strStyles;
                ?>
			</li>
            <?php } // fin du if
            else {
                // Si lieu courant est le même que le lieu suivant, on ajoute un li au ul
                if ($strLieuCourant == $strLieuSuivant) { ?>
					<li>
                        <?php $strLieuCourant = $arrEvenements[$intCompteur]['lieux'];
                        $strLieuSuivant = $arrEvenements[$intCompteur + 1]['lieux']; ?>
						<time datetime="<?php echo $arrEvenements[$intCompteur]['balise_time']; ?>"><?php echo $arrEvenements[$intCompteur]['heure']; ?></time>
						,
						<a href="artistes.php?id=<?php echo $arrEvenements[$intCompteur]['id_artistes']; ?>">
                            <?php echo $arrEvenements[$intCompteur]['artistes']; ?></a>,
                        <?php
                        // Aller chercher les styles de l'artiste dans la bd
                        $objResultat = $connexionBD->query("
						SELECT styles.nom
						FROM styles_artistes
						INNER JOIN styles ON styles_artistes.style_id = styles.id
						WHERE artiste_id =" . $arrEvenements[$intCompteur]['id_artistes'] . "");
                        // Affecte les lignes de résultats dans arrStylesArtiste
                        $arrStylesArtiste = array();
                        foreach ($objResultat as $arrLigne) {
                            array_push($arrStylesArtiste, $arrLigne);
                        }
                        //                        // Converti le tableau multidirectionnel en un tableau simple, puis en string
                        echo $strStyles = implode(', ', array_map(function ($ligne) {
                            return $ligne['nom'];
                        }, $arrStylesArtiste));
                        $arrEvenements[$intCompteur]['styles'] = $strStyles;
                        ?>
					</li>
                <?php } // fin du if
            } // fin du else imbriqué
            } // fin du else ?>
			</li> <!-- Fin li du lieu-->
            <?php
            } ?><!--Fin boucle for-->
		</ul><!--Fin ul lieux-->
</ul>
<?php include("../ressources/vues/fragments/pied.inc.php"); ?>
        <?php //var_dump($strCategorie) ?>
        <?php //var_dump($intCategorie) ?>
        <?php //var_dump($intJour) ?>
        <?php //var_dump($strStyles) ?>
        <?php //var_dump($arrStylesArtiste); echo "Nombre: " . count($arrStylesArtiste); ?>
        <?php //var_dump($arrDatesFestival); ?>
        <?php //var_dump($arrEvenements); ?>
        <?php //var_dump($arrDateJour); ?>

</body>

