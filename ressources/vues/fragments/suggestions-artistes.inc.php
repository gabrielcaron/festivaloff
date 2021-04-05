<section class="suggestions">
    <div class="suggestions__conteneur">
        <div class="conteneur-titre">
            <h2 class="suggestions__titre"><span class="suggestions__titreligne1">Vous pourriez</span><span
                    class="suggestions__titreligne2">aussi aimer…</span>
            </h2>
            <div class="souligne--renverse"></div>
        </div>
        <ul class="suggestions__liste">
            <?php $intNombreElements = count($arrRecommendations);
            // S'il y a trois éléments ou moins, on boucle sur la quantité qu'il y a
            if ($intNombreElements <= 3) {
                for ($intCompteur = 0; $intCompteur <= $intNombreElements - 1; $intCompteur++) {
                    ?>
                    <li class="suggestions__item">
                        <a class="suggestions__lien"
                           href="artistes.php?id=<?php echo $arrRecommendations[$intCompteur]["id"]; ?>">
                            <picture>
                                <source
                                    srcset="liaisons/images/<?php echo $arrRecommendations[$intCompteur]["id"]; ?>_artistes_0000<?php echo $intCompteur + 1; ?>_w670.jpg 1x, liaisons/images/<?php echo $arrRecommendations[$intCompteur]["id"]; ?>_artistes_0000<?php echo $intCompteur + 1; ?>_w1340.jpg 2x">
                                <img class="suggestions__img"
                                     src="liaisons/images/<?php echo $arrRecommendations[$intCompteur]["id"]; ?>_artistes_0000<?php echo $intCompteur + 1; ?>_w670.jpg"
                                     alt="">
                            </picture>
                            <h3 class="suggestions__artiste">
                                <?php echo $arrRecommendations[$intCompteur]["nom"]; ?>
                            </h3>
                        </a>
                        <p class="suggestions__styles">
                            <?php echo $arrRecommendations[$intCompteur]["styles"]; ?>
                        </p>
                    </li>
                    <?php
                } // fin du for
            } // fin du if
            // Sinon on sélectionne au hasard trois éléments dans le tableau arrRecommendations
            // afin d'afficher des éléments différents à chaque chargement de la page
            else {
                if ($intNombreElements > 3) {
                    for ($intCompteur = 0; $intCompteur < 3; $intCompteur++) {
                        ; ?>
                        <li class="suggestions__item">
                            <a class="suggestions__lien"
                               href="artistes.php?id=<?php echo $arrRecommendations[$intNombreHasard = rand(0, $intNombreElements - 1)]["id"]; ?>">
                                <picture>
                                    <source
                                        srcset="liaisons/images/<?php echo $arrRecommendations[$intNombreHasard]["id"]; ?>_artistes_0000<?php echo $intCompteur + 1; ?>_w670.jpg 1x, liaisons/images/<?php echo $arrRecommendations[$intNombreHasard]["id"]; ?>_artistes_0000<?php echo $intCompteur + 1; ?>_w1340.jpg 2x">
                                    <img class="suggestions__img"
                                         src="liaisons/images/<?php echo $arrRecommendations[$intNombreHasard]["id"]; ?>_artistes_0000<?php echo $intCompteur + 1; ?>_w670.jpg"
                                         alt="">
                                </picture>
                                <h3 class="suggestions__artiste">
                                    <?php echo $arrRecommendations[$intNombreHasard]["nom"]; ?>
                                </h3>
                            </a>
                            <p class="suggestions__styles">
                                <?php echo $arrRecommendations[$intNombreHasard]["styles"]; ?>
                            </p>
                        </li>
                        <?php array_splice($arrRecommendations, $intNombreHasard, 1);
                        $intNombreElements += -1;
                    } // fin du for
                } // fin du if
            } // fin du else ?>
        </ul>
    </div>
</section>