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

class CustomerGroup implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Groups')) {
      Registry::set('Groups', new GroupsApp());
    }

    $this->app = Registry::get('Groups');
  }

  private function getCouponB2BCount(): int|null
  {
// Count the coupon for create account b2b for an alert message
    $QcouponsCountB2b = $this->app->db->prepare('select count(coupons_create_account_b2b) as coupons_b2b_count
                                                   from :table_discount_coupons
                                                   where coupons_create_account_b2b = 1
                                                  ');
    $QcouponsCountB2b->execute();

    return $QcouponsCountB2b->valueInt('coupons_b2b_count');
  }

  /**
   * @return string
   */
  private function getCoupon(): int|null
  {
    if (isset($_GET['cID'])) {
      $coupons_id = HTML::sanitize($_GET['cID']);
    } else {
      $coupons_id = '';
    }

    $QcouponsCountB2b = $this->app->db->prepare('select coupons_create_account_b2b
                                                   from :table_discount_coupons
                                                   where coupons_id = :coupons_id
                                                  ');
    $QcouponsCountB2b->bindValue(':coupons_id', $coupons_id);
    $QcouponsCountB2b->execute();

    return $QcouponsCountB2b->valueInt('coupons_create_account_b2b');
  }

  public function display()
  {
    if (!\defined('CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS') || CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'False') {
      return false;
    }

    if (\defined('CLICSHOPPING_APP_DISCOUNT_COUPON_DC_STATUS') && CLICSHOPPING_APP_DISCOUNT_COUPON_DC_STATUS == 'False') {
      return false;
    }

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/CustomerGroup/customer_group');

    if (CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'True' && !empty(CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS)) {
      if (MODE_B2B_B2C == 'True') {
        $content = '<div class="row">';
        $content .= '<div class="col-md-5">';
        $content .= '<div class="form-group row">';
        $content .= '<label for="' . $this->app->getDef('text_customer_group_b2b') . '" class="col-5 col-form-label">' . $this->app->getDef('text_customer_group_b2b') . '</label>';
        $content .= '<div class="col-md-5">';
        $content .= HTML::checkboxField('coupons_create_account_b2b', '1', $this->getCoupon());
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        if ($this->getCouponB2BCount() > 0) {
          $content .= '<div class="row">';
          $content .= '<div class="col-md-12">';
          $content .= '<strong>' . $this->app->getDef('text_discount_coupons_warning_b2b') . '</strong>';
          $content .= '</div>';
          $content .= '</div>';
        }

        $title = $this->app->getDef('text_group');

        $output = <<<EOD
<!-- ######################## -->
<!--  Start CustomersGroup      -->
<!-- ######################## -->
        <div class="separator"></div>
        <div class="mainTitle">
          <span class="col-md-10">{$title}</span>
        </div>
        <div class="adminformTitle">
           {$content}
        </div>
<!-- ######################## -->
<!--  Start CustomersGroup      -->
<!-- ######################## -->
EOD;
        return $output;
      }
    }
  }
}
