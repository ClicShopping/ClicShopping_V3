<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class StatsProductsSafetyStock extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Products = Registry::get('Products');

    $this->page->setFile('stats_products_safety_stock.php');

    $CLICSHOPPING_Products->loadDefinitions('Sites/ClicShoppingAdmin/stats_products_safety_stock');
  }
}