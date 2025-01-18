import "../scss/hbkcolissimochrono.scss";
import "../scss/edit.scss";
import widget from "./widget.js";

(function (Drupal) {
  /**
   * Permet d'ouvrir le popup pour la selection du pickup.
   * C'est drupal qui determine s'il faut l'ouvrir ou pas.
   * Le popup peut ne pas s'ouvrir s'il ya deja une adresse selectionné.
   *
   * @param {*} ajax
   * @param {*} response
   * @param {*} status
   */
  Drupal.AjaxCommands.prototype.CommandColissimoPickUp = function (DrupalAjax, datas, status) {
    console.log(" DrupalAjax : ", DrupalAjax, "\n Content : ", datas, "\n Status : ", status);
    const token = datas.token;
    const address = datas.arguments.address;
    const shippingMethod = datas.arguments.shipping_method;
    // Si l'adresse n'est pas definit on n'ouvre pas le pop-up.
    if (address.country_code) {
      window.CommandColissimoPickUp = new widget(document, token, shippingMethod, address);
      window.CommandColissimoPickUp.openModal();
    }
  };
  //
  Drupal.behaviors.hbkcolissimochronoHbkcolissimochrono = {
    attach(context, settings) {
      console.log("It works!");
    },
  };
  //
})(Drupal);
