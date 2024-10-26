<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecurityCheck\Sites\ClicShoppingAdmin\Pages\Home\Actions\IpRestriction;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\SecurityCheck\Classes\IpRestriction;

class SetFlagShop extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('SecurityCheck');
  }

  public function execute()
  {
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['cID'], $_GET['flag'])) {
      IpRestriction::getIpRestrictionShopStatus($_GET['cID'], $_GET['flag']);

      $this->app->redirect('IpRestriction&page=' . $page . '&cID=' . (int)$_GET['cID']);
    } else {
      $this->app->redirect('IpRestriction&page=' . $page);
    }
  }
}