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

  define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . '/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('ClicShoppingAdmin');

  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');

  $json = [];

  $Qorders = $CLICSHOPPING_Db->prepare('select count(*) AS total, 
                                              SUM(ot.value) AS amount, 
                                              c.countries_iso_code_2 
                                        from :table_orders o,
                                             :table_countries c, 
                                             :table_orders_total ot
                                        where o.orders_status = 3
                                        and o.billing_country = c.countries_name
                                        and ot.class = :class
                                        and o.orders_id = ot.orders_id
                                        group by o.billing_country
                                     ');
  $Qorders->bindValue('class', 'ST');
  $Qorders->execute();

  $results = $Qorders->fetchAll();

  if (is_array($results)) {
    foreach ($results as $result) {
      $json[strtolower($result['countries_iso_code_2'])] = ['total' => $result['total'],
        'amount' => $result['amount'],

      ];
    }
  }

# JSON-encode the response
  $json_response = json_encode($json); //Return the JSON Array

# Return the response
  echo $json_response;
