<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\WhosOnline\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Tools\WhosOnline\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_WhosOnline = Registry::get('WhosOnline');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_WhosOnline->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('WhosOnlineAdminConfig' . $current_module);
    $m->install();

//add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_WhosOnline->getDef('alert_module_install_success'), 'success', 'WhosOnline');

    $CLICSHOPPING_WhosOnline->redirect('Configure&module=' . $current_module);
  }
}
