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
use ClicShopping\Sites\ClicShoppingAdmin\Composer as ComposerClass;
/**
 * This class implements the ServiceInterface and represents
 * the Composer service within the ClicShoppingAdmin namespace.
 * It is used to register and manage the Composer class instance
 * in the Registry.
 */
class Composer implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (!Registry::exists('Composer')) {
      Registry::set('Composer', new ComposerClass());
    }

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
