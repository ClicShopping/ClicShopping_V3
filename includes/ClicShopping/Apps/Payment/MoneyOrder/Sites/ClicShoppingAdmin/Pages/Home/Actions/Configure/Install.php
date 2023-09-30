<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\MoneyOrder\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Payment\MoneyOrder\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_MoneyOrder = Registry::get('MoneyOrder');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_MoneyOrder->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('MoneyOrderAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_MoneyOrder->getDef('alert_module_install_success'), 'success', 'MoneyOrder');

    $CLICSHOPPING_MoneyOrder->redirect('Configure&module=' . $current_module);
  }
}
