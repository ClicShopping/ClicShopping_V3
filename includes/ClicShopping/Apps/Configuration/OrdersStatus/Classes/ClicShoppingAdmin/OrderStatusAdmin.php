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

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class OrderStatusAdmin
  {

    protected $orders_status_id;
    protected $language_id;

    /**
     * the status name
     *
     * @param string $orders_status_id , $language_id
     * @return string $orders_status['orders_status_name'],  name of the status
     *
     */
    Public Static function getOrdersStatusName(int $orders_status_id, int $language_id): string
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qstatus = $CLICSHOPPING_Db->get('orders_status', 'orders_status_name', ['orders_status_id' => (int)$orders_status_id, 'language_id' => $language_id]);

      return $Qstatus->value('orders_status_name');
    }

    /**
     * Get DropDown orders Status
     *
     * @param string countries_id, status
     * @return string status order
     *
     */

    Public Static function getDropDownOrderStatus(string $name = 'dropdown_status', $id = null, string $displays_all_orders_status = 'yes'): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $orders_statuses = [];

      $QordersStatus = $CLICSHOPPING_Db->prepare('select orders_status_id,
                                                          orders_status_name
                                                  from :table_orders_status
                                                  where language_id = :language_id
                                                  ');
      $QordersStatus->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
      $QordersStatus->execute();

      if (isset($displays_all_orders_status)) {
        $orders_statuses[] = ['id' => '0', 'text' => CLICSHOPPING::getDef('text_all_orders')];
      } else {
        $orders_statuses[] = ['id' => '0', 'text' => CLICSHOPPING::getDef('text_select')];
      }

      while ($QordersStatus->fetch() !== false) {
        $orders_statuses[] = ['id' => $QordersStatus->valueInt('orders_status_id'),
          'text' => $QordersStatus->value('orders_status_name')
        ];

      }

      $status = HTML::selectMenu($name, $orders_statuses, $id);

      return $status;
    }
  }
