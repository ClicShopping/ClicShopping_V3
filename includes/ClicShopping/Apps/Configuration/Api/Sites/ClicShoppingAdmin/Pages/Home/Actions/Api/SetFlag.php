<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Api\Sites\ClicShoppingAdmin\Pages\Home\Actions\Api;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Api\Classes\ClicShoppingAdmin\Status;


class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Api');
  }

  public function execute()
  {
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    Status::getApiStatus($_GET['cID'], $_GET['flag']);

    $this->app->redirect('Api&' . $page . '&cID=' . $_GET['cID']);
  }
}