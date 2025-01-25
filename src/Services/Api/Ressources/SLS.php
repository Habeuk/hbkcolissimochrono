<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Stephane888\Debug\ExceptionExtractMessage;

/**
 * La Poste - Colissimo met le service SLS, Simple Label Solution, à la
 * disposition de ses clients pour réaliser leurs affranchissements.
 * https://www.colissimo.fr/doc-colissimo/redoc-sls/fr
 *
 * @author stephane
 *        
 */
class SLS extends SLS_ressources {
  
  /**
   * Génère une expédition : annonce informatique du colis + documents associés
   * (étiquette et déclarations douanières)
   *
   * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#tag/Description-and-examples-of-WS-SLS-methods/operation/generateLabel
   * @param ShipmentInterface $shipment
   */
  public function generateLabel(ShipmentInterface $shipment) {
    $payload = new LabelGenerationPayload();
    $payload->outputFormat = $this->buildOutputFormat();
    $payload->letter = $this->buildLetter($shipment);
    \Stephane888\Debug\debugLog::symfonyDebug($payload, 'generateLabel', true);
    try {
      return $this->RestClient->post("/generateLabel", $payload);
    }
    catch (\Exception $e) {
      \Stephane888\Debug\debugLog::symfonyDebug(ExceptionExtractMessage::errorAll($e), 'generateLabel', true);
      $this->logger->error(ExceptionExtractMessage::errorAllToString($e));
    }
  }
  
  /**
   * Permet de tester les requêtes web service.
   *
   * Fonctionne comme la méthode generateLabel mais ne renvoie pas les
   * informations suivantes :
   * - Le numéro de colis
   * - Le ou les liens xop
   * - La balise pdfUrl en cas de sortie PDF demandée
   *
   * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#tag/SlsServiceWS-:-documentation/operation/checkGenerateLabel
   *
   * @param ShipmentInterface $shipment
   */
  public function checkGenerateLabel(ShipmentInterface $shipment) {
    // dd($shipment->get('hbkcolissimochrono_pickup')->value);
    $payload = new LabelGenerationPayload();
    $payload->outputFormat = $this->buildOutputFormat();
    $payload->letter = $this->buildLetter($shipment);
    try {
      return $this->RestClient->post("https://ws.colissimo.fr/sls-ws/SlsServiceWSRest/2.0/checkGenerateLabel", $payload);
    }
    catch (\Exception $e) {
      \Stephane888\Debug\debugLog::symfonyDebug(ExceptionExtractMessage::errorAll($e), 'generateLabel', true);
      $this->logger->error(ExceptionExtractMessage::errorAllToString($e));
    }
  }
}