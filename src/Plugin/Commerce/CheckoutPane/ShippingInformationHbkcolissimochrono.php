<?php

namespace Drupal\hbkcolissimochrono\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_shipping\Plugin\Commerce\CheckoutPane\ShippingInformation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hbkcolissimochrono\AjaxFormDefault;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Component\Serialization\Json;
use Drupal\hbkcolissimochrono\HbkcolissimochronoAjax;

/**
 * Overwrite the shipping pane.
 *
 * Adds ajax refresh on shipping method change,
 * and reset profile data if a non-relay method is selected.
 */
class ShippingInformationHbkcolissimochrono extends ShippingInformation {
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form = parent::buildPaneForm($pane_form, $form_state, $complete_form);
    $pane_form['#attached']['library'][] = 'hbkcolissimochrono/hbkcolissimochrono';
    $pane_form['#attached']['drupalSettings']['stripebyhabeuk'] = [];
    $pane_form['hbkcolissimochrono_map_pickup'] = [
      '#theme' => 'hbkcolissimochrono_pickup',
      '#settings' => [],
      '#address' => [],
      '#attributes' => [
        'class' => [
          'hbkcolissimochrono_pickup'
        ]
      ]
    ];
    $pane_form['hbkcolissimochrono_pickup'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'id' => 'hbkcolissimochrono_pickup-container'
      ]
    ];
    if (!empty($pane_form['shipments'][0]['#shipment'])) {
      /**
       *
       * @var \Drupal\commerce_shipping\Entity\Shipment $commerce_shipment
       */
      $commerce_shipment = $pane_form['shipments'][0]['#shipment'];
      
      /**
       *
       * @var \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkColissimo $shipping_method
       */
      $shipping_method = $commerce_shipment->getShippingMethod()->getPlugin();
      if ($shipping_method instanceof \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkShippingInterface && $shipping_method->isRelay()) {
        // On effectue la demande afin d'accelerer la demande qui se ferra via
        // la selection des methode ajax.
        /**
         *
         * @var \Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings $default_settings
         */
        $default_settings = \Drupal::service('hbkcolissimochrono.default_settings');
        $default_settings->getWidgetAuthenticationToken();
        
        // On definie un champs textarea caché mais qui doit contenir les
        // informations du pickup si necessaire.
        $hbkcolissimochrono_pickup_book = $this->order->getData('hbkcolissimochrono_pickup_book');
        $pane_form['hbkcolissimochrono_pickup']['hbkcolissimochrono_pickup_book'] = [
          '#type' => 'hidden',
          '#attributes' => [
            'class' => [
              'hbkcolissimochrono-pickup-book-edit'
            ]
          ],
          '#default_value' => $hbkcolissimochrono_pickup_book,
          '#element_validate' => [
            [
              self::class,
              'hbkcolissimochrono_pickup_book_validate'
            ]
          ]
        ];
        // https://www.colissimo.entreprise.laposte.fr/outils-et-services/kit-de-communication#elements-graphiques
        $pane_form['hbkcolissimochrono_pickup']['hbkcolissimochrono_pickup_edit'] = [
          '#type' => 'button',
          '#name' => 'open-popup',
          '#prefix' => '<div class="hbkcolissimochrono-pickup-edit my-3"><div class="pickup-html">' . $this->buildPointHtml($hbkcolissimochrono_pickup_book) . '</div>',
          '#suffix' => '</div>',
          '#attributes' => [
            'class' => [
              'btn',
              'hbkcolissimochrono-pickup-button'
            ]
          ],
          '#value' => 'Edit pickup',
          '#ajax' => [
            'callback' => [
              self::class,
              'ajaxOpenModal'
            ]
          ]
        ];
      }
      else {
        // On vide les données dans le cas contraire.
        $pane_form['hbkcolissimochrono_pickup']['hbkcolissimochrono_pickup_book'] = [
          '#type' => 'hidden',
          '#attributes' => [
            'class' => [
              'hbkcolissimochrono-pickup-book-edit'
            ]
          ],
          '#default_value' => ''
        ];
      }
    }
    /**
     * Suite à un bug qui empeche la MAJ des moyens de livraison, on ajoute un
     * validateur.
     */
    $pane_form['#element_validate'] = [
      [
        self::class,
        'ShippingPaymentValidate'
      ]
    ];
    return $pane_form;
  }
  
  /**
   * Afin de palier à ce bug :
   * https://www.drupal.org/project/commerce_shipping/issues/3226851
   * Le validateur permet de charger la methode de Shipping et de verifier si
   * elle
   * est compatible avec la methode de paiement.
   *
   * @param array $element
   * @param FormStateInterface $form_state
   */
  static public function ShippingPaymentValidate(&$element, FormStateInterface $form_state, $form) {
    // Get Shipments
    if (!empty($element['shipments'][0]['#shipment'])) {
      /**
       *
       * @var \Drupal\commerce_shipping\Entity\Shipment $shipment
       */
      $shipment = $element['shipments'][0]['#shipment'];
      /**
       *
       * @var \Drupal\commerce_shipping\Entity\ShippingMethod $commerce_shipping_method
       */
      $commerce_shipping_method = $shipment->getShippingMethod();
      $commerce_shipping_method->applies($shipment);
    }
    // dd($element);
  }
  
  static public function hbkcolissimochrono_pickup_book_validate(&$element, FormStateInterface $form_state, $form) {
    $button = $form_state->getTriggeringElement();
    if ($button['#type'] == 'submit' && $button['#name'] == 'op' && empty($element['#value'])) {
      $form_state->setError($element, "Vous devez definir une adresse colissimo");
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildPaneSummary() {
    $summary = parent::buildPaneSummary();
    if ($this->isVisible()) {
      $hbkcolissimochrono_pickup_book = $this->order->getData('hbkcolissimochrono_pickup_book');
      if ($hbkcolissimochrono_pickup_book) {
        $summary['hbkcolissimochrono_pickup_edit'] = [
          '#type' => "markup",
          "#markup" => '<div class="hbkcolissimochrono-pickup-edit my-3"><div class="pickup-html">' . $this->buildPointHtml($hbkcolissimochrono_pickup_book) . '</div>'
        ];
        $summary['#attached']['library'][] = 'hbkcolissimochrono/hbkcolissimochrono';
      }
    }
    return $summary;
  }
  
  protected function buildPointHtml($hbkcolissimochrono_pickup_book) {
    $string = '';
    if (!empty($hbkcolissimochrono_pickup_book)) {
      $hbkcolissimochrono_pickup_book = Json::decode($hbkcolissimochrono_pickup_book);
      if (!empty($hbkcolissimochrono_pickup_book['nom']))
        $string .= $hbkcolissimochrono_pickup_book['nom'] . '<br>';
      if (!empty($hbkcolissimochrono_pickup_book['adresse1']))
        $string .= $hbkcolissimochrono_pickup_book['adresse1'] . '<br>';
      if (!empty($hbkcolissimochrono_pickup_book['adresse2']))
        $string .= $hbkcolissimochrono_pickup_book['adresse2'] . '<br>';
      if (!empty($hbkcolissimochrono_pickup_book['codePostal']))
        $string .= $hbkcolissimochrono_pickup_book['codePostal'] . '<br>';
    }
    if (empty($string))
      return "Selectionner une relais";
    return $string;
  }
  
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    // dd($form_state->getValues());
    $hbkcolissimochrono_pickup_book = $form_state->getValue([
      'shipping_information',
      "hbkcolissimochrono_pickup",
      'hbkcolissimochrono_pickup_book'
    ]);
    // Save colisomo data
    if ($this->order) {
      $this->order->setData('hbkcolissimochrono_pickup_book', $hbkcolissimochrono_pickup_book);
      $this->order->save();
    }
    parent::submitPaneForm($pane_form, $form_state, $complete_form);
  }
  
  public static function ajaxOpenModal(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    // add custom js.
    HbkcolissimochronoAjax::addCommandColissimoPickUp($response, $form, $form_state);
    return $response;
  }
  
  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public static function ajaxRefreshForm(array $form, FormStateInterface $form_state) {
    // \Drupal::messenger()->addStatus("Run ajaxRefreshForm", true);
    $response = AjaxFormDefault::ajaxRefreshForm($form, $form_state);
    // add custom js.
    HbkcolissimochronoAjax::addCommandColissimoPickUp($response, $form, $form_state);
    return $response;
  }
  
  /**
   *
   * @param FormStateInterface $form_state
   * @return \Drupal\commerce_order\Entity\Order
   */
  static protected function getOrderFromFrom(FormStateInterface $form_state) {
    if (!self::$order) {
      /**
       *
       * @var \Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\MultistepDefault $getFormObject
       */
      $getFormObject = $form_state->getFormObject();
      /**
       *
       * @var \Drupal\commerce_order\Entity\Order $order
       */
      self::$order = $getFormObject->getOrder();
    }
    return self::$order;
  }
  
}