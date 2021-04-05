﻿<?php
/**
 * @file Prototype projet Festival OFF - page fiche d'artiste
 * @author Gabriel Caron <1861095@csfoy.ca>
 * @version 1.1
 */

// Connexion à la base de données
$connexionBD = new PDO(
    'mysql:host=localhost;dbname=19_pwem2_off', '19_off', 'tim_19_off');

$connexionBD->exec("SET CHARACTER SET utf8");

// Récupération de la queryString
if (isset($_GET['id'])) {
    $intID = $_GET["id"];
} // Sinon affiche un artiste quelconque
else {
    $intID = 12;
}

// Récupère les informations de l'artiste dans la base de données

$objResultat = $connexionBD->query("
SELECT artistes.nom AS nom_artiste,
provenance AS provenance,
description AS description,
site_web AS site_web,
myspace AS my_space
FROM artistes
WHERE artistes.id =" . $intID . "
LIMIT 1");

// Affecte les lignes de résultats dans arrEvenements
$arrArtiste = array();
foreach ($objResultat as $ligne) {
    array_push($arrArtiste, $ligne);
}

// Récupère les styles de l'artiste
$objResultat = $connexionBD->query("
SELECT styles.nom AS styles_artiste,
       styles_artistes.style_id AS id_styles
FROM artistes
INNER JOIN styles_artistes ON artistes.id = styles_artistes.artiste_id
INNER JOIN styles ON styles_artistes.style_id = styles.id
WHERE artistes.id =" . $intID . "");

// Affecte les lignes de résultats dans arrStylesArtistes
$arrStylesArtiste = array();
foreach ($objResultat as $ligne) {
    array_push($arrStylesArtiste, $ligne);
}

// Récupère les événements de l'artiste à partir de la queryString
$objResultat = $connexionBD->query("
SELECT
CONCAT(DATE_FORMAT(date_et_heure, '%d'), '/',
DATE_FORMAT(date_et_heure, '%m'), ' ',
DATE_FORMAT(date_et_heure, '%H'), 'h',
DATE_FORMAT(date_et_heure, '%i')) AS date_heure,
CONCAT(DATE_FORMAT(date_et_heure, '%Y'), '-',
DATE_FORMAT(date_et_heure, '%m'), '-', DATE_FORMAT(date_et_heure, '%d'), ' ', DATE_FORMAT(date_et_heure, '%H'), ':',
DATE_FORMAT(date_et_heure, '%i')) AS balise_time,
lieux.nom AS lieux
FROM evenements
INNER JOIN artistes ON evenements.artiste_id = artistes.id
INNER JOIN lieux ON evenements.lieu_id = lieux.id
WHERE artiste_id = " . $intID . "
ORDER BY lieux.nom, DAYOFMONTH(date_et_heure), MONTH(date_et_heure), HOUR(date_et_heure), MINUTE(date_et_heure), artistes.nom");


// Affecte les lignes de résultats dans arrEvenements
$arrEvenements = array();
foreach ($objResultat as $arrLigne) {
    array_push($arrEvenements, $arrLigne);
}

// Cherche les id des styles de l'artiste dans le tableau arrStylesArtiste
// La chaîne sert ensuite pour la requête SQL afin de faire un WHERE dans la BD
// avec les styles de l'artiste pour trouver les autres artistes avec les mêmes styles
for ($intCompteur = 0; $intCompteur < $intNombreStyles = count($arrStylesArtiste); $intCompteur++) {
    if ($intNombreStyles == 1) {
        $strStylesPourRequeteSQL = $arrStylesArtiste[$intCompteur]["id_styles"];
    } else {
        if ($intCompteur == $intNombreStyles - 1) {
            $strStylesPourRequeteSQL .= $arrStylesArtiste[$intCompteur]["id_styles"];
        } else {
            $strStylesPourRequeteSQL .= $arrStylesArtiste[$intCompteur]["id_styles"] . ", ";
        }
    }
}

//
//echo "Nombre de styles: ".$intNombreStyles;
//var_dump($strStylesPourRequeteSQL);
//

$objResultat = $connexionBD->query("
SELECT DISTINCT artistes.nom,
artistes.id, styles.nom AS styles
FROM artistes
INNER JOIN styles_artistes ON artistes.id = styles_artistes.artiste_id
INNER JOIN styles ON styles_artistes.style_id = styles.id
WHERE style_id IN (" . $strStylesPourRequeteSQL . ") AND artiste_id NOT IN (" . $intID . ", 39)");

// L'artiste 39 est exclus, car il n'était pas prévu cette année

// Affecte les lignes de résultats dans arrRecommendations
$arrRecommendations = array();
foreach ($objResultat as $arrLigne) {
    array_push($arrRecommendations, $arrLigne);
}
//var_dump($arrRecommendations);

// Ferme le curseur, afin de libérer de l'espace mémoire
$objResultat->closeCursor();

?>

<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $arrArtiste[0]['nom_artiste']; ?> - Festival OFF</title>
	<link rel="icon"
		  type="image/png"
		  href="liaisons/images/favicon.png">
	<link href="liaisons/css/styles.css" rel="stylesheet">
	<style>
		.photo-artiste__image {
			grid-column: 1 / 13;
			background: url("liaisons/images/<?php echo $intID; ?>_artistes_00002_w670.jpg");
			background-size: cover;
			background-position: top;
			height: 600px;
			width: 100%;
			border-radius: 30px;
		}

		@media (min-width: 600px) {
			.photo-artiste__image {
				grid-column: 3 / 13;
				background: radial-gradient(39.8% 39.8% at 13.22% 14.23%, #FFFFFF 21.88%, rgba(255, 255, 255, 0) 100%), url("liaisons/images/<?php echo $intID; ?>_artistes_00002_w1340.jpg");
				background-size: cover;
				background-position: top;
				height: 700px;
				width: 700px;
				border-radius: 500px;
			}
		}

		@media (min-width: 800px) {
			.photo-artiste__image {
				grid-column: 4 / 13;
				height: 800px;
				width: 800px;
			}
		}

		@media (min-width: 1000px) {
			.photo-artiste__image {
				grid-column: 5 / 13;
				height: 800px;
				width: 800px;
			}
		}
	</style>
</head>
<body>
<a href="#contenu" class="visuallyhidden focusable sauter-contenu">Allez au contenu</a>
<?php include ("../ressources/vues/fragments/entete.inc.php"); ?>
<main class="main">
	<div class="photo-artiste">
		<div class="photo-artiste__image">
		</div>
	</div>
	<section id="contenu" class="identification">
		<div class="identification__conteneur">
			<div class="conteneur-titre">
				<h1 class="identification__nom"><?php echo $arrArtiste[0]['nom_artiste']; ?></h1>
				<div class="souligne--renverse"></div>
			</div>
			<ul class="identification__styles-liste"><?php for ($intCompteur = 0; $intCompteur < count($arrStylesArtiste); $intCompteur++) { ?>
					<li class="identification__styles-item"><?php echo $arrStylesArtiste[$intCompteur]['styles_artiste']; ?></li>
                <?php } ?>
			</ul>
			<p class="identification__provenance"><?php echo $arrArtiste[0]['provenance']; ?></p>
		</div>
	</section>
	<section class="infos texte-courant">
		<div class="infos__conteneur">
			<div class="conteneur-titre">
				<h2 class="infos__titre">Biographie</h2>
				<div class="souligne--regulier"></div>
			</div>
			<p class="infos__texte"><?php echo $arrArtiste[0]['description']; ?></p>
			<div class="conteneur-titre">
				<h2 class="infos__titre titre-mini">Voir aussi</h2>
				<div class="souligne--mini"></div>
			</div>
			<ul class="infos__voirAussiListe">
                <?php if ($arrArtiste[0]['site_web'] != null) { ?>
					<li class="infos__voirAussiItem"><a class="lien-externe"
														href="<?php echo $arrArtiste[0]['site_web']; ?>"
														target="_blank">Site officiel</a>
					</li>
                <?php }
                if ($arrArtiste[0]['my_space'] != null) { ?>
					<li>
						<a class="lien-externe" href="<?php echo $arrArtiste[0]['my_space']; ?>"
						   target="_blank">Page Myspace</a>
					</li>
                <?php } ?>
			</ul>
		</div>
		<div class="quand">
			<div class="quand__conteneur">
				<div class="conteneur-titre">
					<h2 class="quand__titre">Quand</h2>
					<div class="souligne--regulier"></div>
				</div>
				<ul class="quand__liste">
                    <?php for ($intCompteur = 0; $intCompteur < count($arrEvenements); $intCompteur++) { ?>
						<li class="quand__item">
							<time datetime="<?php echo $arrEvenements[$intCompteur]['balise_time']; ?>"><?php echo $arrEvenements[$intCompteur]['date_heure']; ?></time>
							,<br>
                            <?php echo $arrEvenements[$intCompteur]['lieux']; ?></li>
                    <?php } ?>
				</ul>
			</div>
		</div>
	</section>
	<section class="galerie">
		<div class="conteneur-titre">
			<h2 class="galerie__titre">Galerie photos</h2>
			<div class="souligne--regulier"></div>
		</div>
		<ul class="galerie__liste">
			<li class="galerie__item">
				<figure class="galerie__figure">
					<picture>
						<source
								srcset="liaisons/images/<?php echo $intID; ?>_artistes_00001_w670.jpg 1x, liaisons/images/<?php echo $intID; ?>_artistes_00001_w1340.jpg 2x">
						<img class="galerie__img" src="liaisons/images/<?php echo $intID; ?>_artistes_00001_w670.jpg"
							 alt="">
					</picture>
					<figcaption
							class="galerie__figcaption screen-reader-only"><?php echo $arrArtiste[0]['nom_artiste']; ?>
						en concert
					</figcaption>
				</figure>
			</li>
			<li class="galerie__item">
				<figure class="galerie__figure">
					<picture>
						<source
								srcset="liaisons/images/<?php echo $intID; ?>_artistes_00002_w670.jpg 1x, liaisons/images/<?php echo $intID; ?>_artistes_00002_w1340.jpg 2x">
						<img class="galerie__img" src="liaisons/images/<?php echo $intID; ?>_artistes_00002_w670.jpg"
							 alt="">
					</picture>
					<figcaption
							class="galerie__figcaption screen-reader-only"><?php echo $arrArtiste[0]['nom_artiste']; ?>
						en concert
					</figcaption>
				</figure>
			</li>
			<li class="galerie__item">
				<figure class="galerie__figure">
					<picture>
						<source
								srcset="liaisons/images/<?php echo $intID; ?>_artistes_00003_w670.jpg 1x, liaisons/images/<?php echo $intID; ?>_artistes_00003_w1340.jpg 2x">
						<img class="galerie__img" src="liaisons/images/<?php echo $intID; ?>_artistes_00003_w670.jpg"
							 alt="">
					</picture>
					<figcaption
							class="galerie__figcaption screen-reader-only"><?php echo $arrArtiste[0]['nom_artiste']; ?>
						en concert
					</figcaption>
				</figure>
			</li>
		</ul>
	</section>
    <?php include ("../ressources/vues/fragments/suggestions-artistes.inc.php"); ?>
</main>
<?php include ("../ressources/vues/fragments/pied.inc.php"); ?>
<!-- Lien sécuritaire vers le serveur de contenu (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
		integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
		crossorigin="anonymous"></script>

<!-- Si l’objet jQuery n’existe pas, écrire une balise script avec un lien relatif vers jQuery -->
<script>window.jQuery || document.write('<script src="liaisons/js/jquery-3.5.1.min.js">\x3C/script>')</script>

<script src="liaisons/js/_menu.js"></script>
<script>
	//ajout de la classe js, pour la gestion de la feuille de style, si le JavaScript est actif
	document.body.classList.add('js');
</script>
<?php //var_dump($strCategorie) ?>
<?php //var_dump($intCategorie) ?>
<?php //var_dump($arrArtiste) ?>
<?php //var_dump($arrEvenements) ?>
<?php //var_dump($objResultat) ?>
<?php //var_dump($arrStylesArtiste) ?>
<?php //var_dump($arrRecommendations) ?>
</body>