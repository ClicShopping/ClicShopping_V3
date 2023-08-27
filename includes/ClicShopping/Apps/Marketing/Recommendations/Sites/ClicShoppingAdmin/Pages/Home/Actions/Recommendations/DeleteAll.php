<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Recommendations\Sites\ClicShoppingAdmin\Pages\Home\Actions\Recommendations;

  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Recommendations = Registry::get('Recommendations');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      if (isset($_POST['selected'], $_GET['DeleteAll'], $_GET['Recommendations'])) {
        foreach ($_POST['selected'] as $id) {
          $CLICSHOPPING_Recommendations->db->delete('customers_basket', ['products_id' => (int)$id]);

          $CLICSHOPPING_Hooks->call('Recommendations', 'RemoveRecommendations');
        }
      }

      $CLICSHOPPING_Recommendations->redirect('ProductsRecommendation', 'page=' . $page);
    }
  }