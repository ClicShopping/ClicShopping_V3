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

class Core implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'OM/MessageStack.php')) {
      Registry::set('MessageStack', new MessageStackClassAdmin());

      return true;
    } else {
      return false;
    }
  }

  public static function stop(): bool
  {
    return true;
  }
}
