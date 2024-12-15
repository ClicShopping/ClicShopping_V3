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
use ClicShopping\Sites\Shop\Address as AddressClass;
/**
 * Class Address
 *
 * This class implements the ClicShopping service interface for the Address module in the Shop site.
 * It manages the initialization and termination of the Address service within the application.
 */
class Address implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the Address module by checking if the required Address.php file exists
   * and registering the Address class in the registry.
   *
   * @return bool Returns true if the file exists and the Address class is successfully registered; false otherwise.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Sites/Shop/Address.php')) {
      Registry::set('Address', new AddressClass());

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the process or operation.
   *
   * @return bool Returns true if the process was successfully stopped.
   */
  public static function stop(): bool
  {
    return true;
  }
}
