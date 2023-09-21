<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . '/');

require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

CLICSHOPPING::initialize();

CLICSHOPPING::loadSite('ClicShoppingAdmin');

$CLICSHOPPING_Db = Registry::get('Db');

if (isset($_GET['q'])) {
  $terms = HTML::sanitize(mb_strtolower($_GET['q']));

  $Qcheck = $CLICSHOPPING_Db->prepare('select distinct suppliers_id as id,
                                                         suppliers_name as name
                                       from :table_suppliers
                                       where suppliers_name LIKE :terms
                                       limit 10;
                                      ');
  $Qcheck->bindValue(':terms', '%' . $terms . '%');
  $Qcheck->execute();

  $list = $Qcheck->rowCount();

  if ($list > 0) {
    $array = [];

    while ($value = $Qcheck->fetch()) {
      $array[] = $value;
    }

# JSON-encode the response
    $json_response = json_encode($array); //Return the JSON Array

# Return the response
    echo $json_response;
  }
}
