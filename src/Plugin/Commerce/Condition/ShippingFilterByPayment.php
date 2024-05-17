<?php

namespace Drupal\hbkcolissimochrono\Plugin\Commerce\Condition;

use Drupal\commerce\Plugin\Commerce\Condition\ConditionBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\physical\MeasurementType;
use Drupal\physical\Weight;

/**
 * Provides the weight condition for shipments.
 *
 * @CommerceCondition(
 *   id = "hbk_shipping_filterby_payment",
 *   label = @Translation("show if method if Collsimo"),
 *   category = @Translation("Shipment"),
 *   entity_type = "commerce_order",
 * )
 */
class ShippingFilterByPayment extends ConditionBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'payment_gateways' => []
    ] + parent::defaultConfiguration();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['payment_gateways'] = [
      '#type' => 'commerce_entity_select',
      '#title' => $this->t('Payment gateways'),
      '#default_value' => $this->configuration['payment_gateways'],
      '#target_type' => 'commerce_payment_gateway',
      '#hide_single_entity' => FALSE,
      '#multiple' => TRUE,
      '#required' => TRUE
    ];
    
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    
    $values = $form_state->getValue($form['#parents']);
    $this->configuration['payment_gateways'] = $values['payment_gateways'];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function evaluate(EntityInterface $entity) {
    $this->assertEntity($entity);
    // return true;
    
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $entity;
    
    if ($order->get('payment_gateway')->isEmpty()) {
      // The payment gateway is not known yet, the condition cannot pass.
      return FALSE;
    }
    // Avoiding ->target_id to allow the condition to be unit tested,
    // because Prophecy doesn't support magic properties.
    $payment_gateway_item = $order->get('payment_gateway')->first()->getValue();
    $payment_gateway_id = $payment_gateway_item['target_id'];
    // \Stephane888\Debug\debugLog::kintDebugDrupal($payment_gateway_item,
    // 'evaluate__' . $payment_gateway_id, true);
    // $payment_gateway_id2 = $order->getData('payment_gateway_id');
    // \Stephane888\Debug\debugLog::kintDebugDrupal($payment_gateway_item,
    // 'evaluate__getData__' . $payment_gateway_id2, true);
    
    return in_array($payment_gateway_id, $this->configuration['payment_gateways']);
  }
  
}