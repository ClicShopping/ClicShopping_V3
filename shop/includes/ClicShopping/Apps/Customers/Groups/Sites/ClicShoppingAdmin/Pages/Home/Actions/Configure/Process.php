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

  namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {
    public function execute() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Groups = Registry::get('Groups');

      $current_module = $this->page->data['current_module'];

      $m = Registry::get('GroupsAdminConfig' . $current_module);

      foreach ($m->getParameters() as $key) {
        $p = strtolower($key);

        if (isset($_POST[$p])) {
          $CLICSHOPPING_Groups->saveCfgParam($key, $_POST[$p]);
        }
      }

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Groups->getDef('alert_cfg_saved_success'), 'success', 'Groups');

      $CLICSHOPPING_Groups->redirect('Configure&module=' . $current_module);
    }
  }
