<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions\Administrators;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\Status;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Administrators = Registry::get('Administrators');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['Administrators'])) {
      Status::getAdministratorStatus($_GET['id'], $_GET['flag']);

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Administrators->getDef('success_status_updated'), 'success');
    }

    $CLICSHOPPING_Administrators->redirect('Administrators&page=' . $page . '&aID=' . (int)$_GET['id']);
  }
}