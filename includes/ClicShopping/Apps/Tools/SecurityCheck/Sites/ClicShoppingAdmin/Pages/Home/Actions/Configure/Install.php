<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecurityCheck\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Tools\SecurityCheck\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_SecurityCheck = Registry::get('SecurityCheck');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_SecurityCheck->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('SecurityCheckAdminConfig' . $current_module);
    $m->install();

//add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_SecurityCheck->getDef('alert_module_install_success'), 'success');

    $CLICSHOPPING_SecurityCheck->redirect('Configure&module=' . $current_module);
  }
}
