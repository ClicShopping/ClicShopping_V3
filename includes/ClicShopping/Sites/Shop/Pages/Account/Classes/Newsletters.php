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

class Newsletters
{
  /**
   * Retrieves the newsletter subscription status of the current customer.
   *
   * This method queries the database to check if the customer is subscribed
   * to the newsletter. It uses the customer's ID to look up the relevant
   * record in the customers table and returns the integer value representing
   * the subscription status.
   *
   * @return int Returns the newsletter subscription status as an integer.
   */
  public static function getCustomerNewsletter()
  {
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qnewsletter = $CLICSHOPPING_Db->prepare('select customers_newsletter
                                             from :table_customers
                                             where customers_id = :customers_id
                                           ');
    $Qnewsletter->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
    $Qnewsletter->execute();

    return $Qnewsletter->valueInt('customers_newsletter');
  }
}