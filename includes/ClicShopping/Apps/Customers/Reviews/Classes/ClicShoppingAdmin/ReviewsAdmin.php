<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Reviews\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class ReviewsAdmin
  {
    /**
     * @return int
     */
      public static function countCustomersTags(): int
      {
        $CLICSHOPPING_Db = Registry::get('Db');

        $Qreviews = $CLICSHOPPING_Db->prepare('select customers_tag
                                               from :table_reviews
                                              ');
        $Qreviews->execute();

        $reviews_array = $Qreviews->fetchAll();
        $total = 0;

        foreach($reviews_array as $value) {
          $customer_tag_array = explode(',', $value['customers_tag']);
          $total += count($customer_tag_array);
        }

        return $total;
      }

    /**
     * Status reviews  - Sets the status of a reviews products
     *
     * @param int $id , reviews_id
     * @param int|null $status
     * @return string status on or off
     */
    Public static function getReviewsStatus(int $id, ?int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('reviews', [
          'status' => 1,
          'last_modified' => 'now()'
        ],
          ['reviews_id' => (int)$id]
        );

      } elseif ($status == 0) {
        return $CLICSHOPPING_Db->save('reviews', ['status' => 0,
          'last_modified' => 'now()'
        ],
          ['reviews_id' => (int)$id]
        );

      } else {
        return -1;
      }
    }
  }