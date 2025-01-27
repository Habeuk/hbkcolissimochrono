<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Type de produit et tarifs
 * Label generation service.
 */
class LabelGenerationService {
  /**
   * Product code.
   *
   * @var string
   */
  public $productCode;
  /**
   * Deposit date.
   *
   * @var \DateTime
   */
  public $depositDate;
  /**
   * Commercial name.
   *
   * @var string
   */
  public string $commercialName;
}