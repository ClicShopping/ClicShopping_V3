<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Report\StatsProductsNotification\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class StatsProductsNotification extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_StatsProductsNotification = Registry::get('StatsProductsNotification');

    $this->page->setFile('stats_products_notification.php');

    $CLICSHOPPING_StatsProductsNotification->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}