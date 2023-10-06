<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Catalog\ProductsAttributes\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_ProductsAttributes->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('ProductsAttributesAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductsAttributes->getDef('alert_module_install_success'), 'success', 'ProductsAttributes');

    $CLICSHOPPING_ProductsAttributes->redirect('Configure&module=' . $current_module);
  }
}
