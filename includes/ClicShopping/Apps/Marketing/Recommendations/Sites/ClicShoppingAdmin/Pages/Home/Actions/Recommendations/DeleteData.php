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

class DeleteData extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Recommendations = Registry::get('Recommendations');
    $CLICSHOPPING_Db = Registry::get('Db');

    if (isset($_GET['DeleteData'])) {
      $CLICSHOPPING_Db->delete('products_recommendations');
      $CLICSHOPPING_Db->delete('products_recommendations_to_categories');
    }

    $CLICSHOPPING_Recommendations->redirect('Recommendations');
  }
}