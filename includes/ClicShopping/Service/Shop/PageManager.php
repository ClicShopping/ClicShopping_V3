<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Communication\PageManager\Classes\Shop\PageManagerShop as PageManagerShopClass;

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
