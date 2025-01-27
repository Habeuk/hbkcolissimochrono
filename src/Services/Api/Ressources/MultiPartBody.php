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
use Drupal\Component\Serialization\Json;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileExists;
use Drupal\file\Entity\File;

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
          $parsedData = Json::decode($body);
        }
      }
      else {
        $files[] = $body;
      }
    }
    $this->savefiles($files);
    return [
      'datas' => $parsedData,
      'files' => $files
    ];
  }
  
  /**
   *
   * @param array $files
   */
  private function savefiles(array $files) {
    $destination = "public://colissimo_files/";
    /**
     *
     * @var \Drupal\Core\File\FileSystem $filesystem
     */
    $filesystem = \Drupal::service('file_system');
    // Check the directory exists before writing data to it.
    if ($filesystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS))
      foreach ($files as $data) {
        $filename = "file-" . rand(10, 2000) . ".pdf";
        $newUri = $filesystem->saveData($data, $destination . $filename, FileExists::Replace);
        $file = File::create([
          'uri' => $newUri,
          'filename' => $filename
        ]);
        $file->setPermanent();
        $file->save();
      }
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