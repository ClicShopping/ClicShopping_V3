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

/**
 * the clic_cfg_use_function_get_country_name name
 *
 * @param string  $orders_status_id, $language_id
 * @return string $orders_status['orders_status_name'],  name of the status
 * @access public
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  /**
 * Country name name
 *
 * @param string country_id
 * @return string $country['countries_name'] the country name
 * @access public
 * osc_get_country_name
 */

  function clic_cfg_use_function_get_country_name($country_id) {

    $Qcountry = Registry::get('Db')->get('countries', 'countries_name', ['countries_id' => (int)(int)$country_id],
                                                                        ['status' => 1]
                                        );


    if ( $Qcountry->fetch() === false) {
      return $country_id;
    } else {
      return $Qcountry->value('countries_name');
    }
  }