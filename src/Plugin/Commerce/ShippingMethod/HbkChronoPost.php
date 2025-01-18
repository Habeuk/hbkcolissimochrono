<?php

namespace Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings;
use Drupal\commerce_price\Price;
use Drupal\commerce_shipping\ShippingRate;
use Drupal\physical\Weight;
use Drupal\physical\WeightUnit;

/**
 * Provides the colissimo shipping method.
 *
 * @CommerceShippingMethod(
 *   id = "hbkchronopost",
 *   label = @Translation("ChronoPost by HBK"),
 * )
 */
class HbkChronoPost extends HbkColissimo {
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['colissimo_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Chrono shipping type'),
      '#default_value' => $this->configuration['colissimo_type'],
      '#options' => [
        ColissimoDefaultSettings::TYPE_RELAY => $this->t(' Chrono relay '),
        ColissimoDefaultSettings::TYPE_SIGNATURE => $this->t(' Chrono with signature '),
        ColissimoDefaultSettings::TYPE_NO_SIGNATURE => $this->t(' Chrono without signature ')
      ],
      '#required' => TRUE,
      '#weight' => 0
    ];
    return $form;
  }
  
  public function getChronoType(): string {
    return $this->configuration['colissimo_type'];
  }
  
}