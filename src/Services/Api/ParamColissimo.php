<?php

namespace Drupal\hbkcolissimochrono\Services\Api;

use Drupal\Core\Messenger\Messenger;
use Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings;

/**
 *
 * @author stephane
 *        
 */
class ParamColissimo implements ParamInterface {
  protected string $baseUrl = 'https://ws.colissimo.fr/sls-ws/SlsServiceWSRest/2.0';
  protected string $userLogin;
  protected string $userPassword;
  protected string $size;
  protected string $format;
  protected int $delayInDays;
  protected string $CommercialName;
  protected float $DefaultParcelWeigthInKg = 0.01;
  protected string $LabelSenserIdSource = 'ORDER_ID';
  protected ColissimoDefaultSettings $ConfigFactory;
  protected Messenger $Messenger;

  public function __construct(ColissimoDefaultSettings $ConfigFactory, Messenger $Messenger) {
    $this->ConfigFactory = $ConfigFactory;
    $this->Messenger = $Messenger;
    $this->setDefaultConfiguration();
  }

  /**
   * Definit le configuration par defaut.
   */
  protected function setDefaultConfiguration() {
    $config = $this->ConfigFactory->getSettings();
    if (isset($config['login']))
      $this->userLogin = $config->get('login');
    else
      $this->Messenger->addError("Paramettre coliShip non definit");

    $this->userPassword = $config["password"] ?? NULL;
    $this->size = $config["size"] ?? NULL;
    $this->format = $config["format"] ?? NULL;
    $this->delayInDays = $config["delais_in_days"] ?? NULL;
    $this->CommercialName = $config['commercial_name'] ?? NULL;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getBaseUrl()
   */
  public function getBaseUrl(): string {
    return $this->baseUrl;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getUserLogin()
   */
  public function getUserLogin(): string {
    return $this->userLogin;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getPassWord()
   */
  public function getPassWord(): string {
    return $this->userPassword;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getSize()
   */
  public function getSize(): string {
    return $this->size;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getFormat()
   */
  public function getFormat(): string {
    return $this->format;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getAveragePreparationDelayInDays()
   */
  public function getAveragePreparationDelayInDays(): int {
    return $this->delayInDays;
  }

  public function getCommercialName(): string {
    return $this->CommercialName;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getDefaultParcelWeigthInKg()
   */
  public function getDefaultParcelWeigthInKg(): float {
    return $this->DefaultParcelWeigthInKg;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getLabelSenserIdSource()
   */
  public function getLabelSenserIdSource(): string {
    return $this->LabelSenserIdSource;
  }
}
