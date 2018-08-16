<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {
    public function execute() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

      $current_module = $this->page->data['current_module'];

      $m = Registry::get('ProductsAttributesAdminConfig' . $current_module);

      foreach ($m->getParameters() as $key) {
        $p = strtolower($key);

        if (isset($_POST[$p])) {
          $CLICSHOPPING_ProductsAttributes->saveCfgParam($key, $_POST[$p]);
        }
      }

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductsAttributes->getDef('alert_cfg_saved_success'), 'success', 'ProductsAttributes');

      $CLICSHOPPING_ProductsAttributes->redirect('Configure&module=' . $current_module);
    }
  }
