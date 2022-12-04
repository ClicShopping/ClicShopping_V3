<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Favorites\Sites\ClicShoppingAdmin\Pages\Home\Actions\Favorites;

  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Favorites = Registry::get('Favorites');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {
          $Qdelete = $CLICSHOPPING_Favorites->db->prepare('delete
                                                            from :table_products_favorites
                                                            where products_favorites_id = :products_favorites_id
                                                          ');
          $Qdelete->bindInt(':products_favorites_id', (int)$id);
          $Qdelete->execute();

          $CLICSHOPPING_Hooks->call('Favorites', 'RemoveFavorites');
        }
      }

      $CLICSHOPPING_Favorites->redirect('Favorites', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'sID=' . (int)$_GET['id']);
    }
  }