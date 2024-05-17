<?php

namespace Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings;
use Drupal\commerce_price\Price;
use Drupal\commerce_shipping\ShippingRate;
use Drupal\physical\Weight;
use Drupal\physical\WeightUnit;

/**
 * Provides the colissimo shipping method.
 *
 * @CommerceShippingMethod(
 *   id = "hbkchronopost",
 *   label = @Translation("ChronoPost by HBK"),
 * )
 */
class HbkChronoPost extends HbkColissimo {
  
}