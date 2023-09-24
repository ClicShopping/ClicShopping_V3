<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

/**
 * the customer group name
 *
 * @param string $orders_status_id , $language_id
 * @return string $orders_status['orders_status_name'],  name of the status
 *
 */

use ClicShopping\OM\Registry;

function clic_cfg_use_function_get_customers_group_name($customers_group_id)
{

  $QcustomersGroup = Registry::get('Db')->get('customers_groups', 'customers_group_name', ['customers_group_id' => (int)$customers_group_id]);

  if ($QcustomersGroup->fetch() === false) {
    return $customers_group_id;
  } else {
    return $QcustomersGroup->value('customers_group_name');
  }
}