<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Classes\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ReturnOrderStatus
{
  /**
   * the status name
   *
   * @param int $return_orders_status_id , $language_id
   * @param int $language_id
   * @return string $orders_status['name'],  name of the status
   */
  public static function getReturnOrdersStatusName(int $return_orders_status_id, int $language_id): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

    $Qstatus = $CLICSHOPPING_Db->get('return_orders_status', 'name', ['return_orders_status_id' => (int)$return_orders_status_id, 'language_id' => $language_id]);

    return $Qstatus->value('name');
  }

  /**
   * Get DropDown orders Status
   *
   * @param string $name
   * @param int|null $id
   * @param string $displays_all_orders_status
   * @return string status order
   */
  public static function getDropDownReturnOrderStatus(string $name = 'dropdown_status', $id = null, string $displays_all_orders_status = 'yes'): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    $orders_statuses = [];

    $QordersStatus = $CLICSHOPPING_Db->prepare('select return_orders_status_id,
                                                          name
                                                  from :table_return_orders_status
                                                  where language_id = :language_id
                                                  ');
    $QordersStatus->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
    $QordersStatus->execute();

    if (isset($displays_all_orders_status)) {
      $orders_statuses[] = [
        'id' => '0',
        'text' => CLICSHOPPING::getDef('text_all_orders')
      ];
    } else {
      $orders_statuses[] = [
        'id' => '0',
        'text' => CLICSHOPPING::getDef('text_select')
      ];
    }

    while ($QordersStatus->fetch() !== false) {
      $orders_statuses[] = [
        'id' => $QordersStatus->valueInt('return_orders_status_id'),
        'text' => $QordersStatus->value('name')
      ];

    }

    $status = HTML::selectMenu($name, $orders_statuses, $id);

    return $status;
  }
}
