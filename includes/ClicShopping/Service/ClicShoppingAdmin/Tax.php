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
use ClicShopping\Sites\ClicShoppingAdmin\Tax as TaxClass;
/**
 * Service class for managing the Tax functionality in the ClicShoppingAdmin system.
 * This class implements the ServiceInterface to define the required service lifecycle methods.
 */
class Tax implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('Tax', new TaxClass());

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
