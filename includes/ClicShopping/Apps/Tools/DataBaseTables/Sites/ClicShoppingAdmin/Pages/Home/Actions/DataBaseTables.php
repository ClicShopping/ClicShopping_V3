<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\DataBaseTables\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class DataBaseTables extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_DataBaseTables = Registry::get('DataBaseTables');

    $this->page->setFile('data_base_tables.php');

    $CLICSHOPPING_DataBaseTables->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}