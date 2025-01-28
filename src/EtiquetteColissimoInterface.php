<?php

declare(strict_types=1);

namespace Drupal\hbkcolissimochrono;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an etiquette colissimo entity type.
 */
interface EtiquetteColissimoInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
