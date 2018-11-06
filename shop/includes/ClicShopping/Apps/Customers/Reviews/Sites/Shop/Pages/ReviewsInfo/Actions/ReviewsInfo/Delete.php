<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\ReviewsInfo\Actions\ReviewsInfo;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      if (!isset($_GET['products_id']) && !is_numeric($CLICSHOPPING_ProductsCommon->getId())) {
        CLICSHOPPING::redirect();
      }

      if (isset($_POST['action']) && ($_POST['action'] == 'process')  && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $review_id = HTML::sanitize($_GET['reviews_id']);
        $products_id = HTML::sanitize($_GET['products_id']);

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

        CLICSHOPPING::redirect(null, 'Products&Description&products_id=' . $products_id);
      }
    }
  }