<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Gdpr\Sites\ClicShoppingAdmin\Pages\Home\Actions\Gdpr;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Gdpr\Classes\ClicShoppingAdmin\Gdpr as GdprAdmin;

class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Gdpr = Registry::get('Gdpr');
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
    
    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {
        GdprAdmin::deleteCustomersData($id);
      }
    }

    $CLICSHOPPING_Gdpr->redirect('Customers', 'page=' . $page);
  }
}