<?php

namespace Drupal\hbkcolissimochrono\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Component\Serialization\Json;

/**
 * Plugin implementation of the 'text_default' formatter.
 *
 * @FieldFormatter(
 *   id = "hbkcoliickup_formatter",
 *   label = @Translation("Hbkcolissimochrono Pickup Formatter"),
 *   field_types = {
 *     "text_long"
 *   }
 * )
 */
class HbkcolissimochronoPickupFormatter extends FormatterBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    
    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'processed_text',
        '#text' => $this->jsonFormatter($item->value),
        '#format' => 'full_html',
        '#langcode' => $item->getLangcode()
      ];
    }
    $elements['#attached']['library'][] = 'hbkcolissimochrono/hbkcolissimochrono';
    return $elements;
  }
  
  protected function jsonFormatter(string $value) {
    $html = '';
    if (!empty($value)) {
      $html .= '<div class="hbkcolissimochrono-pickup-edit my-3 px-4 py-3 border bg-warning-subtle border-warning"><div class="pickup-html">';
      $datas = Json::decode($value);
      foreach ($datas as $key => $item) {
        $html .= $key . ' : ' . $item . '<br>';
      }
      $html .= '</div></div>';
    }
    return $html;
  }
  
}