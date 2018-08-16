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

  /**
   * the customer group name
   *
   * @param string  $orders_status_id, $language_id
   * @return string $orders_status['orders_status_name'],  name of the status
   * @access public
   * osc_get_customers_group_name
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  function clic_cfg_use_function_get_customers_group_name($customers_group_id) {

    $QcustomersGroup = Registry::get('Db')->get('customers_groups', 'customers_group_name', ['customers_group_id' => (int)$customers_group_id] );

    if ($QcustomersGroup->fetch() === false) {
      return $customers_group_id;
    } else {
      return $QcustomersGroup->value('customers_group_name');
    }
  }