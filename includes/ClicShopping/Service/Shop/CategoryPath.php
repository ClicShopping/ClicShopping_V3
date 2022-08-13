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

  use ClicShopping\Apps\Catalog\Categories\Classes\Shop\Category as CategoryClass;
  use ClicShopping\Apps\Catalog\Categories\Classes\Shop\CategoryTree as CategoryTreeClass;

  class CategoryPath implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Classes/Shop/Category.php')) {
        Registry::set('CategoryTree', new CategoryTreeClass());
        Registry::set('Category', new CategoryClass());

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