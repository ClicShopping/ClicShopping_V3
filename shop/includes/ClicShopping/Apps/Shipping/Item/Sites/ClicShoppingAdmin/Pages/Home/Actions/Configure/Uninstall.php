<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Shipping\Item\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Uninstall extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Item = Registry::get('Item');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('ItemAdminConfig' . $current_module);
      $m->uninstall();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Item->getDef('alert_module_uninstall_success'), 'success', 'Item');

      $CLICSHOPPING_Item->redirect('Configure&module=' . $current_module);
    }
  }