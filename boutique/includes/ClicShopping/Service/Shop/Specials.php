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
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Marketing\Specials\Classes\Shop\SpecialsClass;

  class Specials implements \ClicShopping\OM\ServiceInterface {

    public static function start() {

      if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Specials/Classes/Shop/SpecialsClass.php')) {
        Registry::set('SpecialsClass', new SpecialsClass());

        $CLICSHOPPING_Specials = Registry::get('SpecialsClass');

        $CLICSHOPPING_Specials->scheduledSpecials();
        $CLICSHOPPING_Specials->expireSpecials();

        return true;
      } else {
        return false;
      }
    }

    public static function stop() {
      return true;
    }
  }
