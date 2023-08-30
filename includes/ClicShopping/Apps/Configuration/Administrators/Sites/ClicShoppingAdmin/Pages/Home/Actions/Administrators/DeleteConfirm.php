<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions\Administrators;

use ClicShopping\OM\Registry;

class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Administrators');
  }

  public function execute()
  {

    $id = (int)$_GET['aID'];

    $Qcheck = $this->app->db->get('administrators', ['id', 'user_name'], ['id' => $id]);

    if ($_SESSION['admin']['id'] === $Qcheck->valueInt('id')) {
      unset($_SESSION['admin']);
    }

    $this->app->db->delete('administrators', ['id' => $id]);

    $this->app->redirect('Administrators');
  }
}