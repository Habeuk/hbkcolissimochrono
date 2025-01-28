<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Stephane888\Debug\ExceptionExtractMessage;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileExists;
use Drupal\file\Entity\File;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
   * * Génère une expédition : annonce informatique du colis + documents
   * associés
   * (étiquette et déclarations douanières)
   *
   * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#tag/Description-and-examples-of-WS-SLS-methods/operation/generateLabel
   * @param ShipmentInterface $shipment
   * @return \Drupal\hbkcolissimochrono\Entity\EtiquetteColissimo|mixed
   */
  public function generateLabel(ShipmentInterface $shipment) {
    $payload = new LabelGenerationPayload();
    $payload->outputFormat = $this->buildOutputFormat();
    $payload->letter = $this->buildLetter($shipment);
    try {
      $orders = \Drupal::entityTypeManager()->getStorage('hbketiquetecolisimo')->loadByProperties([
        'order' => $shipment->getOrderId()
      ]);
      if (empty($orders)) {
        $datas = $this->RestClient->post("/generateLabel", $payload);
        $EtiquetteColissimo = $this->createEntityLabel($datas, $shipment);
      }
      else {
        $EtiquetteColissimo = reset($orders);
      }
      return $EtiquetteColissimo;
    }
    catch (\Exception $e) {
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
      return $this->RestClient->post("/checkGenerateLabel", $payload);
    }
    catch (\Exception $e) {
      \Stephane888\Debug\debugLog::symfonyDebug(ExceptionExtractMessage::errorAll($e), 'generateLabel', true);
      $this->logger->error(ExceptionExtractMessage::errorAllToString($e));
    }
  }
  
  /**
   *
   * @param array $datas
   * @param ShipmentInterface $shipment
   * @return \Drupal\hbkcolissimochrono\Entity\EtiquetteColissimo
   */
  private function createEntityLabel(array $datas, ShipmentInterface $shipment) {
    if (!empty($datas['files'])) {
      $orderId = $shipment->getOrderId();
      $label = $orderId;
      $id_file = $orderId;
      $metadatas = Json::decode($datas['datas']);
      if (!empty($metadatas['labelV2Response']['parcelNumber'])) {
        $label = $orderId . '|' . $metadatas['labelV2Response']['parcelNumber'];
        $id_file = $orderId . '--' . $metadatas['labelV2Response']['parcelNumber'];
      }
      $files = $this->saveFiles($datas['files'], $id_file);
      $values = [
        'label' => $label,
        'order' => $orderId,
        'metadatas' => $datas['datas']
      ];
      foreach ($files as $file) {
        $values['files'][] = [
          'target_id' => $file->id()
        ];
      }
      $EtiquetteColissimo = \Drupal\hbkcolissimochrono\Entity\EtiquetteColissimo::create($values);
      $EtiquetteColissimo->save();
      return $EtiquetteColissimo;
    }
    else {
      \Drupal::messenger()->addError("Aucune etiquette generer, commande : " . $shipment->getOrderId());
      $this->logger->error("Aucune etiquette generer, commande : " . $shipment->getOrderId());
    }
  }
  
  /**
   *
   * @param array $files_string
   * @return \Drupal\file\Entity\File[]
   */
  private function saveFiles(array $files_string, string $id_file) {
    $destination = "public://colissimo_labels/";
    $files = [];
    /**
     *
     * @var \Drupal\Core\File\FileSystem $filesystem
     */
    $filesystem = \Drupal::service('file_system');
    // Check the directory exists before writing data to it.
    if ($filesystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS))
      foreach ($files_string as $key => $data) {
        $filename = "label--" . $id_file . '--' . $key . ".pdf";
        $newUri = $filesystem->saveData($data, $destination . $filename, FileExists::Replace);
        $file = File::create([
          'uri' => $newUri,
          'filename' => $filename
        ]);
        $file->setPermanent();
        $file->save();
        $files[] = $file;
      }
    
    return $files;
  }
}