<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Customers\Groups\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Groups = Registry::get('Groups');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_Groups->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('GroupsAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Groups->getDef('alert_module_install_success'), 'success', 'groups');

    $CLICSHOPPING_Groups->redirect('Configure&module=' . $current_module);
  }
}
