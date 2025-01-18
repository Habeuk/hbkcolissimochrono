<?php

namespace Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodInterface;
use Drupal\commerce_shipping\Entity\ShipmentInterface;

/**
 *
 * @author stephane
 *        
 */
interface HbkShippingInterface extends ShippingMethodInterface {
  
  /**
   * Permet de recuperer les poids valide en fonction de la configuration
   *
   * @return mixed The shipping method label.
   */
  public function ListWeights(): array;
  
  /**
   * Retourne le poid max en gramme.
   *
   * @return \Drupal\physical\Weight
   */
  public function getMaxWeight(): \Drupal\physical\Weight;
  
  /**
   * Retourne les empaquetages utilisable pour la livraison.
   *
   * @return array
   */
  public function getShippingPackagings(ShipmentInterface $shipment): array;
  
  /**
   *
   * @return string
   */
  public function getColissimoType(): string;
  
}