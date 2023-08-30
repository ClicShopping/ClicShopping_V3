<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Stock extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Manufacturers = Registry::get('Manufacturers');

    $this->page->setFile('stock.php');

    $CLICSHOPPING_Manufacturers->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}