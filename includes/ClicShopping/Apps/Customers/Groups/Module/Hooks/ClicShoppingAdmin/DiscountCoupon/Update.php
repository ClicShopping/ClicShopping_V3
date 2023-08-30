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

class Update implements \ClicShopping\OM\Modules\HooksInterface
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

    if (isset($_GET['Update']) && isset($_GET['DiscountCoupon'])) {
      if (isset($_POST['coupons_id'])) {
        $coupons_id = (!empty($_POST['coupons_id']) ? $_POST['coupons_id'] : (!empty($_GET['cID']) ? $_GET['cID'] : DiscountCouponsAdmin::CreateRandomDiscountCoupons()));

        // Definir la position 0 ou 1 pour --> coupons_create_account : Code coupon envoye lors de la creation d'un compte par le client pour les clients B2B
        if (isset($_POST['coupons_create_account_b2b']) && HTML::sanitize($_POST['coupons_create_account_b2b']) == 1) {
          $coupons_create_account_b2b = 1;
        } else {
          $coupons_create_account_b2b = 0;
        }

        // Count the coupon for create account b2b for an alert message
        $QcouponsCountB2b = $this->app->db->prepare('select count(coupons_create_account_b2b) as coupons_b2b_count
                                                       from :table_discount_coupons
                                                       where coupons_create_account_b2b = :coupons_create_account_b2b
                                                     ');
        $QcouponsCountB2b->bindInt(':coupons_create_account_b2b', 1);
        $QcouponsCountB2b->execute();

        // update the different coupon if there are another value
        $QcouponsTest = $this->app->db->prepare('select coupons_id,
                                                          coupons_create_account_b2b
                                                   from :table_discount_coupons
                                                   where coupons_id = :coupons_id
                                                  ');
        $QcouponsTest->bindValue(':coupons_id', $coupons_id);
        $QcouponsTest->execute();

        if ($coupons_create_account_b2b != $QcouponsTest->valueInt('coupons_create_account_b2b')) {
          $Qupdate = $this->app->db->prepare('update :table_discount_coupons
                                                set coupons_create_account_b2b = :coupons_create_account_b2b
                                                where coupons_id = :coupons_id
                                               ');
          $Qupdate->bindInt(':coupons_create_account_b2b', $coupons_create_account_b2b);
          $Qupdate->bindValue(':coupons_id', $coupons_id);
          $Qupdate->execute();
        }

        //Update the configuration coupon in configuration database
        if ((COUPON_CUSTOMER_B2B == $coupons_id) || (COUPON_CUSTOMER_B2B == '')) {
          if ($coupons_create_account_b2b == 1) {
            $Qupdate = $this->app->db->prepare('update :table_configuration
                                                  set configuration_value = :configuration_value
                                                  where configuration_key = :configuration_key
                                                 ');
            $Qupdate->bindValue(':configuration_key', 'COUPON_CUSTOMER_B2B');
            $Qupdate->bindValue(':configuration_value', $coupons_id);
            $Qupdate->execute();

          } else {
            $Qupdate = $this->app->db->prepare('update :table_configuration
                                                  set configuration_value = :configuration_value
                                                  where configuration_key = :configuration_key
                                                 ');
            $Qupdate->bindValue(':configuration_key', 'COUPON_CUSTOMER_B2B');
            $Qupdate->bindValue(':configuration_value', '');
            $Qupdate->execute();
          }
        }
      }
    }
  }
}