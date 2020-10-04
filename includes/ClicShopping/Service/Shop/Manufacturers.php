<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Manufacturers\Classes\Shop\Manufacturers as ManufacturersShopClass;

  class Manufacturers implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Manufacturers/Classes/Shop/Manufacturers.php')) {
        Registry::set('Manufacturers', new ManufacturersShopClass());
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
