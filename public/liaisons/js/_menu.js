/**
 * @file Menu mobile utilisant jQuery
 * @author Gabriel Caron <1861095@csfoy.ca>
 * @version 1.0
 *
 */

//*******************
// Déclaration d'objet(s)
//*******************

var menu = {
  lblMenuFerme: 'Menu',
  lblMenuOuvert: 'Fermer',

  configurerNav: function () {
    // Injection des balises HTML du libellé du menu mobile
    $(".menu__rangeeBouton").prepend('<button id="menu-mobile" class="menu__controle"><span class="menu__libelle">Menu</span></button>');

    // Ajout d'un écouteur d'événement sur le bouton du menu
    $("#menu-mobile").on('click', function (e) {
      menu.ouvrirFermerNav(e);
    });
  },

  ouvrirFermerNav: function () {
    // Si le menu est fermé, on l'ouvre
    if ($("#navigation").hasClass("menu--ferme") === true) {
      $("#navigation").toggleClass("menu--ferme");
      $(".menu__libelle").text("Fermer");

    }
    // Sinon on le ferme
    else {
      $("#navigation").toggleClass("menu--ferme");
      $(".menu__libelle").text("Menu");
    }
  }
};


//*******************
// Écouteurs d'événements
//*******************

$(document).ready(menu.configurerNav.bind(menu));