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


  namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\Reviews;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      $reviews_id = HTML::sanitize($_GET['rID']);
      $reviews_rating = HTML::sanitize($_POST['reviews_rating']);
      $reviews_text = HTML::sanitize($_POST['reviews_text']);
      $reviews_status = HTML::sanitize($_POST['status']);

      $CLICSHOPPING_Reviews->db->save('reviews', [
                                          'reviews_rating' => $reviews_rating,
                                          'status' => (int)$reviews_status,
                                          'last_modified' => 'now()'
                                          ], [
                                          'reviews_id' => (int)$reviews_id
                                          ]
                              );

      $CLICSHOPPING_Reviews->db->save('reviews_description', ['reviews_text' => $reviews_text],
                                                      ['reviews_id' => (int)$reviews_id]
                              );

      $CLICSHOPPING_Reviews->redirect('Reviews&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'rID=' . $_GET['id']);
    }
  }