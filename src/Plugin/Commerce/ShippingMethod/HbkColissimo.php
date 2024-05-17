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
 *   id = "hbkcolissimo",
 *   label = @Translation("Colissimo by HBK"),
 * )
 */
class HbkColissimo extends HbkShippingBase {
  
  /**
   * Pour le calcul on evolue sur la base que chaque produit serra emballé
   * separement.
   *
   * {@inheritdoc}
   * @see \Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodInterface::calculateRates()
   */
  public function calculateRates(ShipmentInterface $shipment) {
    $rates = [];
    $rate_amount = null;
    $colissimo_formats = $this->configuration['colissimo_formats'];
    /**
     * Liste de colis à livrer.
     *
     * @var array $packagings
     */
    $packagings = $this->getShippingPackagings($shipment);
    foreach ($packagings as $format => $packaging) {
      // On parcourt les types de format.
      foreach ($packaging as $colis) {
        $poidColis = new Weight($colis['weight']['number'], $colis['weight']['unit']);
        $colis_rate_amount = $this->getAmountFromWeight($poidColis);
        if (!$rate_amount) {
          $rate_amount = Price::fromArray($colis_rate_amount);
        }
        else {
          $rate_amount->add(Price::fromArray($colis_rate_amount));
        }
        // Pour chaque colis, on verifie s'il faut ajouter ou pas un montant
        // supplementaire.
        if (!empty($colissimo_formats[$format]) && $colissimo_formats[$format]['amount']['number'] > 0) {
          $rate_amount->add(Price::fromArray($colissimo_formats[$format]['amount']));
        }
      }
    }
    $rates[] = new ShippingRate([
      'shipping_method_id' => $this->parentEntity->id(),
      'service' => $this->services['default'],
      'amount' => $rate_amount,
      'description' => $this->configuration['rate_description']
    ]);
    return $rates;
  }
  
  /**
   * --
   */
  protected function getAmountFromWeight(Weight $weight) {
    $colis_rate_amount = null;
    $colissimo_weights = $this->configuration['colissimo_weights'];
    foreach ($colissimo_weights as $value) {
      $weightByPrice = new Weight($value['weight'], WeightUnit::GRAM);
      if ($weightByPrice->compareTo($weight) === 1) {
        $colis_rate_amount = $value['rate_amount'];
        break;
      }
    }
    return $colis_rate_amount;
  }
  
}