<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditDesign\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;

class Uninstall extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_EditDesign = Registry::get('EditDesign');

    $current_module = $this->page->data['current_module'];
    $m = Registry::get('EditDesignAdminConfig' . $current_module);
    $m->uninstall();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_EditDesign->getDef('alert_module_uninstall_success'), 'success', 'EditDesign');

    $CLICSHOPPING_EditDesign->redirect('Configure&module=' . $current_module);
  }
}