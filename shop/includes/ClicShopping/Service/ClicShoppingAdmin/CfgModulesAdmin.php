<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Service\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\CfgModulesAdmin as CfgModulesAdminClass;

  class CfgModulesAdmin implements \ClicShopping\OM\ServiceInterface {

    public static function start() {
      Registry::set('CfgModulesAdmin', new CfgModulesAdminClass());

      return true;
    }

    public static function stop() {
      return true;
    }
  }
