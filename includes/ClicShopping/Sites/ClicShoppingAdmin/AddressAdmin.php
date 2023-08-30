<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class AddressAdmin extends \ClicShopping\Sites\Shop\Address
{
  /**
   * Get the country zone
   *
   * @param $country_id , if ogf the country
   * @return array $zones_array, zone of the country
   *
   */
  public static function getCountryZones($country_id)
  {
    $zones_array = parent::getCountryZones($country_id);

    return $zones_array;
  }

  /**
   * Get the address format
   *
   * @param
   * @return array $address_format_array, list of address_format_id's
   *
   */

  public static function getAddressFormats(): array
  {
    $address_format_array = [];

    $Qaddress = Registry::get('Db')->get('address_format', 'address_format_id', null, 'address_format_id');

    while ($Qaddress->fetch()) {
      $address_format_array[] = ['id' => $Qaddress->valueInt('address_format_id'),
        'text' => $Qaddress->valueInt('address_format_id')
      ];
    }

    return $address_format_array;
  }

  /**
   * Set the address format
   *
   * @param
   * @return array $address_format_array, list of address_format_id's
   *
   *
   */
  public static function setAddressFormatArrayAdmin(): array
  {
    $address_format_array = ['company' => CLICSHOPPING::getDef('text_address_company'),
      'firstname' => CLICSHOPPING::getDef('text_address_first_name'),
      'lastname' => CLICSHOPPING::getDef('text_address_last_name'),
      'street_address' => CLICSHOPPING::getDef('text_address_street_address'),
      'suburb' => CLICSHOPPING::getDef('text_address_suburb'),
      'city' => CLICSHOPPING::getDef('text_address_city'),
      'state' => CLICSHOPPING::getDef('text_address_state'),
      'postcode' => CLICSHOPPING::getDef('text_address_postcode'),
      'country' => CLICSHOPPING::getDef('text_address_country')
    ];

    return $address_format_array;
  }

  /**
   * get the address format
   *
   * @param $address_format_id ; id of the address
   * @return address format
   *
   *
   */
  public static function getAddressFormatRadio(int $address_format_id)
  {
    return parent::addressFormat($address_format_id, AddressAdmin::setAddressFormatArrayAdmin(), true, '', '<br />');
  }

  /**
   * Get the geo zone name of the country
   *
   * @param string $geo_zone_id , if of the geo zone
   * @return string $geo_zone_name the drop down of the zone name
   *
   */
  public static function getGeoZoneName(int $geo_zone_id): ?int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qzones = $CLICSHOPPING_Db->prepare('select geo_zone_name
                                           from :table_geo_zones
                                           where geo_zone_id = :geo_zone_id
                                           ');
    $Qzones->bindInt(':geo_zone_id', (int)$geo_zone_id);
    $Qzones->execute();

    if ($Qzones->fetch() === false) {
      $geo_zone_name = $geo_zone_id;
    } else {
      $geo_zone_name = $Qzones->value('geo_zone_name');
    }

    return $geo_zone_name;
  }

  /**
   * Drop down of the geo name
   *
   * @param string $parameters , $selected
   * @return string $select_string, the drop down of the zone name
   *
   *
   */
  public static function getGeoZonesPullDown(string $parameters, string $selected = ''): string
  {
    $select_string = '<select name="' . $parameters . '" id="' . $parameters . '">';

    $Qzones = Registry::get('Db')->get('geo_zones', ['geo_zone_id',
      'geo_zone_name'
    ],
      null,
      'geo_zone_name'
    );

    while ($Qzones->fetch()) {
      $select_string .= '<option value="' . $Qzones->valueInt('geo_zone_id') . '"';

      if ($selected == $Qzones->valueInt('geo_zone_id')) {
        $select_string .= ' SELECTED';
      }

      $select_string .= '>' . $Qzones->value('geo_zone_name') . '</option>';
    }

    $select_string .= '</select>';

    return $select_string;
  }

  /**
   * Get the address of customer id
   *
   * @param $id , id of the customer
   * @return array $QaddressesBook, list of address_format_id's
   *
   */
  public static function getListingAdmin(int $id): mixed
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QaddressesBook = $CLICSHOPPING_Db->prepare('select address_book_id,
                                                         entry_firstname as firstname,
                                                         entry_lastname as lastname,
                                                         entry_company as company,
                                                         entry_street_address as street_address,
                                                         entry_suburb as suburb,
                                                         entry_city as city,
                                                         entry_postcode as postcode,
                                                         entry_state as state,
                                                         entry_zone_id as zone_id,
                                                         entry_country_id as country_id ,
                                                         entry_telephone as telephone
                                                  from :table_address_book
                                                  where customers_id = :customers_id
                                                  order by address_book_id
                                                  ');
    $QaddressesBook->bindInt(':customers_id', (int)$id);
    $QaddressesBook->execute();

    return $QaddressesBook;
  }
}