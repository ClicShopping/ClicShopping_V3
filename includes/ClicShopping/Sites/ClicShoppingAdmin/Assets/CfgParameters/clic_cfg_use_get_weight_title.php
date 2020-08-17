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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  /**
   * the weight title
   *
   * @param int id
   * @return string $orders_status['orders_status_name'],  name of the status
   *
   *
   */

  function clic_cfg_use_get_weight_title($id)
  {

    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qweight_title = $CLICSHOPPING_Db->get('weight_classes', 'weight_class_title', ['weight_class_id' => (int)$id,
        'language_id' => $CLICSHOPPING_Language->getId()
      ]
    );

    return $Qweight_title->value('weight_class_title');
  }
