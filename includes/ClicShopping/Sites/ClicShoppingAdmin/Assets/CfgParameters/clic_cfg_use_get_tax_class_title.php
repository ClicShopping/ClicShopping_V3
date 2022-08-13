<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  function clic_cfg_use_get_tax_class_title($id)
  {

    $CLICSHOPPING_Db = Registry::get('Db');

    if ($id < 1) {
      return CLICSHOPPING::getDef('text_none');
    } else {

      $Qclass = $CLICSHOPPING_Db->prepare('select tax_class_title 
                                     from :table_tax_class 
                                     where tax_class_id = :tax_class_id
                                   ');
      $Qclass->bindInt(':tax_class_id', $id);
      $Qclass->execute();

      return $Qclass->value('tax_class_title');
    }
  }