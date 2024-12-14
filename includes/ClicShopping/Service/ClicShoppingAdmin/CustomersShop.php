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
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Customers/Customers/Classes/Shop/CustomerShop.php')) {
      Registry::set('Customer', new CustomerShopClass());
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
