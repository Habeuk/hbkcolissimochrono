<?php

namespace Drupal\hbkcolissimochrono;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\hbkcolissimochrono\Ajax\CommandColissimoPickUp;
use Drupal\Core\Ajax\ReplaceCommand;

class HbkcolissimochronoAjax {
  
  static public function addCommandColissimoPickUp(AjaxResponse &$response, array $form, FormStateInterface $form_state) {
    // On verifie la presence du champs : hbkcolissimochrono_pickup_book afin de
    // determiner si on doit afficher la map ou pas.
    if (!empty($form['shipping_information']['hbkcolissimochrono_pickup']['hbkcolissimochrono_pickup_book']) && !empty($form['shipping_information']['shipments'][0]['#shipment'])) {
      $open_map = false;
      /**
       *
       * @var \Drupal\commerce_shipping\Entity\Shipment $shipment
       */
      $shipment = $form['shipping_information']['shipments'][0]['#shipment'];
      /**
       *
       * @var \Drupal\commerce_shipping\Entity\ShippingMethod $ShippingMethod
       */
      $ShippingMethod = $shipment->getShippingMethod();
      if ($ShippingMethod) {
        /**
         *
         * @var \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkChronoPost $plugin
         */
        $plugin = $ShippingMethod->getPlugin();
        $open_map = $plugin->isRelay();
      }
      if ($open_map) {
        $arguments = [
          'shipping_method' => null,
          'address' => []
        ];
        if (!empty($form['shipping_information']['shipments'][0]['shipping_method']['widget'][0]['#options'])) {
          $shipping_method_options = $form['shipping_information']['shipments'][0]['shipping_method']['widget'][0]['#options'];
          // dump( $shipping_method_options );
          $selectMethod = $form_state->getValue([
            'shipping_information',
            'shipments',
            0,
            'shipping_method',
            0
          ]);
          if (!empty($shipping_method_options[$selectMethod])) {
            $shipping_method = $shipping_method_options[$selectMethod];
            if ($shipping_method instanceof \Drupal\Component\Render\FormattableMarkup) {
              $arguments['shipping_method'] = $shipping_method->__toString();
            }
          }
        }
        
        // Get address.
        if (!empty($form['shipping_information']['shipping_profile']['#inline_form'])) {
          /**
           *
           * @var \Drupal\commerce_order\Plugin\Commerce\InlineForm\CustomerProfile $inline_form
           */
          $inline_form = $form['shipping_information']['shipping_profile']['#inline_form'];
          /**
           *
           * @var \Drupal\profile\Entity\Profile $profile
           */
          $profile = $inline_form->getEntity();
          $arguments['address'] = reset($profile->get('address')->getValue());
        }
        // add custom js.
        if (!$arguments['address']) {
          \Drupal::messenger()->addWarning("Impossible de determiner l'adresse");
        }
        else {
          /**
           *
           * @var \Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings $default_settings
           */
          $default_settings = \Drupal::service('hbkcolissimochrono.default_settings');
          $response->addCommand(new CommandColissimoPickUp($default_settings->getWidgetAuthenticationToken(), $arguments));
        }
      }
    }
    // On doit mettre à jour le champs concernant les informations sur la
    // section du point relais.
    if (!empty($form['shipping_information']['hbkcolissimochrono_pickup'])) {
      // $content =
      // \Drupal::service('renderer')->render($form['shipping_information']['hbkcolissimochrono_pickup']);
      $response->addCommand(new ReplaceCommand('#hbkcolissimochrono_pickup-container', $form['shipping_information']['hbkcolissimochrono_pickup']));
    }
    return $response;
  }
  
}