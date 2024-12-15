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

use ClicShopping\Apps\Orders\Orders\Classes\Shop\Order as OrderClass;
/**
 * This class implements the service interface and provides functionality
 * to initialize and stop the Order class in the application.
 *
 * The class checks for the existence of the Order class file,
 * initializes it in the registry, and ensures the service is properly started and stopped.
 */
class Order implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the 'Order' class if the required file exists.
   *
   * @return bool Returns true if the 'Order' class is successfully initialized, false otherwise.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Orders/Orders/Classes/Shop/Order.php')) {
      Registry::set('Order', new OrderClass());
      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the current process or operation.
   *
   * @return bool Returns true when the stop operation is successfully executed.
   */
  public static function stop(): bool
  {
    return true;
  }
}
