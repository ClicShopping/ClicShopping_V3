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

  class ValidateAntiSpam  {

    public static function execute($antispam) {
      if ($antispam != 5) {
        $valid_antispam = false;
      } else {
        $valid_antispam = true;
      }

      return $valid_antispam;
    }
  }
