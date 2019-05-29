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

  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions\Featured;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Featured = Registry::get('Featured');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      $products_featured_id = HTML::sanitize($_POST['products_featured_id']);

      if (!empty($_POST['expdate'])) {
        $expdate = HTML::sanitize($_POST['expdate']);
      } else {
        $expdate = null;
      }

      if (!empty($_POST['expdate'])) {
        $schdate = HTML::sanitize($_POST['schdate']);
      } else {
        $schdate = null;
      }

      $Qupdate = $CLICSHOPPING_Featured->db->prepare('update :table_products_featured
                                                      set products_featured_last_modified = now(),
                                                          expires_date = :expires_date,
                                                          scheduled_date = :scheduled_date
                                                      where products_featured_id = :products_featured_id
                                                    ');
      $Qupdate->bindValue(':expires_date', $expdate);
      $Qupdate->bindValue(':scheduled_date', $schdate);
      $Qupdate->bindInt(':products_featured_id', $products_featured_id);

      $Qupdate->execute();

      $CLICSHOPPING_Hooks->call('Featured', 'Update');

      $CLICSHOPPING_Featured->redirect('Featured', 'page=' . $page . '&sID=' . $products_featured_id);
    }
  }