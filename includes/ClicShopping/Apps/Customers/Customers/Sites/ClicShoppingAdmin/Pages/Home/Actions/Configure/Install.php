<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Customers\Customers\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Customers = Registry::get('Customers');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_Customers->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('CustomersAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('alert_module_install_success'), 'success');

    $CLICSHOPPING_Customers->redirect('Configure&module=' . $current_module);
  }
}
