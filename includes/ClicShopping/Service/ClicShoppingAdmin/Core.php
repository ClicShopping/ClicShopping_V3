<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\MessageStack as MessageStackClassAdmin;
use ClicShopping\OM\Registry;
/**
 * Class Core
 *
 * This class represents the core service for the ClicShoppingAdmin namespace.
 * It implements the ServiceInterface and provides functionality to start and stop the service.
 *
 * The `start` method initializes the MessageStack instance and registers it in the Registry
 * if the required file exists, while the `stop` method is designed to terminate the service.
 */
class Core implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Starts the process by checking for the existence of the required file and initializing the MessageStackClassAdmin.
   *
   * @return bool Returns true if the file exists and the MessageStackClassAdmin is successfully initialized; otherwise, false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'OM/MessageStack.php')) {
      Registry::set('MessageStack', new MessageStackClassAdmin());

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the current process or operation.
   *
   * @return bool Returns true when the process is successfully stopped.
   */
  public static function stop(): bool
  {
    return true;
  }
}
