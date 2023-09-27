<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\Reviews\Actions\Reviews;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{
  /**
   * @param $products_id
   * @return void
   */
  private static function deleteReviews($products_id): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
    $CLICSHOPPING_Reviews = Registry::get('Reviews');

    $review_id = HTML::sanitize($_GET['reviews_id']);

    $Ocheck = $CLICSHOPPING_Db->prepare('select reviews_id
                                          from :table_reviews
                                          where reviews_id = :reviews_id
                                          and products_id = :products_id
                                          and customers_id = :customer_id
                                          ');
    $Ocheck->bindInt(':reviews_id', $review_id);
    $Ocheck->bindInt(':products_id', $products_id);
    $Ocheck->bindInt(':customer_id', $CLICSHOPPING_Customer->getID());
    $Ocheck->execute();

    if ($Ocheck->rowCount() > 0) {
      $CLICSHOPPING_Reviews->deleteReviews($review_id);
    }
  }
  public function execute()
  {
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
    $products_id = $CLICSHOPPING_ProductsCommon->getId();

    if (!$CLICSHOPPING_ProductsCommon->getId() !== null && !is_numeric($CLICSHOPPING_ProductsCommon->getId())) {
      CLICSHOPPING::redirect();
    }

    if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] === $_SESSION['sessiontoken'])) {
      self::deleteReviews($products_id);

      CLICSHOPPING::redirect(null, 'Products&Reviews&products_id=' . $products_id);
    }
  }
}