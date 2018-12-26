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

  namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

  use ClicShopping\OM\Registry;

  class Notifications {

    public static function getGlobalNotificationCustomer() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qnotification = $CLICSHOPPING_Db->prepare('select global_product_notifications
                                                  from :table_customers_info
                                                  where customers_info_id = :customers_info_id
                                                ');
      $Qnotification->bindInt(':customers_info_id', $CLICSHOPPING_Customer->getID());
      $Qnotification->execute();

      return $Qnotification->valueInt('global_product_notifications');
    }

    public static function getGlobalProductNotificationsCheckRowCount() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $QCheck = $CLICSHOPPING_Db->prepare('select count(*)
                                          from :table_products_notifications
                                          where customers_id = :customers_id
                                         ');
      $QCheck->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());
      $QCheck->execute();

      $number = $QCheck->rowCount();

      return $number;
    }

    public static function getGlobalProductNotificationsProduct($products_id = null) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!is_null($products_id)) {
        $Qproducts = $CLICSHOPPING_Db->prepare('select pd.products_id,
                                                      pd.products_name
                                                from :table_products_description pd,
                                                     :table_products_notifications pn
                                                where pn.customers_id = :customers_id
                                                and pn.products_id = pd.products_id
                                                and pd.language_id = :language_id
                                                and pn.products_id = :products_id
                                                order by pd.products_name
                                                ');
        $Qproducts->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
        $Qproducts->bindInt(':products_id', (int)$products_id);
        $Qproducts->execute();

        $products_name = $Qproducts->value('products_name');
      }

      return $products_name;
    }
  }