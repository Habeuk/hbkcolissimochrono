<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Label generation sender.
 */
class LabelGenerationSender {
  /**
   * Sender parcel reference.
   *
   * @var string
   */
  public $senderParcelRef;
  /**
   * Address.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationAddress
   */
  public LabelGenerationAddress $address;
  
}
