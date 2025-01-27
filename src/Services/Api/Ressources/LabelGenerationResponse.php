<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Label generation response.
 */
class LabelGenerationResponse {
  /**
   * Messages.
   *
   * @var \Drupal\commerce_shipping_colissimo\Api\LabelGenerationMessage[]|null
   */
  public $messages;
  
  /**
   * Label V2 response.
   *
   * @var \Drupal\commerce_shipping_colissimo\Api\LabelGenerationLabelV2|null
   */
  public $labelV2Response;
  
  /**
   * Files.
   */
  public array $files = [];
}
