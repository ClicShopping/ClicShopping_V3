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

use ClicShopping\Apps\Marketing\BannerManager\Classes\Shop\Banner as BannerClass;
/**
 * Class Banner
 *
 * This service handles the initialization and management of the Banner functionality.
 * It integrates with the BannerManager app and ensures banners are activated and expired appropriately.
 * Implements the ServiceInterface for use within the ClicShopping framework.
 */
class Banner implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the BannerManager application by setting up the necessary dependencies
   * and triggering the activation and expiration of banners.
   *
   * @return bool Returns true if the BannerManager class was successfully initialized;
   *              otherwise, returns false if the required file is not found.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Marketing/BannerManager/Classes/Shop/Banner.php')) {
      Registry::set('Banner', new BannerClass());

      $CLICSHOPPING_Banner = Registry::get('Banner');

      $CLICSHOPPING_Banner->activateBanners();
      $CLICSHOPPING_Banner->expireBanners();

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the execution process.
   *
   * @return bool Returns true if the process is successfully stopped.
   */
  public static function stop(): bool
  {
    return true;
  }
}
