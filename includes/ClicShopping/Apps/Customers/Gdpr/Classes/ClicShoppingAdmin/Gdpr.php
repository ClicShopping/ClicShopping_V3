<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Gdpr\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
/**
 * Class Gdpr
 *
 * Provides methods for handling GDPR-related operations, including the deletion of customer data from multiple tables.
 */
class Gdpr
{
  /**
   * Deletes all customer-related data from multiple database tables based on the provided customer ID.
   *
   * @param int $customers_id The ID of the customer whose data will be deleted.
   * @return void
   */
  public static function deleteCustomersData(int $customers_id): void
  {
    $CLICSHOPPING_Gdpr = Registry::get('Gdpr');

    $Qreviews = $CLICSHOPPING_Gdpr->db->prepare('select reviews_id
                                                       from :table_reviews
                                                       where customers_id = :customers_id
                                                       ');
    $Qreviews->bindInt(':customers_id', $customers_id);
    $Qreviews->execute();

    while ($Qreviews->fetch()) {
      $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                         from :table_reviews_description
                                                         where reviews_id = :reviews_id
                                                        ');
      $Qdelete->bindInt(':reviews_id', $Qreviews->valueInt('reviews_id'));
      $Qdelete->execute();
    }

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                      from :table_reviews
                                                      where customers_id = :customers_id
                                                    ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                      from :table_address_book
                                                      where customers_id = :customers_id
                                                    ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                        from :table_customers
                                                        where customers_id = :customers_id
                                                      ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                          from :table_customers_info
                                                          where customers_info_id = :customers_id
                                                        ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                          from :table_customers_basket
                                                          where customers_id = :customers_id
                                                        ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                          from :table_customers_basket_attributes
                                                          where customers_id = :customers_id
                                                        ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();

    $Qdelete = $CLICSHOPPING_Gdpr->db->prepare('delete
                                                        from :table_whos_online
                                                        where customer_id = :customers_id
                                                      ');
    $Qdelete->bindInt(':customers_id', $customers_id);
    $Qdelete->execute();
  }
}