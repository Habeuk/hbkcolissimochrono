<?php

namespace Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodBase;
use Drupal\commerce_price\Price;
use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\PackageTypeManagerInterface;
use Drupal\commerce_shipping\ShippingRate;
use Drupal\commerce_shipping\ShippingService;
use Drupal\Core\Form\FormStateInterface;
use Drupal\state_machine\WorkflowManagerInterface;
use Drupal\hbkcolissimochrono\Services\ColissimoDefaultSettings;
use Drupal\physical\Volume;
use Drupal\physical\VolumeUnit;
use Drupal\physical\Weight;
use Drupal\physical\WeightUnit;

/**
 *
 * @author stephane
 *        
 */
class HbkShippingBase extends ShippingMethodBase implements HbkShippingInterface {
  
  /**
   *
   * @var \Drupal\physical\Weight
   */
  protected $colissimo_weight_max;
  /**
   *
   * @var array
   */
  protected $CurrentFormatPackaging = [];
  
  /**
   * Constructs a new FlatRate object.
   *
   * @param array $configuration
   *        A configuration array containing information about the plugin
   *        instance.
   * @param string $plugin_id
   *        The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *        The plugin implementation definition.
   * @param \Drupal\commerce_shipping\PackageTypeManagerInterface $package_type_manager
   *        The package type manager.
   * @param \Drupal\state_machine\WorkflowManagerInterface $workflow_manager
   *        The workflow manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PackageTypeManagerInterface $package_type_manager, WorkflowManagerInterface $workflow_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $package_type_manager, $workflow_manager);
    
    $this->services['default'] = new ShippingService('default', $this->configuration['rate_label']);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'rate_label' => '',
      'rate_description' => '',
      'rate_amount' => NULL,
      'services' => [
        'default'
      ],
      'colissimo_type' => NULL,
      'colissimo_weights' => [],
      'colissimo_formats' => [
        ColissimoDefaultSettings::FORMAT_STANDARD => [
          'label' => 'standard',
          'amount' => [
            'number' => 0
          ],
          'dimension' => 150, // mm
          'weight' => 400
        ],
        ColissimoDefaultSettings::FORMAT_VOLUMINEUX => [
          'label' => 'Volumineux',
          'amount' => [
            'number' => 6
          ],
          'dimension' => 200, // cm
          'weight' => 900
        ]
      ],
      "combine_products" => true,
      'colissimo_weight_max' => 30000
    ] + parent::defaultConfiguration();
  }
  
  /**
   * Sadly this functions code had to be copy pasted with only few replacements.
   *
   * {@inheritdoc}
   */
  public function buildPaneForm(array $paneForm, FormStateInterface $formState, array &$completeForm) {
    $this->messenger()->addStatus(" Selection d'une methode colissimo ");
  }
  
  public function getColissimoType(): string {
    return $this->configuration['colissimo_type'];
  }
  
  /**
   * Is relay shipping.
   *
   * @return bool True if relay shipping, false otherwise.
   */
  public function isRelay(): bool {
    return $this->getColissimoType() == ColissimoDefaultSettings::TYPE_RELAY;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkShippingInterface::ListWeights()
   */
  public function ListWeights(): array {
    $results = [];
    $colissimo_weight_max = $this->configuration['colissimo_weight_max'];
    $ListWeights = ColissimoDefaultSettings::ListWeights();
    foreach ($ListWeights as $key => $item) {
      if ($key <= $colissimo_weight_max)
        $results[$key] = $item;
    }
    return $results;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkShippingInterface::getMaxWeight()
   */
  public function getMaxWeight(): \Drupal\physical\Weight {
    if (!$this->colissimo_weight_max) {
      $this->colissimo_weight_max = new \Drupal\physical\Weight($this->configuration['colissimo_weight_max'], \Drupal\physical\WeightUnit::GRAM);
    }
    return $this->colissimo_weight_max;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\hbkcolissimochrono\Plugin\Commerce\ShippingMethod\HbkShippingInterface::getShippingPackagings()
   */
  public function getShippingPackagings(ShipmentInterface $shipment): array {
    if (!$this->CurrentFormatPackaging) {
      $colis = [
        ColissimoDefaultSettings::FORMAT_STANDARD => [],
        ColissimoDefaultSettings::FORMAT_VOLUMINEUX => []
      ];
      $packagings = $this->getPackagings();
      $max_weight = $this->getMaxWeight();
      /**
       *
       * @var array $OrderItems
       */
      $OrderItems = $shipment->getOrder()->getItems();
      foreach ($OrderItems as $OrderItem) {
        /**
         *
         * @var \Drupal\commerce_order\Entity\OrderItem $OrderItem
         */
        /**
         *
         * @var \Drupal\commerce_product\Entity\ProductVariation $variation
         */
        $variation = $OrderItem->getPurchasedEntity();
        if ($variation->hasField('dimensions')) {
          $dimensions = $variation->get('dimensions')->getValue();
          // \Stephane888\Debug\debugLog::kintDebugDrupal($dimensions,
          // 'getShippingPackagings', true);
          $dimension = reset($dimensions);
          if (!$dimension)
            throw new \Exception("Les dimantions du produits ( variation id : " . $variation->id() . " ) ne sont pas definit ");
          $dd = (int) round($dimension['length'] * $dimension['width'] * $dimension['height']);
          $unitLetter = 'cl';
          switch ($dimension['unit']) {
            case 'mm':
              $unitLetter = VolumeUnit::MILLILITER;
              break;
            case 'cm':
              $unitLetter = VolumeUnit::CENTILITER;
              break;
            case 'm':
              $unitLetter = VolumeUnit::LITER;
              break;
          }
          $VolumeProductVariation = new Volume($dd, $unitLetter);
          // On ne souhaite pas combiner les produits.
          if (!$this->configuration['combine_products']) {
            foreach ($packagings as $format => $packaging) {
              // si le volume du produit est plus petit que l'emballage
              $status = $VolumeProductVariation->compareTo($packaging['volume_max']);
              if ($status === -1 || $status === 0) {
                $productVariationWeight = $this->getWeightFromProductVariation($variation);
                if (!$max_weight->compareTo($productVariationWeight)) {
                  // La validation du poid et du volume par Variation de produit
                  // doivent se faire ailleurs dans une condition.
                  // On doit s'assurer que aucun produit n'excede les limites
                  // autorisé.
                  continue;
                }
                for ($i = 0; $i < $OrderItem->getQuantity(); $i++) {
                  $colis[$format][] = [
                    'weight' => $productVariationWeight->toArray(),
                    'produtVariations' => [
                      $variation->id()
                    ]
                  ];
                }
                break;
              }
            }
          }
          // On souhaite faire un merge.
          else {
            foreach ($packagings as $format => $packaging) {
              // Si le volume du produit est plus petit que l'emballage
              $status = $VolumeProductVariation->compareTo($packaging['volume_max']);
              if ($status === -1 || $status === 0) {
                $productVariationWeight = $this->getWeightFromProductVariation($variation);
                for ($i = 0; $i < $OrderItem->getQuantity(); $i++) {
                  if (empty($colis[$format])) {
                    $colis[$format][] = [
                      'weight' => $productVariationWeight->toArray(),
                      'volume' => $VolumeProductVariation->toArray(),
                      'produtVariations' => [
                        $variation->id()
                      ]
                    ];
                  }
                  else {
                    $add_in_old_packaging = false;
                    // On verifie s'il peut s'inserrer dans un colis exitant.
                    foreach ($colis[$format] as &$col) {
                      $volume_used = new Volume($col['volume']['number'], $col['volume']['unit']);
                      $volume_used->add($VolumeProductVariation);
                      $status = $packaging['volume_max']->compareTo($volume_used);
                      // Si le volume de l'emballage est plus grand ou s'il est
                      // egal à la somme du volume produit.
                      if ($status === 1 || $status === 0) {
                        $weight_used = new Weight($col['weight']['number'], $col['weight']['unit']);
                        $weight_used->add($productVariationWeight);
                        // Si le poid max est > au poid des produits.
                        if ($max_weight->compareTo($weight_used) === 1) {
                          $col['weight'] = $weight_used->toArray();
                          $col['volume'] = $volume_used->toArray();
                          $col['produtVariations'][] = $variation->id();
                          $add_in_old_packaging = true;
                        }
                        // S'il nya plus assez d'espace on ajoute cela dans un
                        // autre colis.
                      }
                    }
                    // Si on a pas pu ajouter dans un colis, on cree un nouveau
                    // colis.
                    if (!$add_in_old_packaging) {
                      $colis[$format][] = [
                        'weight' => $productVariationWeight->toArray(),
                        'volume' => $VolumeProductVariation->toArray(),
                        'produtVariations' => [
                          $variation->id()
                        ]
                      ];
                    }
                  }
                }
                break;
              }
            }
          }
        }
        else {
          // Il faudra stoppé l'éxecution.
          $this->messenger()->addError(" Vous dévez définir les dimensions sur vos produits ");
        }
      }
      $this->CurrentFormatPackaging = $colis;
    }
    return $this->CurrentFormatPackaging;
  }
  
  /**
   *
   * @param \Drupal\commerce_product\Entity\ProductVariation $variation
   * @return \Drupal\physical\Weight
   */
  protected function getWeightFromProductVariation(\Drupal\commerce_product\Entity\ProductVariation $variation) {
    $weight = $variation->get('weight')->getValue();
    $weight = reset($weight);
    return new Weight($weight['number'], $weight['unit']);
  }
  
  /**
   * Retoune les emballages pour le colis
   */
  protected function getPackagings() {
    $packagings = [];
    foreach ($this->configuration['colissimo_formats'] as $format => $item) {
      $d = ($item['dimension'] / 3) - 2;
      // Ce ci permet d'obtenir le volume max disponible.
      $dd = (int) round($d * $d * $d);
      $packagings[$format]['volume_max'] = new Volume($dd, VolumeUnit::CENTILITER);
    }
    return $packagings;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['colissimo_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Colissimo shipping type'),
      '#default_value' => $this->configuration['colissimo_type'],
      '#options' => [
        ColissimoDefaultSettings::TYPE_RELAY => $this->t(' Colissimo relay '),
        ColissimoDefaultSettings::TYPE_SIGNATURE => $this->t(' Colissimo with signature '),
        ColissimoDefaultSettings::TYPE_NO_SIGNATURE => $this->t(' Colissimo without signature ')
      ],
      '#required' => TRUE,
      '#weight' => 0
    ];
    $form['rate_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rate label'),
      '#description' => $this->t('Shown to customers when selecting the rate.'),
      '#default_value' => $this->configuration['rate_label'],
      '#required' => TRUE
    ];
    $form['rate_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rate description'),
      '#description' => $this->t('Provides additional details about the rate to the customer.'),
      '#default_value' => $this->configuration['rate_description']
    ];
    $form['colissimo_weights'] = [
      '#type' => 'details',
      '#title' => $this->t('Define price by weight'),
      '#open' => false,
      '#tree' => true
    ];
    $listsWeight = !empty($this->configuration['colissimo_weights']) ? $this->configuration['colissimo_weights'] : $this->ListWeights();
    // dump($this->configuration['colissimo_weights'], $this->ListWeights());
    foreach ($listsWeight as $key => $item) {
      $amount = $item['rate_amount'];
      // A bug in the plugin_select form element causes $amount to be
      // incomplete.
      if (isset($amount) && !isset($amount['number'], $amount['currency_code'])) {
        $amount = NULL;
      }
      $form['colissimo_weights'][$key] = [
        '#type' => 'details',
        '#title' => isset($item['label']) ? $item['label'] : $item['weight'] . 'g',
        '#open' => false
      ];
      $form['colissimo_weights'][$key]['rate_amount'] = [
        '#type' => 'commerce_price',
        '#title' => 'Price',
        '#default_value' => $amount
      ];
      $form['colissimo_weights'][$key]['weight'] = [
        '#type' => 'textfield',
        '#title' => 'Weight',
        '#default_value' => $item['weight'],
        '#required' => true
      ];
      $form['colissimo_weights'][$key]['status'] = [
        '#type' => 'checkbox',
        '#title' => 'Status',
        '#default_value' => isset($item['status']) ? $item['status'] : 1
      ];
    }
    //
    $form['colissimo_formats'] = [
      '#type' => 'details',
      '#title' => $this->t('Definition des formats'),
      '#open' => false,
      '#tree' => true
    ];
    $colissimo_formats = $this->configuration['colissimo_formats'];
    // dump($colissimo_formats);
    foreach ($colissimo_formats as $key => $item) {
      $form['colissimo_formats'][$key] = [
        '#type' => 'details',
        '#title' => $item['label'],
        '#open' => false
      ];
      if (empty($item['amount']['currency_code'])) {
        $item['amount']['currency_code'] = 'EUR'; // il faudra le moyen de
                                                  // recuperer la
                                                  // device
                                                  // par defaut.
      }
      $form['colissimo_formats'][$key]['amount'] = [
        '#type' => 'commerce_price',
        '#title' => 'Price',
        '#default_value' => $item['amount']
      ];
      $form['colissimo_formats'][$key]['dimension'] = [
        '#type' => 'number',
        '#title' => 'dimension',
        '#default_value' => $item['dimension'],
        '#suffix' => $this->t("Centimeter(s)")
      ];
    }
    // On permet de sucharger la valeur max du poid.
    $form['colissimo_weight_max'] = [
      '#type' => 'number',
      '#title' => 'Poid max',
      '#default_value' => $this->configuration['colissimo_weight_max'],
      '#required' => true
    ];
    $form['combine_products'] = [
      '#type' => 'checkbox',
      '#title' => "Combiner les produits afin de reduire le cout",
      '#default_value' => $this->configuration['combine_products']
    ];
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodInterface::calculateRates()
   */
  public function calculateRates(ShipmentInterface $shipment) {
    return [];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['colissimo_type'] = $values['colissimo_type'];
      $this->configuration['colissimo_weights'] = $values['colissimo_weights'];
      $this->configuration['colissimo_weight_max'] = $values['colissimo_weight_max'];
      $this->configuration['colissimo_formats'] = $values['colissimo_formats'];
      //
      $this->configuration['rate_label'] = $values['rate_label'];
      $this->configuration['rate_description'] = $values['rate_description'];
    }
  }
  
}