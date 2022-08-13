<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AddressBook
  {

    /*
    * Return a pecific customer address
    * @param : int|null $id customer_id
    * @param : int|null$address_book_id , id of address book
    * @return array $Qaddresses
    * public
    */
    public static function getAddressCustomer(int|null $customers_id = null, int|null $address_book_id = null)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (\is_null($customers_id)) {
        $customers_id = $CLICSHOPPING_Customer->getID();
      }

      $Qaddress = $CLICSHOPPING_Db->prepare('select address_book_id,
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
      $Qaddress->bindInt(':customers_id', $customers_id);

      $Qaddress->execute();

      $address = $Qaddress->fetch();

      return $address;
    }

    /**
     * Return a formatted address
     * TABLES: customers, address_book
     * @param $customers_id
     * @param int $address_id
     * @param bool $html
     * @param string $boln
     * @param string $eoln
     * @return mixed
     */
    public static function addressLabel(int $customers_id, ?int $address_id = 1, bool $html = false, string $boln = '', string $eoln = "\n")
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Address = Registry::get('Address');

      if (\is_array($address_id) && !empty($address_id)) {
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
     * @param string $id
     * @param bool $check_session
     * @return string
     */
    public static function countCustomersModifyAddressDefault($id = '', bool $check_session = true) :string
    {

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
        if (!$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID())) {
          return 0;
        }
      }

      if (ACCOUNT_MODIFY_ADRESS_DEFAULT_PRO == 'true' || $CLICSHOPPING_Customer->getCustomersGroupID() == '0') {
        $QcustomersModifyAddressDefault = $CLICSHOPPING_Db->prepare('select customers_modify_address_default
                                                                     from :table_customers
                                                                     where customers_id = :customers_id
                                                                    ');
        $QcustomersModifyAddressDefault->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());

        $QcustomersModifyAddressDefault->execute();

        return $QcustomersModifyAddressDefault->value('customers_modify_address_default');
      }
    }

    /**
     *  Controle autorisation d'ajouter une adresse selon la fiche client
     * @param null $id
     * @param bool $check_session
     * @return string|null
     */
    public static function countCustomersAddAddress($id = null, bool $check_session = true) :?string
    {

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
        if (!$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID())) {
          return 0;
        }
      }

      if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 || ACCOUNT_ADRESS_BOOK_PRO == 'true') {
        $Qaddresses = $CLICSHOPPING_Db->prepare('select customers_add_address
                                                 from :table_customers
                                                 where customers_id = :customers_id
                                                ');
        $Qaddresses->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());

        $Qaddresses->execute();
        $customers_add_address = $Qaddresses->fetch();
      }

      return $customers_add_address['customers_add_address'];
    }

    /**
     * Controle autorisation au client B2B de modifier ses informations sur la societe
     * @param string $id
     * @param bool $check_session
     * @return string
     *
     */
    public static function countCustomersModifyCompany($id = '', bool $check_session = true) :string
    {
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
        if (!$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID())) {
          return 0;
        }
      }

      $QcustomersModifyCompany = $CLICSHOPPING_Db->prepare('select customers_modify_company
                                                            from :table_customers
                                                            where customers_id = :customers_id
                                                          ');
      $QcustomersModifyCompany->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());

      $QcustomersModifyCompany->execute();
      $customers_modify_company = $QcustomersModifyCompany->fetch();

      return $customers_modify_company['customers_modify_company'];
    }

    /**
     * Verify the address book entry belongs to the current customer
     * @param int $id
     * @return bool
     */
    public static function checkEntry(int $id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $count = static::countCustomerAddressBookEntries($CLICSHOPPING_Customer->getID());

      if (isset($_GET['newcustomer']) && $count == 1) {
        $Qentry = $CLICSHOPPING_Db->prepare('select address_book_id
                                            from :table_address_book
                                            where customers_id = :customers_id
                                          ');
        $Qentry->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qentry->execute();
      } else {
        $Qentry = $CLICSHOPPING_Db->prepare('select address_book_id
                                            from :table_address_book
                                            where address_book_id = :address_book_id
                                            and customers_id = :customers_id
                                          ');
        $Qentry->bindInt(':address_book_id', $id);
        $Qentry->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qentry->execute();
      }

      return ($Qentry->fetch() !== false);
    }

    /**
     * Delete an address book entry
     * @param int $id
     * @return bool
     */
    public static function deleteEntry(int $id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_address_book
                                            where address_book_id = :address_book_id
                                            and customers_id = :customers_id
                                           ');
      $Qdelete->bindInt(':address_book_id', $id);
      $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qdelete->execute();

      return ($Qdelete->rowCount() === 1);
    }

    /**
     *  count customer address book
     * @param string $id
     * @param bool $check_session
     * @return int
     */
    public static function countCustomerAddressBookEntries($id = '', bool $check_session = true)
    {

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
        if (!$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID())) {
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
     * @param string $id
     * @param bool $check_session
     * @return int
     */
    public static function countCustomerOrders($id = '', $check_session = true)
    {

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
        if (!$CLICSHOPPING_Customer->isLoggedOn() || ($id != $CLICSHOPPING_Customer->getID())) {
          return 0;
        }
      }

      $Qorders = $CLICSHOPPING_Db->prepare('select count(*) as total
                                            from :table_orders o,
                                                 :table_orders_status s
                                            where o.customers_id = :customers_id
                                            and o.orders_status = s.orders_status_id
                                            and s.language_id = :language_id
                                            and s.public_flag = 1
                                          ');
      $Qorders->bindInt(':customers_id', (int)$id);
      $Qorders->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

      $Qorders->execute();

      if ($Qorders->fetch() !== false) {
        return $Qorders->valueInt('total');
      }

      return 0;
    }

    /**
     * Returns the address book entries for the current customer
     * @return mixed
     */
    public static function getListing()
    {
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
                                                       z.zone_name as zone_name,
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
     * @param int $id
     * @return mixed
     */
    public static function getEntry(int $id)
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_GET['newcustomer']) && $_GET['newcustomer'] == 1) {
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
                                             where customers_id = :customers_id
                                             ');
        $Qentry->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qentry->execute();

        return $Qentry->toArray();
      } else{
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
    }

    /**
     * Return the number of address book entries the current customer has
     * @param $total_entries
     * @return int
     */
    public static function numberOfEntries($total_entries) :int
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!isset($total_entries)) {
        $total_entries = 0;

        if ($CLICSHOPPING_Customer->isLoggedOn()) {
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

    /**
     * Set the address book entry as the primary address for the current customer
     * @param int $id
     * @return bool
     */
    public static function setPrimaryAddress(int $id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (is_numeric($id) && ($id > 0)) {
        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers
                                              set customers_default_address_id = :customers_default_address_id
                                              where customers_id = :customers_id
                                             ');
        $Qupdate->bindInt(':customers_default_address_id', $id);
        $Qupdate->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qupdate->execute();

        return ($Qupdate->rowCount() === 1);
      }

      return false;
    }

    /**
     * Check if it's new customer or not
     * @return bool
     */
    public static function checkNewCustomer() :bool
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (isset($_GET['newcustomer'])) {
        $new_customer = HTML::sanitize($_GET['newcustomer']);
      } else {
        $new_customer = null;
      }

      if ($new_customer == 1) {
        if (!empty($CLICSHOPPING_Customer->getDefaultAddressID())) {
          $entry = AddressBook::getEntry($CLICSHOPPING_Customer->getDefaultAddressID());
        }
      } else {
        if (isset($_GET['edit'])) {
          $entry = AddressBook::getEntry((int)$_GET['edit']);
        } else {
          $entry = false;
        }
      }

      $exists = false;

      if ($entry !== false) {
        $exists = true;
      }

      return $exists;
    }
  }