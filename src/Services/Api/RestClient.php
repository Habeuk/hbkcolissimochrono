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
use Drupal\hbkcolissimochrono\Services\Api\Ressources\MultiPartBody;

/**
 *
 * @author stephane
 *        
 */
class RestClient {
  /**
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected static $pathResolver;
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
      if (str_contains($path, "https://"))
        $url = $path;
      else
        $url = $this->Param->getBaseUrl() . $path;
      $options = [
        RequestOptions::HEADERS => [
          'Content-Type' => 'application/json'
        ]
      ];
      // \Stephane888\Debug\debugLog::$max_depth = 10;
      $payload = $this->normalize($payload);
      // dd($payload);
      $payload['contractNumber'] = $this->Param->getUserLogin();
      $payload['password'] = $this->Param->getPassWord();
      // \Stephane888\Debug\debugLog::symfonyDebug($payload,
      // 'generateLabel_PAYLOAD__', true);
      //
      $options[RequestOptions::BODY] = $this->serializer->serialize($payload, 'json');
      /**
       *
       * @var \Psr\Http\Message\ResponseInterface $httpResponse
       */
      $httpResponse = $this->httpClient->request('POST', $url, $options);
      $bodyContens = [
        'raw_body' => $httpResponse->getBody()->getContents()
      ];
      $data = [
        'body' => $bodyContens,
        'httpResponse' => $httpResponse
      ];
      // \Stephane888\Debug\debugLog::symfonyDebug($data, 'RestClient__post',
      // true);
      $MultiPartBody = new MultiPartBody();
      //
      // $datas = $MultiPartBody->getDatas($httpResponse);
      // \Stephane888\Debug\debugLog::kintDebugDrupal($datas, 'getDatas', true);
      //
      // $defaultThemeName = \Drupal::config('system.theme')->get('default');
      // $path_of_module = DRUPAL_ROOT . '/' . self::getPath('theme',
      // $defaultThemeName) . "/logs";
      // \Stephane888\Debug\debugLog::logger($bodyContens, 'raw_body_content',
      // true, 'file', $path_of_module);
      //
      $bodyContens += $MultiPartBody->parseMultiPartBody($bodyContens['raw_body']);
      // \Stephane888\Debug\debugLog::kintDebugDrupal($parseMultiPartBody,
      // 'parseMultiPartBody', true);
      //
      return $bodyContens;
    }
    catch (\GuzzleHttp\Exception\ClientException $e) {
      $debugs = [
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
        'ResponseBody' => $e->getResponse()->getBody()->getContents()
      ];
      \Stephane888\Debug\debugLog::$max_depth = 10;
      \Stephane888\Debug\debugLog::kintDebugDrupal($debugs, 'RestClient__post_error__', true);
      $this->logger->error($e->getMessage());
    }
    catch (\Exception $e) {
      $this->Messenger->addError($e->getMessage());
      \Stephane888\Debug\debugLog::kintDebugDrupal(ExceptionExtractMessage::errorAll($e), 'RestClient__post_error__', true);
      $this->logger->error(ExceptionExtractMessage::errorAllToString($e));
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
  
  public static function getPath($type, $name) {
    if (!self::$pathResolver) {
      self::$pathResolver = \Drupal::service('extension.path.resolver');
    }
    return self::$pathResolver->getPath($type, $name);
  }
}