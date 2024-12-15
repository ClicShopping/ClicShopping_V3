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
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Weight\Classes\Shop\Weight as WeightShop;
/**
 * Service class for initializing and managing the Weight module in the shop.
 * This service checks the presence of the Weight module file and registers it in the system Registry.
 */
class Weight implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the WeightShop class if the required file exists.
   *
   * @return bool Returns true if the file exists and the class is instantiated, otherwise false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Weight/Classes/Shop/Weight.php')) {
      Registry::set('Weight', new WeightShop());

      return true;
    } else {
      return false;
    }
  }

  /**
   *
   * @return bool Returns true indicating the stop operation was successful.
   */
  public static function stop(): bool
  {
    return true;
  }
}
