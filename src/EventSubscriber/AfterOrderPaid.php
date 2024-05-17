<?php

namespace Drupal\stripebyhabeuk\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_order\Event\OrderEvent;
use Drupal\commerce_order\Event\OrderEvents;

/**
 *
 * @author stephane
 *        
 */
class AfterOrderPaid implements EventSubscriberInterface {
  
  /**
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;
  
  /**
   * --
   */
  function ORDER_PAID(OrderEvent $event) {
    $this->order = $event->getOrder();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      OrderEvents::ORDER_PAID => [
        'ORDER_PAID'
      ]
    ];
  }
  
}