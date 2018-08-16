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

  use ClicShopping\OM\Registry;

  require('../includes/application_top.php');

  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');

  if (isset($_REQUEST['q'])) {
    $terms = strtolower($_GET["q"]);

    $language_id = $CLICSHOPPING_Language->getId();

    $Qcheck = $CLICSHOPPING_Db->prepare('select distinct s.specification_id as specificationId,
                                                  sd.name as name,
                                                  sgd.name as groupName,
                                                  sg.specification_group_id as specificationGroupId
                                  from :table_specification s
                                          left join :table_products_specification_group sg ON (s.specification_group_id = sg.specification_group_id )
                                          left join :table_products_specification_group_description sgd on (sg.specification_group_id = sgd.specification_group_id),
                                       :table_specification_description sd
                                  where (sd.name like :terms or sgd.name like :terms)
                                  and s.specification_id = sd.specification_id
                                  and sg.specification_group_id = sgd.specification_group_id 
                                  and sd.language_id = :language_id
                                  and sgd.language_id = :language_id
                                  limit 10
                                ');

    $Qcheck->bindValue(':terms', '%' . $terms . '%');
    $Qcheck->bindInt(':language_id', $language_id);
    $Qcheck->execute();

    $list = $Qcheck->rowCount() ;

    if ($list > 0) {
      $array = [];

      while ($value = $Qcheck->fetch() ) {
        $array[] = $value;
      }

# JSON-encode the response
      $json_response = json_encode($array); //Return the JSON Array

# Return the response
      echo $json_response;
    }
  }

