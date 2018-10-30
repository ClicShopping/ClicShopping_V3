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

  if (isset($_REQUEST['q'])) {
    $terms = strtolower($_GET["q"]);

    $Qcheck = $CLICSHOPPING_Db->prepare('select distinct manufacturers_id as id,
                                                         manufacturers_name as name
                                        from :table_manufacturers
                                        where manufacturers_name LIKE :terms
                                        limit 10
                                      ');
    $Qcheck->bindValue(':terms', '%' . $terms . '%');
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
