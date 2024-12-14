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

use ClicShopping\Apps\Customers\Reviews\Classes\Shop\ReviewsClass as NewReviews;
/**
 * Service class responsible for managing the initialization and termination of the Reviews module in the shop.
 * Implements the ClicShopping service interface.
 */
class Reviews implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Customers/Reviews/Classes/Shop/ReviewsClass.php')) {
      Registry::set('Reviews', new NewReviews());

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
