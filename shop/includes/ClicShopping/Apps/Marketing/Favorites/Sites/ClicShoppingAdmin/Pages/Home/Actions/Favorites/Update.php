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

  namespace ClicShopping\Apps\Marketing\Favorites\Sites\ClicShoppingAdmin\Pages\Home\Actions\Favorites;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Favorites = Registry::get('Favorites');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      $products_favorites_id = HTML::sanitize($_POST['products_favorites_id']);

      if (!empty($_POST['expdate'])) {
        $expdate = HTML::sanitize($_POST['expdate']);
      } else {
        $expdate = null;
      }

      if (!empty($_POST['schdate'])) {
        $schdate = HTML::sanitize($_POST['schdate']);
      } else {
        $schdate = null;
      }

      $Qupdate = $CLICSHOPPING_Favorites->db->prepare('update :table_products_favorites
                                                      set products_favorites_last_modified = now(),
                                                          expires_date = :expires_date,
                                                          scheduled_date = :scheduled_date
                                                      where products_favorites_id = :products_favorites_id
                                                    ');
      $Qupdate->bindValue(':expires_date', $expdate);
      $Qupdate->bindValue(':scheduled_date', $schdate);
      $Qupdate->bindInt(':products_favorites_id', $products_favorites_id);

      $Qupdate->execute();

      $CLICSHOPPING_Hooks->call('Favorites', 'Update');

      $CLICSHOPPING_Favorites->redirect('Favorites', 'page=' . $page . '&sID=' . $products_favorites_id);
    }
  }