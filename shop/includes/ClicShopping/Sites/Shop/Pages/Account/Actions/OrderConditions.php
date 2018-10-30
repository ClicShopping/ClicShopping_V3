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
  namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class OrderConditions extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      global $QconditionGeneralOfSales;

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $CLICSHOPPING_Hooks->call('OrderConditions', 'PreAction');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect('index.php', 'Account&LogIn');
      }


      if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
        CLICSHOPPING::redirect('account_history.php', '');
      }

      $QcustomerInfo = $CLICSHOPPING_Db->prepare('select o.customers_id
                                            from :table_orders o,
                                                 :table_orders_status s
                                            where  o.orders_id = :orders_id
                                            and o.orders_status = s.orders_status_id
                                            and s.language_id = :language_id
                                            and s.public_flag = :public_flag
                                          ');

      $QcustomerInfo->bindInt(':orders_id', $_GET['order_id']);
      $QcustomerInfo->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QcustomerInfo->bindValue(':public_flag', '1');

      $QcustomerInfo->execute();

      $customer_id = $QcustomerInfo->valueInt('customers_id');

// select the conditions of sales
      $QconditionGeneralOfSales = $CLICSHOPPING_Db->prepare('select page_manager_general_condition
                                                       from  :table_orders_pages_manager
                                                       where orders_id = :orders_id
                                                       and customers_id = :customers_id
                                                     ');

      $QconditionGeneralOfSales->bindInt(':orders_id', $_GET['order_id']);
      $QconditionGeneralOfSales->bindInt(':customers_id', $customer_id);

      $QconditionGeneralOfSales->execute();

      $this->page->setFile('order_conditions.php');
    }
  }