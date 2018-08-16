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

  class ValidateNumberEmail  {

    public static function execute($number_email_confirmation) {
      if ($number_email_confirmation != 5) {
        $valid_number_email_confirmation = false;
      } else {
        $valid_number_email_confirmation = true;
      }

      return $valid_number_email_confirmation;
    }

/*
    // check if the number is good before to send email
    public static function ValidateNumberEmail($number_email_confirmation) {
        if ($number_email_confirmation != 5) {
          $valid_number_email_confirmation = false;
        } else {
          $valid_number_email_confirmation = true;
        }
     return $valid_number_email_confirmation;
    }
 */


  }

