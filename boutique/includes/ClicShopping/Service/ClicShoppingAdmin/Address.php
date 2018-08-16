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

  use ClicShopping\Sites\Shop\Address as AddressClass;

  class Address implements \ClicShopping\OM\ServiceInterface {

    public static function start() {

      if (is_file(CLICSHOPPING_BASE_DIR . 'Sites/Shop/Address.php')) {
        if (!Registry::exists('Address')) {
         Registry::set('Address', new AddressClass());
        }
        return true;
      } else {
        return false;
      }
    }

    public static function stop() {
      return true;
    }
  }
