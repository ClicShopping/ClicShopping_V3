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

use ClicShopping\Apps\Marketing\Featured\Classes\Shop\FeaturedClass;
/**
 * The Featured service is responsible for initializing and managing the functionality
 * of the Featured module within the shop.
 *
 * This service checks for the existence of the required Featured class file.
 * When the file exists, it initializes the FeaturedClass, invokes the
 * scheduledFeatured and expireFeatured methods to handle related processes,
 * and registers the class instance for global use within the application.
 *
 * Methods:
 * - start(): Initializes the Featured service by checking for the required class file,
 *            loading it, and performing related operations. Returns true on success and false on failure.
 * - stop(): Stops the Featured service. Currently, this simply returns true without additional operations.
 */
class Featured implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Featured/Classes/Shop/Featured.php')) {
      Registry::set('FeaturedClass', new FeaturedClass());

      $CLICSHOPPING_Featured = Registry::get('FeaturedClass');

      $CLICSHOPPING_Featured->scheduledFeatured();
      $CLICSHOPPING_Featured->expireFeatured();

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
