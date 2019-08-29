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

  define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../../includes/ClicShopping/') . '/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('Shop');
  if (!empty($_POST['country'])) {
    $zones_array = [];

    $country_id = HTML::sanitize($_POST['country']);




    $Qcheck = $CLICSHOPPING_Db->prepare('select zone_name
                                             from :table_zones
                                             where zone_country_id = :zone_country_id
                                             and zone_status = 0
                                             order by zone_name
                                            ');
    $Qcheck->bindInt(':zone_country_id', (int)$country_id);
    $Qcheck->execute();

//    $countryArr =  $Qcheck->fetchToArray();



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