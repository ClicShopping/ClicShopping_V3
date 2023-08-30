<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Zones\Sites\ClicShoppingAdmin\Pages\Home\Actions\Zones;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Zones\Classes\ClicShoppingAdmin\Status;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Zones');
  }

  public function execute()
  {
    $search = '';

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
    Status::getZonesStatus($_GET['id'], $_GET['flag']);

    if (isset($_GET['search'])) {
      $search = '&search=' . HTML::sanitize($_GET['search']);
    }

    $this->app->redirect('Zones&page=' . $page . '&cID=' . $_GET['id'] . $search);
  }
}