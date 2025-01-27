<?php

namespace Drupal\hbkcolissimochrono\Services\Api\Ressources;

/**
 * Label generation address.
 */
class LabelGenerateMessage {
  private $id;
  private $type;
  private $messageContent;
  private $replacementValues;
  
  // Getters and setters
  public function getId(): string {
    return $this->id;
  }
  
  public function setId(string $id): self {
    $this->id = $id;
    return $this;
  }
  
  public function getType(): string {
    return $this->type;
  }
  
  public function setType(string $type): self {
    $this->type = $type;
    return $this;
  }
  
  public function getMessageContent(): string {
    return $this->messageContent;
  }
  
  public function setMessageContent(string $messageContent): self {
    $this->messageContent = $messageContent;
    return $this;
  }
  
  public function getReplacementValues(): array {
    return $this->replacementValues;
  }
  
  public function setReplacementValues(array $replacementValues): self {
    $this->replacementValues = $replacementValues;
    return $this;
  }
}