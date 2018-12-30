<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\OM\MessageStack as MessageStackClass;

  class MessageStack implements \ClicShopping\OM\ServiceInterface {

    public static function start() {
// initialize the message stack for output messages
      if (is_file(CLICSHOPPING::BASE_DIR . 'OM/MessageStack.php')) {
        Registry::set('MessageStack', new MessageStackClass());

        return true;
      } else {
        return false;
      }
    }

    public static function stop() {
      return true;
    }
  }
