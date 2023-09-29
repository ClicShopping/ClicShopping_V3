<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Archive\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Archive\Sql\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Archive = Registry::get('Archive');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_Archive->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('ArchiveAdminConfig' . $current_module);
    $m->install();

//add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Archive->getDef('alert_module_install_success'), 'success');

    $CLICSHOPPING_Archive->redirect('Configure&module=' . $current_module);
  }
}
