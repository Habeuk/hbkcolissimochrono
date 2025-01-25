<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Label generation parcel.
 */
class LabelGenerationParcel {
  /**
   * Weight.
   *
   * @var float
   */
  public $weight;
  
  /**
   * Pickup location id.
   *
   * @var string
   */
  public string|int $pickupLocationId;
  
  /**
   * Instruction de livraison pour le livreur
   *
   * @var string
   */
  public string $instructions;
}
