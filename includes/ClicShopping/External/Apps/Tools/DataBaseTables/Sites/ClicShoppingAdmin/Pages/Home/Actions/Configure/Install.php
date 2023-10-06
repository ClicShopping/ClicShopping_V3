<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\DataBaseTables\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Tools\DataBaseTables\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_DataBaseTables = Registry::get('DataBaseTables');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_DataBaseTables->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('DataBaseTablesAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_DataBaseTables->getDef('alert_module_install_success'), 'success', 'DataBaseTables');

    $CLICSHOPPING_DataBaseTables->redirect('Configure&module=' . $current_module);
  }
}
