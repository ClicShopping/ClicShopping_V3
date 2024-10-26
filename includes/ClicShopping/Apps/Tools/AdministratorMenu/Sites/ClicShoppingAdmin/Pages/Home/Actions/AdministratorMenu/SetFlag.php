<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions\AdministratorMenu;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\Status;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('AdministratorMenu');
  }

  public function execute()
  {
    if (($_GET['flag'] == 0) || ($_GET['flag'] == 1)) {
      if (isset($_GET['cPath'])) {
        $cPath = HTML::sanitize($_GET['cPath']);
      } else {
        $cPath = 0;
      }

      if (isset($_GET['cID'])) {
        Status::getAministratorMenuStatus($_GET['cID'], (int)$_GET['flag']);
      }
    }

    if (isset($_GET['cID'])) {
      $this->app->redirect('AdministratorMenu&cPath=' . $cPath . '&cID=' . $_GET['cID']);
    } else {
      $this->app->redirect('AdministratorMenu&cPath=' . $cPath);
    }
  }
}