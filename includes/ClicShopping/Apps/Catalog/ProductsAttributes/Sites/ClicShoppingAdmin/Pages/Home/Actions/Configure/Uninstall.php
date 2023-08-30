<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;

class Uninstall extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

    $current_module = $this->page->data['current_module'];
    $m = Registry::get('ProductsAttributesAdminConfig' . $current_module);
    $m->uninstall();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductsAttributes->getDef('alert_module_uninstall_success'), 'success', 'ProductsAttributes');

    $CLICSHOPPING_ProductsAttributes->redirect('Configure&module=' . $current_module);
  }
}