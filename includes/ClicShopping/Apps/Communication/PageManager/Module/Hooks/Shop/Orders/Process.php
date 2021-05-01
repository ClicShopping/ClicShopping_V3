<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Communication\PageManager\Module\Hooks\Shop\Orders;

  use ClicShopping\OM\Registry;

  class Process implements \ClicShopping\OM\Modules\HooksInterface
  {

    protected $app;

    /*
      * getPageManagerGeneralConditons
      * @param $insert_id, order_id
      * @return array and save general condition
      * @access private
     */
// insert the general condition of sales
    private function getPageManagerGeneralConditons()
    {

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');
      $CLICSHOPPING_Order = Registry::get('Order');


      $page_manager_general_condition = $CLICSHOPPING_PageManagerShop->pageManagerGeneralCondition();

      $sql_data_array = ['orders_id' => (int)$CLICSHOPPING_Order->getLastOrderId(),
        'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
        'page_manager_general_condition' => $page_manager_general_condition
      ];

      $CLICSHOPPING_Db->save('orders_pages_manager', $sql_data_array);
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS') || CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS == 'False') {
        return false;
      }

      $this->getPageManagerGeneralConditons();
    }
  }