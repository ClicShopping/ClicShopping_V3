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

  /**
   * the status name
   *
   * @param string $orders_status_id , $language_id
   * @return string $orders_status['orders_status_name'],  name of the status
   *
   */

  use ClicShopping\OM\Registry;

  function clic_cfg_use_function_get_orders_status_name($orders_status_id, $language_id = '')
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

    $Qstatus = $CLICSHOPPING_Db->get('orders_status', 'orders_status_name', ['orders_status_id' => (int)$orders_status_id, 'language_id' => $language_id]);

    return $Qstatus->value('orders_status_name');
  }