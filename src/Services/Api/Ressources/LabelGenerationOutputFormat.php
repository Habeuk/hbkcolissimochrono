<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 *
 * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#section/Produits-disponibles/Informations-necessaires-selon-le-type-de-colis-demande
 * @author stephane
 *        
 */
class LabelGenerationOutputFormat {
  /**
   * X.
   *
   * @var int
   */
  public int $x = 0;
  
  /**
   * Y.
   *
   * @var int
   */
  public int $y = 0;
  
  /**
   * Output printing type.
   *
   * @var string
   */
  protected string $outputPrintingType;
  
  /**
   * --
   * ZPL_10x15_203dpi / ZPL_10x15_300dpi / DPL_10x15_203dpi
   * / DPL_10x15_300dpi
   * / PDF_10x15_300dpi / PDF_A4_300dpi
   */
  public function SetOutputPrintingType(string $value) {
    $outputFormat = PrintFormats::tryFrom($value);
    if ($outputFormat === null)
      throw new \UnexpectedValueException('The requested format type does not exist');
    $this->outputPrintingType = $outputFormat->value;
  }
  
  public function getOutputPrintingType() {
    return $this->outputPrintingType;
  }
}