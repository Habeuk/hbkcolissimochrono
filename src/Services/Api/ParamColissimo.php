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
  protected string $userLogin;
  protected string $userPassword;
  protected string $size;
  protected string $format;
  protected int $delayInDays;
  protected string $CommercialName;
  protected float $DefaultParcelWeigthInKg = 0.01;
  protected string $LabelSenserIdSource = 'ORDER_ID';
  protected string $environnement = "sandbox";
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
      $this->userLogin = $config['login'];
    else
      $this->Messenger->addError("Paramettre coliShip non definit");
    //
    $this->environnement = $config['app_mode'];
    $this->userPassword = $config["password"] ?? NULL;
    $this->size = $config["size"] ?? NULL;
    $this->format = $config["format"] ?? NULL;
    $this->delayInDays = (int) $config["delais_in_days"] ?? NULL;
    $this->CommercialName = $config['commercial_name'] ?? NULL;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::getBaseUrl()
   */
  public function getBaseUrl(): string {
    /**
     * Url de la production.
     *
     * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#section/Acces-au-Web-Service-SLS/URL-d'acces:
     * @var string $base_url_prod
     */
    $base_url_prod = 'https://ws.colissimo.fr/sls-ws/SlsServiceWSRest/2.0';
    
    /**
     * Url de la sandbox
     *
     * @see https://www.colissimo.fr/doc-colissimo/redoc-sls/fr#section/Environnement-Sandbox-du-Web-Service-SLS/URL-d'acces-a-l'environnement-Sandbox
     * @var string $base_url_sandbox
     */
    $base_url_sandbox = 'https://ws.colissimo.fr/sandbox/api-document';
    
    return $this->runInProduction() ? $base_url_prod : $base_url_sandbox;
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
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Services\Api\ParamInterface::runInProduction()
   */
  public function runInProduction(): bool {
    return $this->environnement == 'prod' ? true : false;
  }
}
