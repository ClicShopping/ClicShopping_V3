<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Customers\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class CustomersAdmin
{
  private mixed $db;
  public function __construct()
  {
    $this->db = Registry::get('Db');
  }

  /**
   * @param $id , customer id
   * @return bool
   */
  public function getData(int $id): ?array
  {
    $Qcustomer = $this->db->prepare('select customers_gender,
                                               customers_firstname,
                                               customers_lastname,
                                               customers_email_address,
                                               customers_telephone,
                                               customers_default_address_id
                                        from :table_customers
                                        where customers_id = :customers_id
                                      ');
    $Qcustomer->bindInt(':customers_id', $id);
    $Qcustomer->execute();

    return $Qcustomer->fetchAll();
  }

  /**
   * @param int $id
   * @return string
   */
  protected function getCustomerEmail(int $id): string
  {
    $result = $this->getData($id);

    return $result['customers_email_address'];
  }

  /**
   * @param string $email
   * @return int
   */
  public function getCustomerIdByEmail(string $email): int
  {
    $Qcustomer = $this->db->prepare('select customers_id
                                        from :table_customers
                                        where customer_email = :customer_email
                                      ');
    $Qcustomer->bindInt(':customer_email', $email);
    $Qcustomer->execute();

    return $Qcustomer->valueInt('customers_id');
  }
}