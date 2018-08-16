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

  use ClicShopping\OM\CLICSHOPPING;

  function clic_cfg_use_get_boolean_value($string) {
    switch ($string) {
      case -1:
      case '-1':
//        return CLICSHOPPING::getDef('parameter_false');
        return false;
        break;

      case 0:
      case '0':
//        return CLICSHOPPING::getDef('parameter_optional');
        return 'optional';
        break;

      case 1:
      case '1':
//        return CLICSHOPPING::getDef('parameter_true');
      return true;
        break;

      default:
        return $string;
        break;
    }
  }