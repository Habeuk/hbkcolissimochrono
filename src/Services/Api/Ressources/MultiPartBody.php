<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerationResponse;

/**
 * Traitement de la reponse.
 */
class MultiPartBody {
  /**
   * Serializer.
   */
  private Serializer $serializer;
  
  function __construct() {
    $encoders = [
      new JsonEncoder()
    ];
    $normalizers = [
      new ArrayDenormalizer(),
      new ObjectNormalizer(),
      new DateTimeNormalizer([
        DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
      ]),
      new PropertyNormalizer(NULL, NULL, new PhpDocExtractor())
    ];
    $this->serializer = new Serializer($normalizers, $encoders);
  }
  
  function getDatas(ResponseInterface $response) {
    // Parser la réponse MIME
    $contentType = $response->getHeader('Content-Type')[0];
    $boundary = explode("=", explode(";", $contentType)[1])[1];
    $parts = explode("--$boundary", $response->getBody());
    
    $metadata = [];
    $pdfContent = '';
    
    foreach ($parts as $part) {
      if (empty($part))
        continue;
      
      // Extraire les en-têtes et le contenu
      list($headers, $content) = explode("\r\n\r\n", $part, 2);
      
      if (strpos($headers, "application/json") !== FALSE) {
        // Récupérer les métadonnées JSON
        $metadata = json_decode($content, TRUE);
      }
      elseif (strpos($headers, "application/pdf") !== FALSE) {
        // Récupérer le contenu du PDF
        $pdfContent = $content;
      }
    }
    
    return [
      'metadata' => $metadata,
      'file_path' => $pdfContent,
      'body' => $response->getBody()->getContents()
    ];
  }
  
  /**
   * Parse multipart body.
   */
  public function parseMultiPartBody(string $body, string $responseClass = 'array') {
    preg_match('/--(.*)\b/', $body, $boundaries);
    $boundary = @$boundaries[0];
    
    if (empty($boundary)) {
      // return $this->parseMonoPartBody($body, $responseClass);
      return [
        'datas' => $body,
        'files' => []
      ];
    }
    
    $messages = array_filter(array_map('trim', explode($boundary, $body)));
    
    $parsedData = NULL;
    $files = [];
    
    foreach ($messages as $message) {
      if ('--' === $message) {
        break;
      }
      
      $headers = [];
      [
        $headerLines,
        $body
      ] = explode("\r\n\r\n", $message, 2);
      
      foreach (explode("\r\n", $headerLines) as $headerLine) {
        [
          $key,
          $value
        ] = preg_split('/:\s+/', $headerLine, 2);
        $headers[strtolower($key)] = $value;
      }
      
      if (str_contains($headers['content-type'], 'application/json')) {
        if ($responseClass) {
          // $parsedData = $this->serializer->deserialize($body, $responseClass,
          // 'json');
          $parsedData = $body;
        }
      }
      else {
        $files[] = $body;
      }
    }
    
    return [
      'datas' => $parsedData,
      'files' => $files
    ];
  }
  
  /**
   * Parse monopart body.
   */
  private function parseMonoPartBody(string $body, $responseClass) {
    if ($responseClass) {
      $parsedData = $this->serializer->deserialize($body, "Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelGenerateMessage::[]", 'json');
      return [
        'datas' => $parsedData,
        'files' => []
      ];
    }
    return [
      'datas' => null,
      'files' => []
    ];
  }
}