<?php

namespace Drupal\hbkcolissimochrono\Services;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\RequestOptions;

/**
 * --
 *
 * @author stephane
 *        
 */
class ColissimoDefaultSettings {
  const TYPE_RELAY = 'relay';
  const TYPE_SIGNATURE = 'signature';
  const TYPE_NO_SIGNATURE = 'no_signature';
  //
  const FORMAT_STANDARD = 'standard';
  const FORMAT_VOLUMINEUX = 'volumineux';
  
  public function getSettings() {
    return \Drupal::config("hbkcolissimochrono.settings")->getRawData();
  }
  
  /**
   * // cache.backend.apcu
   * Set widget js url.
   */
  public function getWidgetAuthenticationToken() {
    /**
     *
     * @var \Drupal\Core\Cache\ApcuBackend $cacheApcu
     */
    $cacheApcu = \Drupal::service("cache.backend.database")->get('hbkcolissimochrono_widget_api_cache');
    $token = $cacheApcu->get('token');
    if ($token) {
      return $token->data;
    }
    try {
      $config = $this->getSettings();
      $options = [
        RequestOptions::HEADERS => [
          'Content-Type' => 'application/json',
          'Accept' => 'application/json'
        ]
      ];
      /**
       *
       * @var \GuzzleHttp\Client $httpClient
       */
      $httpClient = \Drupal::httpClient();
      $payload = [
        "login" => $config['login'] ?? "",
        "password" => $config['password'] ?? ""
      ];
      $options[RequestOptions::BODY] = json_encode($payload);
      /**
       *
       * @var \Psr\Http\Message\ResponseInterface $response
       */
      $response = $httpClient->post("https://ws.colissimo.fr/widget-colissimo/rest/authenticate.rest", $options);
      $data = Json::decode($response->getBody()->getContents());
      if (!empty($data['token'])) {
        $cacheApcu->set('token', $data['token'], time() + 15 * 60);
        return $data['token'];
      }
      else {
        \Drupal::messenger()->addWarning("Erreur de formatage sur colisship.");
        $error = !empty($data['erreur']) ? $data['erreur'] : '';
        \Drupal::logger('hbkcolissimochrono')->error("Erreur de formatage sur colisship : " . $error);
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('hbkcolissimochrono')->error("Erreur d'authentification sur colisship.");
      \Drupal::messenger()->addWarning("Erreur d'authentification sur colisship.");
    }
    return false;
  }
  
  /**
   * On pourrait mettre cela dans la configuration ainsi on pourrait facilement
   * ajuster.
   */
  static function ListWeights() {
    $query = \Drupal::entityTypeManager()->getStorage('commerce_currency')->getQuery();
    $query->accessCheck(TRUE);
    $query->range(0, 1);
    $ids = $query->execute();
    $currency_code = reset($ids);
    if (!$currency_code)
      $currency_code = 'EUR';
    return [
      [
        'weight' => 250,
        'label' => '250g',
        'rate_amount' => [
          'number' => 4.99,
          'currency_code' => $currency_code
        ] // les prix sont à titre indicatif.
      ],
      [
        'weight' => 500,
        'label' => '500g',
        'rate_amount' => [
          'number' => 6.99,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 750,
        'label' => '750g',
        'rate_amount' => [
          'number' => 8.10,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 1000,
        'label' => '1kg',
        'rate_amount' => [
          'number' => 8.80,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 2000,
        'label' => '2kg',
        'rate_amount' => [
          'number' => 10.15,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 5000,
        'label' => '5kg',
        'rate_amount' => [
          'number' => 15.60,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 10000,
        'label' => '10kg',
        'rate_amount' => [
          'number' => 22.70,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 15000,
        'label' => '15kg',
        'rate_amount' => [
          'number' => 28.70,
          'currency_code' => $currency_code
        ]
      ],
      [
        'weight' => 30000,
        'label' => '30kg',
        'rate_amount' => [
          'number' => 35.55,
          'currency_code' => $currency_code
        ]
      ]
    ];
  }
  
}
