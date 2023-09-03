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
  /**
   * @return mixed
   */
  public static function getCheckoutSuccessOrder(): mixed
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

  /**
   * @return void
   */
  public static function getCheckoutSuccessOrderCheck(): void
  {
    $Qorders = static::getCheckoutSuccessOrder();
    // redirect to shopping cart page if no orders exist
    if ($Qorders->fetch() === false) {
      CLICSHOPPING::redirect(null, 'Cart');
    }
  }

  /**
   * @return int
   */
  public static function getCheckoutSuccessOrderId(): int
  {
    $Qorders = static::getCheckoutSuccessOrder();

    $orders = $Qorders->toArray();

    $order_id = (int)$orders['orders_id'];

    return $order_id;
  }
}