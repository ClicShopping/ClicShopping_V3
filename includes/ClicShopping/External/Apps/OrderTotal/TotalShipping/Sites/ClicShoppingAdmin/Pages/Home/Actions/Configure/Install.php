<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\TotalShipping\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\OrderTotal\TotalShipping\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_TotalShipping = Registry::get('TotalShipping');

    $CLICSHOPPING_TotalShipping->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $current_module = $this->page->data['current_module'];

    $m = Registry::get('TotalShippingAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TotalShipping->getDef('alert_module_install_success'), 'success');

    $CLICSHOPPING_TotalShipping->redirect('Configure&module=' . $current_module);
  }
}