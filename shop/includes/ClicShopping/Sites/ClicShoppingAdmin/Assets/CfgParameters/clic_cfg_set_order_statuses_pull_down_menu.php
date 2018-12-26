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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

//clic_cfg_pull_down_order_status_list
  function clic_cfg_set_order_statuses_pull_down_menu($default, $key = null) {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $statuses_array = array(array('id' => 0,
                                  'text' => CLICSHOPPING::getDef('text_default')
                                 )
                           );

    $Qstatuses = $CLICSHOPPING_Db->prepare('select orders_status_id,
                                             orders_status_name
                                     from :table_orders_status
                                     where language_id = :language_id
                                     order by orders_status_name
                                     ');

    $Qstatuses->bindInt(':language_id', $CLICSHOPPING_Language->getId());
    $Qstatuses->execute();

    while ( $Qstatuses->fetch() ) {
      $statuses_array[] = array('id' => $Qstatuses->valueInt('orders_status_id'),
                                'text' => $Qstatuses->value('orders_status_name'));
    }

    return HTML::selectMenu($name, $statuses_array, $default);
  }
