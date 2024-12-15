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

use ClicShopping\Apps\Catalog\Products\Classes\Shop\ProductsRecommendations as Recommendation;
/**
 * Service class responsible for handling the initialization and shutdown of the ProductsRecommendations functionality.
 *
 * This class implements the ClicShopping service interface and provides methods to manage the
 * lifecycle of the ProductsRecommendations service. It ensures the necessary file exists and
 * manages its registration with the registry.
 */
class ProductsRecommendations implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Starts the ProductsRecommendations class if the required file exists.
   *
   * @return bool Returns true if the file is found and the class is initialized; otherwise, returns false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Products/Classes/Shop/ProductsRecommendations.php')) {
      Registry::set('ProductsRecommendations', new Recommendation());

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the current process or operation.
   *
   * @return bool Returns true if the operation was stopped successfully.
   */
  public static function stop(): bool
  {
    return true;
  }
}
