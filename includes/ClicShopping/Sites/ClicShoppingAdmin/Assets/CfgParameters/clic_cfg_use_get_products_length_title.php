<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\Registry;

/**
 *
 * return class title
 * @param int
 * @return string $orders_status['orders_status_name'],  name of the status
 *
 *
 */

function clic_cfg_use_get_products_length_title($id)
{

  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Db = Registry::get('Db');

  $Qweight_title = $CLICSHOPPING_Db->get('products_length_classes', 'products_length_class_title', ['products_length_class_id' => (int)$id,
      'language_id' => $CLICSHOPPING_Language->getId()
    ]
  );

  return $Qweight_title->value('products_length_class_title');
}
