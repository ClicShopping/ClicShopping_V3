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

use ClicShopping\Apps\Communication\PageManager\Classes\Shop\PageManagerShop as PageManagerShopClass;
/**
 * Service class for managing the PageManager functionality within the shop.
 * This service initializes and activates the PageManagerShop class, enabling
 * the handling of page manager-related operations.
 */
class PageManager implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Communication/PageManager/Classes/Shop/PageManagerShop.php')) {
      Registry::set('PageManagerShop', new PageManagerShopClass());

      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

      $CLICSHOPPING_PageManagerShop->activatePageManager();
      $CLICSHOPPING_PageManagerShop->expirePageManager();

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
