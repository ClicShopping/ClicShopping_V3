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

  class History {

    public static function getOrderHistory() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qorders = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS o.orders_id,
                                                                       o.date_purchased,
                                                                       o.delivery_name,
                                                                       o.billing_name,
                                                                       ot.text as order_total,
                                                                       s.orders_status_name
                                               from :table_orders o,
                                                    :table_orders_total ot,
                                                    :table_orders_status s
                                               where o.customers_id = :customers_id
                                               and s.language_id = :language_id
                                               and (ot.class = :class or ot.class = :class1)
                                               and s.public_flag = 1
                                               and o.orders_id = ot.orders_id
                                               and o.orders_status = s.orders_status_id
                                               order by o.orders_id desc
                                               limit :page_set_offset,
                                                     :page_set_max_results
                                              ');

      $Qorders->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qorders->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qorders->bindValue(':class', 'ot_total');
      $Qorders->bindValue(':class1', 'TO');
      $Qorders->setPageSet(MAX_DISPLAY_ORDER_HISTORY);
      $Qorders->execute();

      return $Qorders;
    }

    public static function getOrderTotalRows() {
      $orders =  static::getOrderHistory();

      $ordersTotalRow = $orders->getPageSetTotalRows();

      return $ordersTotalRow;
    }
  }