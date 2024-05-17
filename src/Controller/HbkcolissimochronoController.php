<?php
declare(strict_types = 1);

namespace Drupal\hbkcolissimochrono\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hbkcolissimochrono\Services\Api\Ressources\SLS;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for hbk Colissimo &amp; Chronopost routes.
 */
final class HbkcolissimochronoController extends ControllerBase {
  /**
   *
   * @var \Drupal\hbkcolissimochrono\Services\Api\Ressources\SLS
   */
  protected SLS $SLS;
  
  function __construct(SLS $SLS) {
    $this->SLS = $SLS;
  }
  
  static function create(ContainerInterface $container) {
    return new static($container->get('hbkcolissimochrono.api.sls'));
  }
  
  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!')
    ];
    return $build;
  }
  
}
