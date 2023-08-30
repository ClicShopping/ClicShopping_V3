<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ModulesHooks\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;

class Process extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_ModulesHooks = Registry::get('ModulesHooks');

    $current_module = $this->page->data['current_module'];

    $m = Registry::get('ModulesHooksAdminConfig' . $current_module);

    foreach ($m->getParameters() as $key) {
      $p = mb_strtolower($key);

      if (isset($_POST[$p])) {
        $CLICSHOPPING_ModulesHooks->saveCfgParam($key, $_POST[$p]);
      }
    }

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ModulesHooks->getDef('alert_cfg_saved_success'), 'success', 'ModulesHooks');

    $CLICSHOPPING_ModulesHooks->redirect('Configure&module=' . $current_module);
  }
}
