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

use ClicShopping\Apps\Marketing\Favorites\Classes\Shop\FavoritesClass;
/**
 * Service class for managing the Favorites functionality in the shop.
 *
 * This class implements the ServiceInterface and provides methods to
 * initialize and terminate the Favorites service. It ensures the availability
 * of the Favorites module and invokes necessary functions related to scheduled
 * and expired favorites handling.
 */
class Favorites implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initiates the FavoritesClass if the required file exists.
   *
   * @return bool Returns true if the FavoritesClass is successfully initialized and executed, otherwise false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Favorites/Classes/Shop/FavoritesClass.php')) {
      Registry::set('FavoritesClass', new FavoritesClass());

      $CLICSHOPPING_Favorites = Registry::get('FavoritesClass');

      $CLICSHOPPING_Favorites->scheduledFavorites();
      $CLICSHOPPING_Favorites->expireFavorites();

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the current operation.
   *
   * @return bool Returns true upon successful completion.
   */
  public static function stop(): bool
  {
    return true;
  }
}
