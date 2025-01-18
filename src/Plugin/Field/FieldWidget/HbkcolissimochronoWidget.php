<?php

namespace Drupal\hbkcolissimochrono\Plugin\Field\FieldWidget;

use Drupal\commerce_shipping\Plugin\Field\FieldWidget\ShippingRateWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hbkcolissimochrono\HbkcolissimochronoAjax;

/**
 * Plugin implementation of 'commerce_shipping_rate'.
 *
 * @FieldWidget(
 *   id = "commerce_hbkcolissimochrono",
 *   label = @Translation("Shipping rate by Hbkcolissimochrono"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class HbkcolissimochronoWidget extends ShippingRateWidget {
  
  /**
   * Ajax callback.
   */
  public static function ajaxRefresh(array &$form, FormStateInterface $form_state) {
    $response = parent::ajaxRefresh($form, $form_state);
    HbkcolissimochronoAjax::addCommandColissimoPickUp($response, $form, $form_state);
    return $response;
  }
  
}