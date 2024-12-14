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

use ClicShopping\OM\Registry;
use ClicShopping\Sites\ClicShoppingAdmin\LoggerAdmin;
/**
 * Service class for handling the initialization and management of the LoggerAdmin service
 * within the ClicShoppingAdmin site.
 *
 * This class implements the ClicShopping\OM\ServiceInterface and provides the methods
 * for starting and stopping the LoggerAdmin service.
 */
class Logger implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('LoggerAdmin', new LoggerAdmin());

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
