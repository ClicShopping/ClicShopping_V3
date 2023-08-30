<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop\Pages\Checkout\Classes;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class CheckoutSuccess
{

  public static function getCheckoutSuccessOrder()
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    $Qorders = $CLICSHOPPING_Db->prepare('select orders_id
                                            from :table_orders
                                            where customers_id = :customers_id
                                            order by date_purchased desc
                                            limit 1
                                          ');
    $Qorders->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
    $Qorders->execute();

    return $Qorders;
  }

  public static function getCheckoutSuccessOrderCheck()
  {
    $Qorders = static::getCheckoutSuccessOrder();
    // redirect to shopping cart page if no orders exist
    if ($Qorders->fetch() === false) {
      CLICSHOPPING::redirect(null, 'Cart');
    }
  }

  public static function getCheckoutSuccessOrderId()
  {
    $Qorders = static::getCheckoutSuccessOrder();

    $orders = $Qorders->toArray();

    $order_id = (int)$orders['orders_id'];

    return $order_id;
  }
}