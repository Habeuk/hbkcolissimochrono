<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Label generation payload.
 */
class LabelGenerationPayload {
  /**
   * Output format.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationOutputFormat
   */
  public LabelGenerationOutputFormat $outputFormat;
  
  /**
   * Label generation letter.
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationLetter
   */
  public LabelGenerationLetter $letter;
  
}
