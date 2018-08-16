<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\Apps\Catalog\Categories\Classes\Shop\Category as CategoryClass;
  use ClicShopping\Apps\Catalog\Categories\Classes\Shop\CategoryTree as CategoryTreeClass;

  class CategoryPath implements\ClicShopping\OM\ServiceInterface {
    public static function start() {

      if (is_file(CLICSHOPPING_BASE_DIR . 'Apps/Catalog/Categories/Classes/Shop/Category.php')) {
        Registry::set('CategoryTree', new CategoryTreeClass());
        Registry::set('Category', new CategoryClass());

        return true;
      } else {
        return false;
      }
    }

    public static function stop() {
      return true;
    }
  }