<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\hbkcolissimochrono\Services\Api\RestClient;
use Drupal\hbkcolissimochrono\Services\Api\ParamInterface;
use Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkShippingInterface;
use Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings;
use Drupal\physical\WeightUnit;
use Drupal\Component\Serialization\Json;
use Drupal\address\AddressInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\Messenger;
use Stephane888\Debug\ExceptionExtractMessage;

/**
 * La Poste - Colissimo met le service SLS, Simple Label Solution, à la
 * disposition de ses clients pour réaliser leurs affranchissements.
 * https://www.colissimo.fr/doc-colissimo/redoc-sls/fr
 *
 * @author stephane
 *        
 */
class SLS_ressources {
  
  /**
   *
   * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr
   * @var RestClient
   */
  protected RestClient $RestClient;
  /**
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\ParamInterface
   */
  protected ParamInterface $Param;
  
  /**
   * LoggerChannelInterface.
   */
  protected LoggerChannelInterface $logger;
  
  /**
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected Messenger $Messenger;
  
  function __construct(RestClient $RestClient, ParamInterface $Param, LoggerChannelInterface $logger, Messenger $Messenger) {
    $this->RestClient = $RestClient;
    $this->Param = $Param;
    $this->logger = $logger;
    $this->Messenger = $Messenger;
  }
  
  public function testDocuments(ShipmentInterface $shipment) {
    $payload = new LabelGenerationPayload();
    $result = $this->RestClient->post("https://ws.colissimo.fr/sandbox/api-document/rest/documents", $payload);
    dd($result);
  }
  
  /**
   * Build letter.
   *
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationLetter Label
   *         generation letter.
   *        
   * @throws \Drupal\commerce_shipping_label\ShippingLabelGenerationException
   */
  protected function buildLetter(ShipmentInterface $shipment): LabelGenerationLetter {
    $letter = new LabelGenerationLetter();
    $letter->service = $this->buildService($shipment);
    $letter->parcel = $this->buildParcel($shipment);
    $letter->sender = $this->buildSender($shipment);
    $letter->addressee = $this->buildAddressee($shipment);
    
    return $letter;
  }
  
  protected function getAdressInfoColissimo(ShipmentInterface $shipment) {
    $adressColissimo = $shipment->get('hbkcolissimochrono_pickup')->value;
    if ($adressColissimo)
      return JSON::decode($adressColissimo);
    else
      throw new \ErrorException(" The colissimo address is not defined ");
  }
  
  /**
   * Build addressees.
   *
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationAddressee
   *         Label generation addressee.
   *        
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   * @throws \Drupal\commerce_shipping_label\ShippingLabelGenerationException
   */
  protected function buildAddressee(ShipmentInterface $shipment): LabelGenerationAddressee {
    $addressee = new LabelGenerationAddressee();
    $ShippingProfile = $shipment->getShippingProfile();
    if (!$this->isRelay($shipment)) {
      $address = $ShippingProfile->get('address')->first();
      assert($address instanceof AddressInterface);
      $addressee->address = $this->buildAddress($address);
    }
    else {
      $billingProfile = $shipment->getOrder()->getBillingProfile();
      if (!$billingProfile || $billingProfile->get('address')->isEmpty()) {
        // phpcs:ignore
        throw new \Exception($this->t('A billing profile is mandatory to generate a colissimo label with realy pickup.'));
      }
      $address = $billingProfile->get('address')->first();
      assert($address instanceof AddressInterface);
      $addressee->address = $this->buildAddress($address);
    }
    $addressee->address->email = $shipment->getOrder()->getEmail();
    
    // $setting = $this->settings->get();
    // if (!$setting->isPhoneFromBillingProfile()) {
    // $phoneNumber = new
    // MobilePhoneNumber($customerProfile->getMobilePhone($setting));
    // $addressee->address->mobileNumber = $phoneNumber->format();
    // return $addressee;
    // }
    // $billingProfile = $shipment->getOrder()->getBillingProfile();
    // if (!$billingProfile) {
    // return $addressee;
    // }
    // $phoneNumber = new
    // MobilePhoneNumber($billingProfile->get($setting->getCustomerProfilePhoneField())->getString());
    // $addressee->address->mobileNumber = $phoneNumber->format();
    return $addressee;
  }
  
  /**
   * Build service.
   *
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationService
   *         Label generation service.
   */
  protected function buildService(ShipmentInterface $shipment): LabelGenerationService {
    $service = new LabelGenerationService();
    $method = $shipment->getShippingMethod()->getPlugin();
    assert($method instanceof HbkShippingInterface);
    $service->productCode = $this->getProductCode($method, $shipment);
    $depositDate = new \DateTime();
    $depositDate = $depositDate->modify('+' . $this->Param->getAveragePreparationDelayInDays() . ' days');
    $service->depositDate = $depositDate;
    $service->commercialName = $this->Param->getCommercialName();
    return $service;
  }
  
  /**
   * Build parcel.
   *
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationParcel Label
   *         generation parcel.
   */
  protected function buildParcel(ShipmentInterface $shipment): LabelGenerationParcel {
    $parcel = new LabelGenerationParcel();
    $weight = $shipment->getWeight();
    $parcel->weight = $weight ? floatval($weight->convert(WeightUnit::KILOGRAM)->getNumber()) : 0;
    if ($this->isRelay($shipment)) {
      $field_name = "hbkcolissimochrono_pickup";
      $hbkcolissimochrono_pickup_book = Json::decode($shipment->get($field_name)->value);
      $parcel->pickupLocationId = $hbkcolissimochrono_pickup_book['identifiant'];
    }
    return $parcel;
  }
  
  /**
   * Build sender.
   *
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationSender Label
   *         generation sender.
   */
  protected function buildSender(ShipmentInterface $shipment): LabelGenerationSender {
    $sender = new LabelGenerationSender();
    switch ($this->Param->getLabelSenserIdSource()) {
      case 'ORDER_ID':
        $sender->senderParcelRef = $shipment->getOrder()->id();
        break;
      case 'SHIPMENT_ID':
        $sender->senderParcelRef = $shipment->id();
        break;
    }
    
    $store = $shipment->getOrder()->getStore();
    $sender->address = $this->buildAddress($store->getAddress());
    $sender->address->companyName = $store->getName();
    $sender->address->email = $store->getEmail();
    $bilingProdile = $shipment->getOrder()->getBillingProfile();
    // On doit trouver un moyen de regler cela proprement.
    // cette information doit etre dans le shipping.
    // OUps cest pas ICI.
    if ($bilingProdile->hasField('field_telephone')) {
      $sender->address->phoneNumber = $bilingProdile->get('field_telephone')->value;
    }
    return $sender;
  }
  
  /**
   * Build addressee.
   *
   * @param \Drupal\address\AddressInterface $address
   *        Address.
   *        
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationAddress
   *         Label generation addressee.
   */
  protected function buildAddress(AddressInterface $address): LabelGenerationAddress {
    $labelAddress = new LabelGenerationAddress();
    $labelAddress->companyName = $address->getOrganization();
    $labelAddress->firstName = $address->getGivenName();
    $labelAddress->lastName = $address->getFamilyName();
    $labelAddress->line2 = $address->getAddressLine1();
    $labelAddress->line3 = $address->getAddressLine2();
    $labelAddress->city = $address->getLocality();
    $labelAddress->zipCode = $address->getPostalCode();
    $labelAddress->countryCode = $address->getCountryCode();
    $labelAddress->stateOrProvinceCode = $address->getAdministrativeArea();
    return $labelAddress;
  }
  
  /**
   * Is relay.
   *
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return bool Return true if the shipment is a relay.
   */
  protected function isRelay(ShipmentInterface $shipment): bool {
    /**
     *
     * @var \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkShippingBase $method
     */
    $method = $shipment->getShippingMethod()->getPlugin();
    assert($method instanceof HbkShippingInterface);
    return $method->isRelay();
  }
  
  /**
   * Get product code.
   *
   * @param \Drupal\commerce_shipping_colissimo\Plugin\Commerce\ShippingMethod\Colissimo $method
   *        Colissimo method.
   * @param \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
   *        Shipment.
   *        
   * @return string Product code.
   */
  protected function getProductCode(HbkShippingInterface $method, ShipmentInterface $shipment): string {
    switch ($method->getColissimoType()) {
      case ColissimoDefaultSettings::TYPE_RELAY:
        return 'A2P';
      case ColissimoDefaultSettings::TYPE_SIGNATURE:
        return 'DOS';
      case ColissimoDefaultSettings::TYPE_NO_SIGNATURE:
        return 'DOM';
    }
    throw new \UnexpectedValueException('Unknown colissimo type ' . $method->getColissimoType());
  }
  
  /**
   * Build output format.
   *
   * @return \Drupal\commerce_shipping_colissimo\Api\LabelGenerationOutputFormat
   *         Label generation output format.
   */
  protected function buildOutputFormat(): LabelGenerationOutputFormat {
    $format = new LabelGenerationOutputFormat();
    $format->SetOutputPrintingType($this->getOutputPrintintType());
    return $format;
  }
  
  /**
   * Get output printing type.
   *
   * @return string Output printing type.
   */
  protected function getOutputPrintintType(): string {
    $mapping = [
      LabelFormat::PDF => [
        LabelSize::A4 => 'PDF_10x15_300dpi',
        LabelSize::SIZE_10X15 => 'PDF_10x15_300dpi',
        LabelSize::SIZE_10X10 => 'PDF_10x10_300dpi'
      ],
      LabelFormat::ZPL => [
        LabelSize::SIZE_10X15 => 'ZPL_10x15_300dpi',
        LabelSize::SIZE_10X10 => 'ZPL_10x10_300dpi'
      ],
      LabelFormat::DPL => [
        LabelSize::SIZE_10X15 => 'DPL_10x15_300dpi',
        LabelSize::SIZE_10X10 => 'DPL_10x10_300dpi'
      ]
    ];
    $printingType = @$mapping[$this->Param->getFormat()][$this->Param->getSize()];
    if (!$printingType) {
      throw new \UnexpectedValueException('Incoherent label format/size settings.');
    }
    return $printingType;
  }
}