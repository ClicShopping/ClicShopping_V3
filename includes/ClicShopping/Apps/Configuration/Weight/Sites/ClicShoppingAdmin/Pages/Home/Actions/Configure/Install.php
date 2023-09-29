<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Weight\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\Weight\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Weight = Registry::get('Weight');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_Weight->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('WeightAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Weight->getDef('alert_module_install_success'), 'success', 'Weight');

    $CLICSHOPPING_Weight->redirect('Configure&module=' . $current_module);
  }
}
