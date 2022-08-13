<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

  use ClicShopping\OM\Registry;

  class Newsletters
  {
    /**
     * @return mixed
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