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

  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Orders = Registry::get('Orders');

  $QconditionGeneralOfSales = $CLICSHOPPING_Orders->db->prepare('select page_manager_general_condition
                                                          from :table_orders_pages_manager
                                                          where orders_id = :orders_id
                                                          and customers_id = :customers_id
                                                          ');
  $QconditionGeneralOfSales->bindInt(':orders_id', (int)$_GET['order_id'] );
  $QconditionGeneralOfSales->bindInt(':customers_id', (int)$_GET['customer_id'] );
  $QconditionGeneralOfSales->execute();

  echo $QconditionGeneralOfSales->value('page_manager_general_condition');
