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

  namespace ClicShopping\OM\Module\Hooks\Shop\Account;

  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AccountGdprCallDeleteReviews {

    public function execute() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if(isset($_POST['delete_all_reviews'])) {
        $Qcheck = $CLICSHOPPING_Db->prepare('select reviews_id
                                            from :table_reviews
                                            where customers_id = :customers_id
                                           ');
        $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qcheck->execute();

        while ($Qcheck->fetch()) {
          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                 from :table_reviews
                                                 where reviews_id = :reviews_id
                                               ');
          $Qdelete->bindInt(':reviews_id', $Qcheck->valueInt('reviews_id'));
          $Qdelete->execute();

          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                 from :table_reviews_description
                                                 where reviews_id = :reviews_id
                                               ');
          $Qdelete->bindInt(':reviews_id', $Qcheck->valueInt('reviews_id'));
          $Qdelete->execute();
        }
      }
    }
  }
