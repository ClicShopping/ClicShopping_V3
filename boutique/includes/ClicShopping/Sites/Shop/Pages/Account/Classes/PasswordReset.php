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

  namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

  use ClicShopping\OM\Registry;

  class PasswordReset {


    public static function getPasswordResetCheckEmailAddress($email_address) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->prepare('select c.customers_id,
                                        c.customers_email_address,
                                        ci.password_reset_key,
                                        ci.password_reset_date
                                 from :table_customers c,
                                      :table_customers_info ci
                                 where c.customers_email_address = :customers_email_address
                                 and c.customers_id = ci.customers_info_id
                                 limit 1
                               ');
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->execute();

      $check_email_address = $Qcheck->fetch();
      return $check_email_address;
    }

  }