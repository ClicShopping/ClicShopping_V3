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

  namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

  use ClicShopping\OM\Registry;

  class Edit {
    protected $db;
    protected $customer;
    public function __construct() {
      $this->db = Registry::get('Db');
      $this->customer = Registry::get('Customer');
    }

    public static function getAccountEdit() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qaccount = $CLICSHOPPING_Db->prepare('select customers_gender,
                                              customers_firstname,
                                              customers_lastname,
                                              customers_dob,
                                              customers_email_address,
                                              customers_telephone,
                                              customers_fax,
                                              customers_cellular_phone,
                                              customers_company,
                                              customers_siret,
                                              customers_ape,
                                              customers_tva_intracom,
                                              customers_tva_intracom_code_iso
                                       from :table_customers
                                       where customers_id = :customers_id
                                      ');
      $Qaccount->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qaccount->execute();

      $account = $Qaccount->fetch();

      return $account;
    }

    public static function getCountEmail($email_address) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $QcheckEmail =$CLICSHOPPING_Db->prepare('select count(*) as total
                                         from :table_customers
                                         where customers_email_address = :customers_email_address
                                         and customers_id != :customers_id
                                        ');
      $QcheckEmail->bindValue(':customers_email_address', $email_address );
      $QcheckEmail->bindInt(':customers_id', $CLICSHOPPING_Customer->getID() );
      $QcheckEmail->execute();

      $check_email = $QcheckEmail->valueInt('total');

      return $check_email;
    }

    public static function getCustomerAddressEmail($email_address) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qcheck = $CLICSHOPPING_Db->prepare('select customers_id
                                      from :table_customers
                                      where customers_email_address = :customers_email_address
                                      and customers_id != :customers_id
                                      limit 1');
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qcheck->execute();

      $check = $Qcheck->fetch();

      return $check;
    }

    public static function getCheckCountryIsoCode2($country) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->prepare('select countries_id
                                    from :table_countries
                                    where countries_iso_code_2 = :countries_iso_code_2
                                    limit 1
                                    ');
      $Qcheck->bindValue(':countries_iso_code_2', $country );
      $Qcheck->execute();

      $country = $Qcheck->valueInt('countries_id');

      return $country;
    }
  }