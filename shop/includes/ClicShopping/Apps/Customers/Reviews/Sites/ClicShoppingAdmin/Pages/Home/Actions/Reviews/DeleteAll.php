<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\Reviews;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      if (isset($_GET['id'])) $rId = HTML::sanitize($_GET['id']);

      if (!empty($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {
          $reviews_id = HTML::sanitize($id);

          $CLICSHOPPING_Reviews->db->delete('reviews', ['reviews_id' => (int)$reviews_id]);

          $CLICSHOPPING_Reviews->db->delete('reviews_description', ['reviews_id' => (int)$reviews_id]);
        }
      }

      $CLICSHOPPING_Reviews->redirect('Reviews&page=' . $page . '&rID=' . $rId);
    }
  }