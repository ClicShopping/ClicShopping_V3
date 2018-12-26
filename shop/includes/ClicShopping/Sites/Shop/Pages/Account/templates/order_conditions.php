<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Customer = Registry::get('Customer');

  $customer_id = $CLICSHOPPING_Customer->getID();

  $QconditionGeneralOfSales = $CLICSHOPPING_Db->prepare('select page_manager_general_condition
                                                             from  :table_orders_pages_manager
                                                             where orders_id = :orders_id
                                                             and customers_id = :customers_id
                                                           ');

  $QconditionGeneralOfSales->bindInt(':orders_id', $_GET['order_id']);
  $QconditionGeneralOfSales->bindInt(':customers_id', $customer_id);

  $QconditionGeneralOfSales->execute();
?>

<html dir="ltr" lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo 'Conditions of sales'; ?></title>
  </head>
  <body onload="resize();">
    <?php echo $QconditionGeneralOfSales->value('page_manager_general_condition'); ?>
  </body>
</html>

