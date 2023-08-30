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

class delete implements \ClicShopping\OM\Modules\HooksInterface
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

    if (isset($_POST['product_id'])) {
      $product_id = HTML::sanitize($_POST['product_id']);

      $this->app->db->delete('discount_coupons_to_products', ['products_id' => (int)$product_id]);
    }
  }
}