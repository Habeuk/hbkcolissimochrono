<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 *
 * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#section/Format-des-etiquettes/Formats-d'impression
 * @author stephane
 *        
 */
enum PrintFormats: string {
  
  case PDF_10x15_300dpi = 'PDF_10x15_300dpi';
  
  case PDF_A4_300dpi = 'PDF_A4_300dpi';
  
  case PDF_10x10_300dpi = 'PDF_10x10_300dpi';
  
  case PDF_10x15_300dpi_UL = 'PDF_10x15_300dpi_UL';
  
  case PDF_A4_300dpi_UL = 'PDF_A4_300dpi_UL';
  
  case ZPL_10x15_203dpi = 'ZPL_10x15_203dpi';
  
  case ZPL_10x15_300dpi = 'ZPL_10x15_300dpi';
  
  case ZPL_10x10_203dpi = 'ZPL_10x10_203dpi';
  
  case ZPL_10x10_300dpi = 'ZPL_10x10_300dpi';
  
  case ZPL_10x15_203dpi_UL = 'ZPL_10x15_203dpi_UL';
  
  case ZPL_10x15_300dpi_UL = 'ZPL_10x15_300dpi_UL';
  
  case DPL_10x15_203dpi = 'DPL_10x15_203dpi';
  
  case DPL_10x15_300dpi = 'DPL_10x15_300dpi';
  
  case DPL_10x10_203dpi = 'DPL_10x10_203dpi';
  
  case DPL_10x10_300dpi = 'DPL_10x10_300dpi';
  
  case DPL_10x15_203dpi_UL = 'DPL_10x15_203dpi_UL';
  
  case DPL_10x15_300dpi_UL = 'DPL_10x15_300dpi_UL';
}