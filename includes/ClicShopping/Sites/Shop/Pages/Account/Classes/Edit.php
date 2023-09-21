<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

use ClicShopping\OM\Registry;

class Edit
{
  private mixed $db;
  private $customer;

  public function __construct()
  {
    $this->db = Registry::get('Db');
    $this->customer = Registry::get('Customer');
  }

  /**
   * @return array
   */
  public static function getAccountEdit(): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    $Qaccount = $CLICSHOPPING_Db->prepare('select customers_gender,
                                                    customers_firstname,
                                                    customers_lastname,
                                                    customers_dob,
                                                    customers_email_address,
                                                    customers_telephone,
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

  /**
   * @param string $email_address
   * @return int
   */
  public static function getCountEmail(string $email_address): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    $QcheckEmail = $CLICSHOPPING_Db->prepare('select count(*) as total
                                         from :table_customers
                                         where customers_email_address = :customers_email_address
                                         and customers_id != :customers_id
                                        ');
    $QcheckEmail->bindValue(':customers_email_address', $email_address);
    $QcheckEmail->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
    $QcheckEmail->execute();

    $check_email = $QcheckEmail->valueInt('total');

    return $check_email;
  }

  /**
   * @param string $email_address
   * @return bool
   */
  public static function getCustomerAddressEmail(string $email_address): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    $Qcheck = $CLICSHOPPING_Db->prepare('select customers_id
                                            from :table_customers
                                            where customers_email_address = :customers_email_address
                                            and customers_id != :customers_id
                                            limit 1
                                            ');
    $Qcheck->bindValue(':customers_email_address', $email_address);
    $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
    $Qcheck->execute();

    $check = $Qcheck->fetch();

    return $check;
  }

  /**
   * @param int $country_id
   * @return int
   */
  public static function getCheckCountryIsoCode2(int $country_id): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->prepare('select countries_id
                                          from :table_countries
                                          where countries_iso_code_2 = :countries_iso_code_2
                                          limit 1
                                          ');
    $Qcheck->bindValue(':countries_iso_code_2', $country_id);
    $Qcheck->execute();

    $country_id = $Qcheck->valueInt('countries_id');

    return $country_id;
  }
}