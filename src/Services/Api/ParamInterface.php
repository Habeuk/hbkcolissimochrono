<?php

namespace Drupal\hbkcolissimochrono\Services\Api;

/**
 *
 * @author stephane
 *        
 */
interface ParamInterface {
  
  /**
   * L'url de base pour colissimo ou chronoPost.
   *
   * @return string
   */
  public function getBaseUrl(): string;
  
  /**
   * Le login de l'utilisateur.
   *
   * @return string
   */
  public function getUserLogin(): string;
  
  /**
   * Le password
   *
   * @return string
   */
  public function getPassWord(): string;
  
  /**
   * Retourne le format d'impression PDF, ZPL, DPL
   *
   * @return string
   */
  public function getFormat(): string;
  
  /**
   * Retourne la taille d'impression.
   *
   * @return string
   */
  public function getSize(): string;
  
  /**
   *
   * @return int
   */
  public function getAveragePreparationDelayInDays(): int;
  
  /**
   *
   * @return string
   */
  public function getCommercialName(): string;
  
  /**
   *
   * @return float
   */
  public function getDefaultParcelWeigthInKg(): float;
  
  /**
   *
   * @return string
   */
  public function getLabelSenserIdSource(): string;
  
}