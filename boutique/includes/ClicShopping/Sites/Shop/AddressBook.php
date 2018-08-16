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


  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;

  class AddressBook {

/*
* Return a pecific customer address
* @param : $id customer_id
* @param : $address_book_id , id of address book
* @return array $Qaddresses
* public
*/
    public static function getAddressCustomer($id = null, $address_book_id) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (is_null($id)) {
        $CLICSHOPPING_Customer->getID();
      }

      $Qaddress =$CLICSHOPPING_Db->prepare('select address_book_id,
                                            entry_firstname as firstname,
                                            entry_lastname as lastname,
                                            entry_company as company,
                                            entry_street_address as street_address,
                                            entry_suburb as suburb,
                                            entry_city as city,
                                            entry_postcode as postcode,
                                            entry_state as state,
                                            entry_zone_id as zone_id,
                                            entry_country_id as country_id
                                       from :table_address_book
                                       where customers_id = :customers_id
                                       and address_book_id = :address_book_id
                                       order by firstname, lastname
                                      ');
      $Qaddress->bindInt(':address_book_id', $address_book_id);
      $Qaddress->bindInt(':customers_id', $id);

      $Qaddress->execute();

      $address = $Qaddress->fetch();

      return $address;
    }

////
// Return a formatted address
// TABLES: customers, address_book
//  osc_address_label
    public static function addressLabel($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Address = Registry::get('Address');

      if (is_array($address_id) && !empty($address_id)) {
        return $CLICSHOPPING_Address->addressFormat($address_id['address_format_id'], $address_id, $html, $boln, $eoln);
      }

      $Qaddress = $CLICSHOPPING_Db->prepare('select entry_firstname as firstname,
                                              entry_lastname as lastname,
                                              entry_company as company,
                                              entry_street_address as street_address,
                                              entry_suburb as suburb, entry_city as city,
                                              entry_postcode as postcode,
                                              entry_state as state,
                                              entry_zone_id as zone_id,
                                              entry_country_id as country_id
                                       from :table_address_book
                                       where customers_id = :customers_id
                                       and address_book_id = :address_book_id
                                     ');

      $Qaddress->bindInt(':address_book_id', $address_id);
      $Qaddress->bindInt(':customers_id', $customers_id);

      $Qaddress->execute();

      $format_id = $CLICSHOPPING_Address->getAddressFormatId($Qaddress->valueInt('country_id'));

      return $CLICSHOPPING_Address->addressFormat($format_id, $Qaddress->toArray(), $html, $boln, $eoln);
    }


/**
 * Controle autorisation au client de modifier son adresse par defaut
 * osc_count_customers_modify_address_default
 */

    public static function countCustomersModifyAddressDefault($id = '', $check_session = true) {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id) === false) {
        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $id = $CLICSHOPPING_Customer->getID();
        } else {
          return 0;
        }
      }

      if ($check_session === true) {
        if ( !$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID()) ) {
          return 0;
        }
      }

      if (ACCOUNT_MODIFY_ADRESS_DEFAULT_PRO == 'true' || $CLICSHOPPING_Customer->getCustomersGroupID() == '0' ) {

        $QcustomersModifyAddressDefault = $CLICSHOPPING_Db->prepare('select customers_modify_address_default
                                                             from :table_customers
                                                             where customers_id = :customers_id
                                                            ');
        $QcustomersModifyAddressDefault->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID() );

        $QcustomersModifyAddressDefault->execute();
        $customers_modify_address_default = $QcustomersModifyAddressDefault->fetch();

      }

      return $customers_modify_address_default['customers_modify_address_default'];
    }

/**
 * Controle autorisation d'ajouter une adresse selon la fiche client
 * osc_count_customers_add_address
 */

    public static function countCustomersAddAddress($id = null, $check_session = true) {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id) === false) {
        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $id = $CLICSHOPPING_Customer->getID();
        } else {
          return 0;
        }
      }

      if ($check_session === true) {
        if ( !$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID()) ) {
          return 0;
        }
      }

      if ( $CLICSHOPPING_Customer->getCustomersGroupID() == 0 || ACCOUNT_ADRESS_BOOK_PRO == 'true') {
        $Qaddresses = $CLICSHOPPING_Db->prepare('select customers_add_address
                                           from :table_customers
                                           where customers_id = :customers_id
                                          ');
        $Qaddresses->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID() );

        $Qaddresses->execute();
        $customers_add_address = $Qaddresses->fetch();
      }

      return $customers_add_address['customers_add_address'];
    }

/**
 * Controle autorisation au client B2B de modifier ses informations sur la societe
 * osc_count_customers_modify_company
 */

    public static function countCustomersModifyCompany($id = '', $check_session = true) {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id) === false) {
        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $id = $CLICSHOPPING_Customer->getID();
        } else {
          return 0;
        }
      }

      if ($check_session === true) {
        if ( !$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID()) ) {
          return 0;
        }
      }

      $QcustomersModifyCompany = $CLICSHOPPING_Db->prepare('select customers_modify_company
                                                      from :table_customers
                                                      where customers_id = :customers_id
                                                    ');
      $QcustomersModifyCompany->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID() );

      $QcustomersModifyCompany->execute();
      $customers_modify_company = $QcustomersModifyCompany->fetch();

      return $customers_modify_company['customers_modify_company'];
    }

/**
 * Verify the address book entry belongs to the current customer
 *
 * @param int $id The ID of the address book entry to verify
 * @access public
 * @return boolean
 */

    public static function checkEntry($id) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qentry = $CLICSHOPPING_Db->prepare('select address_book_id
                                      from :table_address_book
                                      where address_book_id = :address_book_id
                                      and customers_id = :customers_id
                                    ');
      $Qentry->bindInt(':address_book_id', $id);
      $Qentry->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qentry->execute();

      return ( $Qentry->fetch() !== false );
    }



/**
 * Delete an address book entry
 *
 * @param int $id The ID of the address book entry to delete
 * @access public
 * @return boolean
 */

    public static function deleteEntry($id) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_address_book
                                      where address_book_id = :address_book_id
                                      and customers_id = :customers_id
                                     ');
      $Qdelete->bindInt(':address_book_id', $id);
      $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qdelete->execute();

      return ( $Qdelete->rowCount() === 1 );
    }


/**
 * count customer address book
 * @param string $id, $check_session
 * @param string $addresses['total'], number of the address
 * @access public
 * osc_count_customer_address_book_entries
 */
    public static function countCustomerAddressBookEntries($id = '', $check_session = true) {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id) === false) {
        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $id = $CLICSHOPPING_Customer->getID();
        } else {
          return 0;
        }
      }

      if ($check_session === true) {
        if ( !$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID()) ) {
          return 0;
        }
      }

      $Qaddresses = $CLICSHOPPING_Db->prepare('select count(*) as total
                                       from :table_address_book
                                       where customers_id = :customers_id
                                       limit 1
                                      ');
      $Qaddresses->bindInt(':customers_id', (int)$id);

      $Qaddresses->execute();

      if ($Qaddresses->fetch() !== false) {
        return $Qaddresses->valueInt('total');
      }

      return 0;
    }

/**
* Count customer_order
* @param string $id The id of the order
* @param string $check_session of the session customer
* @access public
* osc_count_ustomer_orders
*/

    public static function countCustomerOrders($id = '', $check_session = true) {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (is_numeric($id) === false) {
        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $id = $CLICSHOPPING_Customer->getID();
        } else {
          return 0;
        }
      }

      if ($check_session === true) {
        if ( !$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID()) ) {
          return 0;
        }
      }

      $Qorders = $CLICSHOPPING_Db->prepare('select count(*) as total
                                      from :table_orders o,
                                           :table_orders_status s
                                      where o.customers_id = :customers_id
                                      and o.orders_status = s.orders_status_id
                                      and s.language_id = :language_id
                                      and s.public_flag = :public_flag
                                    ');
      $Qorders->bindInt(':customers_id', (int)$id);
      $Qorders->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId());
      $Qorders->bindValue(':public_flag', '1');

      $Qorders->execute();

      if ($Qorders->fetch() !== false) {
        return $Qorders->valueInt('total');
      }

      return 0;
    }

/**
 * Returns the address book entries for the current customer
 *
 * @access public
 * @return array
 */

    public static function getListing() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qaddresses = $CLICSHOPPING_Db->prepare('select ab.address_book_id,
                                                ab.entry_firstname as firstname,
                                                 ab.entry_lastname as lastname,
                                                 ab.entry_company as company,
                                                 ab.entry_street_address as street_address,
                                                 ab.entry_suburb as suburb,
                                                 ab.entry_city as city,
                                                 ab.entry_postcode as postcode,
                                                 ab.entry_state as state,
                                                 ab.entry_zone_id as zone_id,
                                                 ab.entry_country_id as country_id,
                                                 z.zone_code as zone_code,
                                                 c.countries_name as country_title
                                        from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id),
                                              :table_countries c
                                        where ab.customers_id = :customers_id
                                        and ab.entry_country_id = c.countries_id
                                        order by ab.entry_firstname,
                                                 ab.entry_lastname
                                       ');
      $Qaddresses->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qaddresses->execute();

      return $Qaddresses;
    }


/**
 * Returns a specific address book entry for the current customer
 *
 * @param int $id The ID of the address book entry to return
 * @access public
 * @return array
 */

    public static function getEntry($id) {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qentry = $CLICSHOPPING_Db->prepare('select entry_gender as gender,
                                           entry_company as company,
                                           entry_firstname as firstname,
                                           entry_lastname as lastname,
                                           entry_street_address as street_address,
                                           entry_suburb as suburb,
                                           entry_postcode as postcode,
                                           entry_city as city,
                                           entry_state as state,
                                           entry_zone_id as zone_id,
                                           entry_country_id as country_id,
                                           entry_telephone as telephone
                                     from :table_address_book
                                     where address_book_id = :address_book_id
                                     and customers_id = :customers_id
                                     ');
      $Qentry->bindInt(':address_book_id', $id);
      $Qentry->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qentry->execute();

      return $Qentry->toArray();
    }


/**
 * Return the number of address book entries the current customer has
 *
 * @access public
 * @return integer
 */
/*
    public static function numberOfEntries() {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db =Registry::get('Db');
//      $CLICSHOPPING_Customer = Registry::get('Customer');

      static $total_entries;

      if ( !isset($total_entries) ) {
        $total_entries = 0;

        if ( $CLICSHOPPING_Customer->isLoggedOn() ) {
          $Qaddresses = $CLICSHOPPING_Db->prepare('select count(*) as total
                                            from :table_address_book
                                            where customers_id = :customers_id');
          $Qaddresses->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qaddresses->execute();

          $total_entries = $Qaddresses->valueInt('total');
        }
      }

      return $total_entries;
    }
*/
/**
 * Save an address book entry
 *
 * @param array $data An array containing the address book information
 * @param int $id The ID of the address book entry to update (if this is not provided, a new address book entry is created)
 * @access public
 * @return boolean
 */
/*
    public static function saveEntry($data, $id = '') {
      $CLICSHOPPING_Db =Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $updated_record = false;

      if ( is_numeric($id) ) {
        $Qab = $CLICSHOPPING_Db->prepare('update :table_address_book
                                    set customers_id = :customers_id,
                                        entry_gender = :entry_gender,
                                        entry_company = :entry_company,
                                        entry_firstname = :entry_firstname,
                                        entry_lastname = :entry_lastname,
                                        entry_street_address = :entry_street_address,
                                        entry_suburb = :entry_suburb,
                                        entry_postcode = :entry_postcode,
                                        entry_city = :entry_city,
                                        entry_state = :entry_state,
                                        entry_country_id = :entry_country_id,
                                        entry_zone_id = :entry_zone_id,
                                        entry_telephone = :entry_telephone,
                                        entry_fax = :entry_fax
                                    where address_book_id = :address_book_id
                                    and customers_id = :customers_id
                                   ');
        $Qab->bindInt(':address_book_id', $id);
        $Qab->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      } else {
        $Qab = $CLICSHOPPING_Db->prepare('insert into :table_address_book (customers_id,
                                                                      entry_gender,
                                                                      entry_company,
                                                                      entry_firstname,
                                                                      entry_lastname,
                                                                      entry_street_address,
                                                                      entry_suburb,
                                                                      entry_postcode,
                                                                      entry_city,
                                                                      entry_state,
                                                                      entry_country_id,
                                                                      entry_zone_id,
                                                                      entry_telephone,
                                                                      entry_fax)
                                    values (:customers_id,
                                            :entry_gender,
                                            :entry_company,
                                            :entry_firstname,
                                            :entry_lastname,
                                            :entry_street_address,
                                            :entry_suburb,
                                            :entry_postcode,
                                            :entry_city,
                                            :entry_state,
                                            :entry_country_id,
                                            :entry_zone_id,
                                            :entry_telephone,
                                            :entry_fax)
                                    ');
      }

      $Qab->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qab->bindValue(':entry_gender', ((ACCOUNT_GENDER > -1) && isset($data['gender']) && (($data['gender'] == 'm') || ($data['gender'] == 'f'))) ? $data['gender'] : '');
      $Qab->bindValue(':entry_company', (ACCOUNT_COMPANY > -1) ? $data['company'] : '');
      $Qab->bindValue(':entry_firstname', $data['firstname']);
      $Qab->bindValue(':entry_lastname', $data['lastname']);
      $Qab->bindValue(':entry_street_address', $data['street_address']);
      $Qab->bindValue(':entry_suburb', (ACCOUNT_SUBURB > -1) ? $data['suburb'] : '');
      $Qab->bindValue(':entry_postcode', (ACCOUNT_POST_CODE > -1) ? $data['postcode'] : '');
      $Qab->bindValue(':entry_city', $data['city']);
      $Qab->bindValue(':entry_state', (ACCOUNT_STATE > -1) ? ((isset($data['zone_id']) && ($data['zone_id'] > 0)) ? '' : $data['state']) : '');
      $Qab->bindInt(':entry_country_id', $data['country']);

      if ( isset($data['zone_id']) && is_numeric($data['zone_id']) ) {
        $Qab->bindInt(':entry_zone_id', $data['zone_id']);
      } else {
        $Qab->bindNull(':entry_zone_id');
      }

      $Qab->bindValue(':entry_telephone', (ACCOUNT_TELEPHONE > -1) ? $data['telephone'] : '');
      $Qab->bindValue(':entry_fax', (ACCOUNT_FAX > -1) ? $data['fax'] : '');
      $Qab->execute();

      if ( $Qab->rowCount() === 1 ) {
        $updated_record = true;
      }

      if ( isset($data['primary']) && ($data['primary'] === true) ) {
        if ( !is_numeric($id) ) {
          $id = $CLICSHOPPING_Db->lastInsertId();
        }

        if ( self::setPrimaryAddress($id) ) {
          $CLICSHOPPING_Customer->setCountryID($data['country']);
          $CLICSHOPPING_Customer->setZoneID(($data['zone_id'] > 0) ? (int)$data['zone_id'] : '0');
          $CLICSHOPPING_Customer->setDefaultAddressID($id);

          if ( $updated_record === false ) {
            $updated_record = true;
          }
        }
      }

      if ( $updated_record === true ) {
        return true;
      }

      return false;
    }
*/
/**
 * Set the address book entry as the primary address for the current customer
 *
 * @param int $id The ID of the address book entry
 * @access public
 * @return boolean
 */
/*
    public static function setPrimaryAddress($id) {
      $CLICSHOPPING_Db =Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if ( is_numeric($id) && ($id > 0) ) {
        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers
                                        set customers_default_address_id = :customers_default_address_id
                                        where customers_id = :customers_id
                                       ');
        $Qupdate->bindInt(':customers_default_address_id', $id);
        $Qupdate->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qupdate->execute();

        return ( $Qupdate->rowCount() === 1 );
      }

      return false;
    }
*/






  }