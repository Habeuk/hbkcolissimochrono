<?php

namespace Drupal\hbkcolissimochrono\Services\Api;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use GuzzleHttp\RequestOptions;
use Stephane888\Debug\ExceptionExtractMessage;
use Drupal\Core\Messenger\Messenger;

/**
 *
 * @author stephane
 *        
 */
class RestClient {
  /**
   * HTTP client.
   */
  private ClientInterface $httpClient;
  /**
   * LoggerChannelInterface.
   */
  private LoggerChannelInterface $logger;
  /**
   * Serializer.
   */
  private Serializer $serializer;
  /**
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\ParamInterface
   */
  private ParamInterface $Param;
  
  /**
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  private Messenger $Messenger;
  
  /**
   * Constructor.
   */
  public function __construct(ClientInterface $httpClient, LoggerChannelInterface $logger, ParamInterface $Param, Messenger $Messenger) {
    $this->httpClient = $httpClient;
    $this->logger = $logger;
    $this->serializer = new Serializer([
      new ArrayDenormalizer(),
      new DateTimeNormalizer([
        DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
      ]),
      new PropertyNormalizer(NULL, NULL, new PhpDocExtractor())
    ], [
      new JsonEncoder()
    ]);
    $this->Param = $Param;
    $this->Messenger = $Messenger;
  }
  
  /**
   *
   * @param string $path
   * @param mixed $payload
   */
  public function post($path, $payload) {
    try {
      $url = $this->Param->getBaseUrl() . $path;
      $options = [
        RequestOptions::HEADERS => [
          'Content-Type' => 'application/json'
        ]
      ];
      $payload = $this->normalize($payload);
      $payload['contractNumber'] = $this->Param->getUserLogin();
      $payload['password'] = $this->Param->getPassWord();
      //
      $options[RequestOptions::BODY] = $this->serializer->serialize($payload, 'json');
      $httpResponse = $this->httpClient->request('POST', $url, $options);
      return $httpResponse->getBody()->getContents();
    }
    catch (\Exception $e) {
      $this->Messenger->addError($e->getMessage());
      $this->logger->error(ExceptionExtractMessage::errorAll($e));
    }
  }
  
  /**
   * Normalize.
   */
  private function normalize(?object $object): array {
    if (!$object) {
      return [];
    }
    return $this->serializer->normalize($object);
  }
  
}