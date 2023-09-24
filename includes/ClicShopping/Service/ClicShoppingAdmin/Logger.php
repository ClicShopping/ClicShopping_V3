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
