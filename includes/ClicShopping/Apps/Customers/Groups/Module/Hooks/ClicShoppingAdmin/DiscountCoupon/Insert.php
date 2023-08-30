<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\DiscountCoupon;

use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Groups')) {
      Registry::set('Groups', new GroupsApp());
    }

    $this->app = Registry::get('Groups');
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS') || CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'False') {
      return false;
    }

    if (\defined('CLICSHOPPING_APP_DISCOUNT_COUPON_DC_STATUS') && CLICSHOPPING_APP_DISCOUNT_COUPON_DC_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Insert'], $_GET['DiscountCoupon'])) {
// Definir la position 0 ou 1 pour --> coupons_create_account : Code coupon envoye lors de la creation d'un compte par le client pour les clients B2B
      if (isset($_POST['coupons_create_account_b2b']) && HTML::sanitize($_POST['coupons_create_account_b2b']) == 1) {
        $coupons_create_account_b2b = 1;
      } else {
        $coupons_create_account_b2b = 0;
      }

// Count the coupon for create account b2b for an alert message
      $QcouponsCountB2c = $this->app->db->prepare('select count(coupons_create_account_b2c) as coupons_b2c_count
                                                     from :table_discount_coupons
                                                     where coupons_create_account_b2c = :coupons_create_account_b2c
                                                    ');
      $QcouponsCountB2c->bindValue(':coupons_create_account_b2c', 1);
      $QcouponsCountB2c->execute();

      $QCoupon = $this->app->db->prepare('select coupons_id
                                             from :table_discount_coupons
                                             order by coupons_id desc
                                             limit 1
                                            ');
      $QCoupon->execute();

      $sql_data_array = ['coupons_create_account_b2b' => (int)$coupons_create_account_b2b];

      $this->app->db->save('discount_coupons', $sql_data_array, ['coupons_id' => (int)$QCoupon->value('coupons_id')]);

//Update the configuration coupon in configuration database
      if (empty(COUPON_CUSTOMER_B2B)) {
        if ($coupons_create_account_b2b == 1) {

          $Qupdate = $this->app->db->prepare('update :table_configuration
                                                set configuration_value = :configuration_value
                                                where configuration_key = :configuration_key
                                              ');
          $Qupdate->bindValue(':configuration_value', $QCoupon->value('coupons_id'));
          $Qupdate->bindValue(':configuration_key', 'COUPON_CUSTOMER_B2B');
          $Qupdate->execute();
        }
      }
    }
  }
}