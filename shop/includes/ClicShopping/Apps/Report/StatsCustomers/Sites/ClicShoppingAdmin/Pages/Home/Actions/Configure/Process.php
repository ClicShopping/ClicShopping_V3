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

  namespace ClicShopping\Apps\Report\StatsCustomers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {
    public function execute() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_StatsCustomers = Registry::get('StatsCustomers');

      $current_module = $this->page->data['current_module'];

      $m = Registry::get('StatsCustomersAdminConfig' . $current_module);

      foreach ($m->getParameters() as $key) {
        $p = strtolower($key);

        if (isset($_POST[$p])) {
          $CLICSHOPPING_StatsCustomers->saveCfgParam($key, $_POST[$p]);
        }
      }

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_StatsCustomers->getDef('alert_cfg_saved_success'), 'success', 'StatsCustomers');

      $CLICSHOPPING_StatsCustomers->redirect('Configure&module=' . $current_module);
    }
  }
