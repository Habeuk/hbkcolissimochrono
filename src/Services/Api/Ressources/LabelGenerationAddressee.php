<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Destinataire
 * Label generation addressee.
 */
class LabelGenerationAddressee {
  /**
   * Addressee parcel reference.
   *
   * @var string
   */
  public $addresseeParcelRef;
  /**
   * Barcode for reference.
   *
   * @var bool
   */
  public bool $codeBarForReference = false;
  /**
   * Address.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationAddress
   */
  public LabelGenerationAddress $address;
}
