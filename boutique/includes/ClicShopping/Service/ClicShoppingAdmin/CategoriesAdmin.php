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
  namespace ClicShopping\Service\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin\CategoriesAdmin as CategoriesAdminClass;

  class CategoriesAdmin implements \ClicShopping\OM\ServiceInterface {

    public static function start() {

      if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Classes/ClicShoppingAdmin/CategoriesAdmin.php')) {
        Registry::set('CategoriesAdmin', new CategoriesAdminClass());

        return true;
      } else {
        return false;
      }
    }

    public static function stop() {
      return true;
    }
  }
