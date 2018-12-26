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
  use ClicShopping\OM\CLICSHOPPING;

/**
 * the status name
 *
 * @param string  $orders_status_id, $language_id
 * @return string $orders_status['orders_status_name'],  name of the status
 * @access public
 *
 */

  function clic_cfg_use_get_order_status_title($id) {

    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    if ( $id < 1 ) {
      return CLICSHOPPING::getDef('text_default');
    } else {

      $Qstatus = $CLICSHOPPING_Db->get('orders_status', 'orders_status_name', ['orders_status_id' => (int)$id,
                                                                        'language_id' =>  $CLICSHOPPING_Language->getId()
                                                                       ]
                                        );

      return $Qstatus->value('orders_status_name');
    }
  }
