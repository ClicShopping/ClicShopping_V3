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
use ClicShopping\Sites\ClicShoppingAdmin\CkEditor4 as CkEditor4;
use ClicShopping\Sites\ClicShoppingAdmin\CkEditor5 as CkEditor5;
/**
 * Service class for handling the WYSIWYG editor integration within the ClicShoppingAdmin application.
 * This class implements the ServiceInterface and provides methods to start and stop the WYSIWYG service.
 */
class Wysiwyg implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (defined('DEFAULT_WYSIWYG') && DEFAULT_WYSIWYG == 'CkEditor5') {
      Registry::set('Wysiwyg', new CkEditor5());
    }

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
