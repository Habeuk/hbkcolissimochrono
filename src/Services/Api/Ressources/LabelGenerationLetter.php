<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Label generation letter.
 */
class LabelGenerationLetter {
  /**
   * Service.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationService
   */
  public LabelGenerationService $service;
  /**
   * Parcel.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationParcel
   */
  public LabelGenerationParcel $parcel;
  /**
   * Sender.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationSender
   */
  public LabelGenerationSender $sender;
  /**
   * Addressee.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationAddressee
   */
  public LabelGenerationAddressee $addressee;
  
}