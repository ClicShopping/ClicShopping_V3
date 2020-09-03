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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  /**
   * The Address class handles address related functions such as the format and country and zone information
   */
  class Address
  {
    protected $db;

    public function __construct()
    {
      $this->db = Registry::get('Db');
    }

    /*
    * Return a formatted address
    *  TABLES: address_format
    */
    public static function addressFormat($address_format_id, $address, $html, $boln, $eoln)
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (!empty($CLICSHOPPING_Customer)) {
        $customer_group_id = $CLICSHOPPING_Customer->getCustomersGroupID();
      } else {
        $customer_group_id = 0;
      }

      $Qformat = Registry::get('Db')->get('address_format', 'address_format', ['address_format_id' => (int)$address_format_id]);

      $replace = [
        '$company' => HTML::outputProtected($address['company']),
        '$firstname' => '',
        '$lastname' => '',
        '$street' => HTML::outputProtected($address['street_address']),
        '$suburb' => HTML::outputProtected($address['suburb']),
        '$city' => HTML::outputProtected($address['city']),
        '$state' => HTML::outputProtected($address['state']),
        '$postcode' => HTML::outputProtected($address['postcode']),
        '$country' => ''
      ];

      if (isset($address['firstname']) && !empty($address['firstname'])) {
        $replace['$firstname'] = HTML::outputProtected($address['firstname']);
        $replace['$lastname'] = HTML::outputProtected($address['lastname']);
      } elseif (isset($address['name']) && !empty($address['name'])) {
        $replace['$firstname'] = HTML::outputProtected($address['name']);
      }

      if (isset($address['country_id']) && !empty($address['country_id'])) {
        $replace['$country'] = self::getCountryName($address['country_id']);

        if (isset($address['zone_id']) && !empty($address['zone_id'])) {
          $replace['$state'] = static::getZoneName($address['country_id'], $address['zone_id'], $replace['$state']);
        }
      } elseif (isset($address['country']) && !empty($address['country'])) {
        if (CLICSHOPPING::getSite() === 'ClicShoppingAdmin') {
          $replace['$country'] = HTML::outputProtected($address['country']);
        } else {
          $replace['$country'] = HTML::outputProtected($address['country']['title']); // bug osc à tester
        }
      }

      $replace['$zip'] = $replace['$postcode'];

      if ($html) {
// HTML Mode
        $HR = '<hr />';
        $hr = '<hr />';
        if (($boln == '') && ($eoln == "\n")) { // Values not specified, use rational defaults
          $CR = '<br />';
          $cr = '<br />';
          $eoln = $cr;
        } else { // Use values supplied
          $CR = $eoln . $boln;
          $cr = $CR;
        }
      } else {
// Text Mode
        $CR = $eoln;
        $cr = $CR;
        $HR = '----------------------------------------';
        $hr = '----------------------------------------';
      }

      $replace['$CR'] = $CR;
      $replace['$cr'] = $cr;
      $replace['$HR'] = $HR;
      $replace['$hr'] = $hr;

      $replace['$statecomma'] = '';
      $replace['$streets'] = $replace['$street'];

      if ($replace['$suburb'] != '') $replace['$streets'] = $replace['$street'] . $replace['$cr'] . $replace['$suburb'];
      if ($replace['$state'] != '') $replace['$statecomma'] = $replace['$state'] . ', ';

      $address = strtr($Qformat->value('address_format'), $replace);

      if ((($customer_group_id == 0) && (ACCOUNT_COMPANY == 'true') && (!empty($replace['$company']))) || (($customer_group_id != 0) && (ACCOUNT_COMPANY_PRO == 'true') && (!is_null($replace['$company'])))) {
        $address = $replace['$company'] . $replace['$cr'] . $address;
      }

      return $address;
    }

    /**
     * Returns the address_format_id for the given country
     * @param $country_id
     * @return int
     */

    public static function getAddressFormatId(int $country_id) :int
    {

      $format_id = 1;

      $Qformat = Registry::get('Db')->get('countries', 'address_format_id', ['countries_id' => (int)$country_id]);

      if ($Qformat->fetch() !== false) {
        $format_id = $Qformat->valueInt('address_format_id');
      }

      return $format_id;
    }


    /**
     * Return the zone code
     *
     * @param int $id The ID of the zone
     *
     * @return string
     */

    public static function getZoneCode(int $country_id, int $zone_id, string $default_zone) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qzone = $CLICSHOPPING_Db->prepare('select zone_code
                                          from :table_zones
                                          where zone_country_id = :zone_country_id
                                          and zone_id = :zone_id
                                          and zone_status = 0
                                         ');

      $Qzone->bindInt(':zone_country_id', (int)$country_id);
      $Qzone->bindInt(':zone_id', (int)$zone_id);

      $Qzone->execute();

      if ($Qzone->fetch() !== false) {
        return $Qzone->value('zone_code');
      } else {
        return $default_zone;
      }
    }

    /**
     * Return the zone name
     * @param int $country_id
     * @param int $zone_id
     * @param null $default_zone
     * @return string
     */
    public static function getZoneName($country_id, $zone_id = null, $default_zone = null)
    {

      if (!is_null($zone_id)) {
        $Qzone = Registry::get('Db')->get('zones', 'zone_name', ['zone_country_id' => (int)$country_id,
                'zone_id' => (int)$zone_id,
                'zone_status' => 0
            ]
        );

        if ($Qzone->fetch() !== false) {
          return $Qzone->value('zone_name');
        } else {
          return $default_zone;
        }
      } elseif (is_null($default_zone)) {
        $Qzone = Registry::get('Db')->get('zones', 'zone_name', ['zone_country_id' => (int)$country_id,
              'zone_name' => $default_zone,
              'zone_status' => 0
            ]
        );

        if ($Qzone->fetch() !== false) {
          return $Qzone->value('zone_name');
        } else {
          return $default_zone;
        }
      } else {
         return $default_zone;
      }
    }

    /**
     *  Returns an array with countries
     * @param string $countries_id
     * @param string $with_iso_codes
     *
     */

    public static function getCountries($countries_id = null, bool $with_iso_codes = false) :array
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $countries_array = [];

      if (!is_null($countries_id)) {
        if ($with_iso_codes === true) {
          $Qcountries = $CLICSHOPPING_Db->prepare('select countries_name,
                                                          countries_iso_code_2,
                                                          countries_iso_code_3
                                                   from :table_countries
                                                   where countries_id = :countries_id
                                                   and status = 1
                                                   order by countries_name
                                                  ');
          $Qcountries->bindInt(':countries_id', $countries_id);
          $Qcountries->execute();

          $countries_array = $Qcountries->toArray();
        } else {

          $Qcountries = $CLICSHOPPING_Db->prepare('select countries_name
                                                   from :table_countries
                                                   where countries_id = :countries_id
                                                   and status = 1
                                                  ');
          $Qcountries->bindInt(':countries_id', $countries_id);
          $Qcountries->execute();

          $countries_array = $Qcountries->toArray();
        }
      } else {
        $countries_array = $CLICSHOPPING_Db->query('select countries_id,
                                                          countries_name,
                                                          countries_iso_code_2
                                                   from :table_countries
                                                   where status = 1
                                                    order by countries_name
                                                  ')->fetchAll();
      }

      return $countries_array;
    }

    /**
     * Retour the name of the country
     * @param $country_id
     *
     */
    public static function getCountryName(int $country_id) :string
    {
      $country_array = self::getCountries($country_id);

      return $country_array['countries_name'];

    }

    /**
     *  Alias function to getCountries, which also returns the countries iso codes
     * @param string $countries_id
     *
     */
    public function getCountriesWithIsoCodes(int $countries_id)
    {
      return static::getCountries($countries_id, true);
    }

    /**
     * Return the zones belonging to a country, or all zones
     *
     * @param int $id The ID of the country
     *
     * @return array
     */

    public static function getZones($id = null) :array
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $zones_array = [];

      $sql_query = 'select z.zone_id,
                           z.zone_country_id,
                           z.zone_name,
                           z.zone_status,
                           c.countries_name
                    from :table_zones z,
                         :table_countries c
                    where';

      if (!empty($id)) {
        $sql_query .= ' z.zone_country_id = :zone_country_id and';
      }

      $sql_query .= ' z.zone_country_id = c.countries_id
                      and z.zone_status = 0
                      order by c.countries_name,
                                z.zone_name';

      if (!empty($id)) {
        $Qzones = $CLICSHOPPING_Db->prepare($sql_query);
        $Qzones->bindInt(':zone_country_id', $id);
      } else {
        $Qzones = $CLICSHOPPING_Db->query($sql_query);
      }

      $Qzones->execute();

      while ($Qzones->fetch()) {
        $zones_array[] = ['id' => $Qzones->valueInt('zone_id'),
          'name' => $Qzones->value('zone_name'),
          'country_id' => $Qzones->valueInt('zone_country_id'),
          'country_name' => $Qzones->value('countries_name')
        ];

        return $zones_array;
      }
    }

    /**
     * Get the country zone
     *
     * @param $country_id , if ogf the country
     * @return array $zones_array, zone of the country
     *
     * Shop and Admin
     */
    public static function getCountryZones($country_id)
    {
      $zones_array = [];

      $Qzones = Registry::get('Db')->get('zones', [
        'zone_id',
        'zone_name'
      ], [
        'zone_country_id' => (int)$country_id,
        'zone_status' => 0
      ], 'zone_name'
      );

      while ($Qzones->fetch()) {
        $zones_array[] = [
          'id' => $Qzones->valueInt('zone_id'),
          'text' => $Qzones->value('zone_name')
        ];
      }

      return $zones_array;
    }

    /**
     * Get pull down of the country zone
     *
     * @param $country_id , if ogf the country
     * @return array $zones_array, zone of the country
     *
     * Shop and Admin
     */
    public static function getPrepareCountryZonesPullDown($country_id = '') :array
    {
      $zones = self::getCountryZones($country_id);

      if (count($zones) > 0) {
        $zones_select = array(['id' => '',
            'text' => CLICSHOPPING::getDef('text_selected')
          ]
        );
        $zones = array_merge($zones_select, $zones);

      } else {
        $zones = array(['id' => '',
            'text' => CLICSHOPPING::getDef('text_selected')
          ]
        );
      }

      return $zones;
    }

    /**
     * Get all zone for one country
     * @param int $country_id
     * @return string
     */
    public function getAllZones(int $country_id)
    {
      $Qcheck = $this->db->prepare('select zone_name
                                     from :table_zones
                                     where zone_country_id = :zone_country_id
                                     and zone_status = 0
                                     order by zone_name
                                    ');
      $Qcheck->bindInt(':zone_country_id', $country_id);
      $Qcheck->execute();

      if ($Qcheck->rowCount() > 1) {
        while ($Qcheck->fetch()) {
          $zones_array[] = ['id' => $Qcheck->value('zone_name'),
              'text' => $Qcheck->value('zone_name')
          ];
        }

        return $zones_array;
      }

      return false;
    }


    /**
     * Display zone states
     * @param int $country_id
     * @return string
     */
    public function getZoneDropdown($country_id) :string
    {
      $zones_array = $this->getAllZones($country_id);

      if ($zones_array !== false) {
        $result = HTML::selectMenu('state', $zones_array, 'id="inputState" aria-describedby="atState"');
      } else {
        $result = HTML::inputField('state', '', 'id="atState" placeholder="' . CLICSHOPPING::getDef('entry_state') . '" aria-required="true" aria-describedby="atState"');
      }

      return $result;
    }

    /**
     * Check if a zone exist
     * @param int $country
     * @param null $zone_id
     * @return bool
     */
    public function checkZoneCountry(int $country, $zone_id = null)
    {
      if (is_null($zone_id)) {
        $Qcheck = $this->db->prepare('select zone_id
                                     from :table_zones
                                     where zone_country_id = :zone_country_id
                                     and zone_status = 0
                                     limit 1
                                     ');
        $Qcheck->bindInt(':zone_country_id', $country);
        $Qcheck->execute();
      } else {
        if (is_numeric($zone_id)) {
          $Qcheck = $this->db->prepare('select zone_id
                                       from :table_zones
                                       where zone_country_id = :zone_country_id
                                       and zone_id = :zone_id
                                       and zone_status = 0
                                       limit 1
                                       ');
          $Qcheck->bindInt(':zone_country_id', $country);
          $Qcheck->bindInt(':zone_id', $zone_id);
          $Qcheck->execute();
        } else {
          $Qcheck = $this->db->prepare('select zone_id
                                       from :table_zones
                                       where zone_country_id = :zone_country_id
                                       and (zone_name = :zone_name or zone_code = :zone_code)
                                       and zone_status = 0
                                       limit 1
                                       ');
          $Qcheck->bindInt(':zone_country_id', $country);
          $Qcheck->bindValue(':zone_name', $zone_id);
          $Qcheck->bindValue(':zone_code', $zone_id);
          $Qcheck->execute();
        }
      }

      $result = $Qcheck->valueInt('zone_id');

      return $result;
    }

    /**
     * Check the zone by country and State
     * @param int $country_id
     * @param $state
     * @return
     */
    public function checkZoneByCountryState(int $country_id, $state = '')
    {

      $Qzone = $this->db->prepare('select zone_id
                                   from :table_zones
                                   where zone_country_id = :zone_country_id
                                   and zone_status = 0
                                 ');

      $Qzone->bindInt(':zone_country_id', $country_id);
      $Qzone->execute();
      $Qzone->fetch();

      $all_zone = $Qzone->fetchAll();
      $count = count($all_zone);

      if ($count > 0 && !empty($state) && !is_numeric($state)) {
        $Qzone = $this->db->prepare('select distinct zone_id
                                     from :table_zones
                                     where zone_country_id = :zone_country_id
                                     and (zone_name = :zone_name or zone_code = :zone_code)
                                     and zone_status = 0
                                   ');

        $Qzone->bindInt(':zone_country_id', $country_id);
        $Qzone->bindValue(':zone_name', $state);
        $Qzone->bindValue(':zone_code', $state);
        $Qzone->execute();
        $Qzone->fetch();

        $zone_id = $Qzone->value('zone_id');

      } elseif (ACCOUNT_STATE_DROPDOWN == 'true' && $state > 0) {
        $Qzone = $this->db->prepare('select distinct zone_id
                                     from :table_zones
                                     where zone_country_id = :zone_country_id
                                     and zone_id = :zone_id
                                     and zone_status = 0
                                   ');

        $Qzone->bindInt(':zone_country_id', $country_id);
        $Qzone->bindValue(':zone_id', $state);
        $Qzone->execute();
        $Qzone->fetch();

        $zone_id = $Qzone->value('zone_id');
      } else {
        $zone_id = false;
      }

      return $zone_id;
    }
  }

