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
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Customers\Classes\Shop\CustomerShop as CustomerShopClass;

/**
 * This service is part of the ClicShopping administration, specifically for managing the Customers functionality
 * in the Shop application context by interfacing with the CustomerShop class.
 */
class CustomersShop implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the CustomerShopClass if the required file exists.
   *
   * @return bool Returns true if the file exists and the class is successfully initialized; otherwise, false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Customers/Customers/Classes/Shop/CustomerShop.php')) {
      Registry::set('Customer', new CustomerShopClass());
      return true;
    } else {
      return false;
    }
  }

  /**
   * Terminates the process or operation.
   *
   * @return bool Returns true indicating the process was successfully stopped.
   */
  public static function stop(): bool
  {
    return true;
  }
}
