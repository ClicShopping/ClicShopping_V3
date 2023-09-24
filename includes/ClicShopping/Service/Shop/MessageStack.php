<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\MessageStack as MessageStackClass;
use ClicShopping\OM\Registry;

class MessageStack implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
// initialize the message stack for output messages
    $CLICSHOPPING_Service = Registry::get('Service');

    if (is_file(CLICSHOPPING::BASE_DIR . 'OM/MessageStack.php')) {
      Registry::set('MessageStack', new MessageStackClass());

      $CLICSHOPPING_Service->addCallBeforePageContent('Address', 'initialize');

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
