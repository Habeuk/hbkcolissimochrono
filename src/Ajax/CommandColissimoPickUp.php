<?php

namespace Drupal\hbkcolissimochrono\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Asset\AttachedAssets;

/**
 * Class ExtendCommand.
 */
class CommandColissimoPickUp implements CommandInterface {
  /**
   *
   * @var string
   */
  protected $token;
  /**
   * An optional list of arguments to pass to the method.
   *
   * @var array
   */
  protected $arguments;
  
  /**
   * Constructs an InvokeCommand object.
   *
   * @param string $selector
   *        A jQuery selector.
   * @param string $method
   *        The name of a jQuery method to invoke.
   * @param array $arguments
   *        An optional array of arguments to pass to the method.
   */
  public function __construct($token, array $arguments = []) {
    $this->token = $token;
    $this->arguments = $arguments;
  }
  
  public function render() {
    return [
      'command' => 'CommandColissimoPickUp',
      'token' => $this->token,
      'arguments' => $this->arguments
    ];
  }
  
}