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

  namespace ClicShopping\OM\Is;

  class ValidateAntiSpam {

    public static function execute($antispan_confirmation) {

      if ($antispan_confirmation !== $_SESSION['createResponseAntiSpam']) {
        $valid_antispan_confirmation = false;
      } else {
        $valid_antispan_confirmation = true;
      }

      unset($_SESSION['createResponseAntiSpam']);

      return $valid_antispan_confirmation;
    }
  }
