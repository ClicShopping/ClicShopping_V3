<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditLogError\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;

class Process extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_EditLogError = Registry::get('EditLogError');

    $current_module = $this->page->data['current_module'];

    $m = Registry::get('EditLogErrorAdminConfig' . $current_module);

    foreach ($m->getParameters() as $key) {
      $p = mb_strtolower($key);

      if (isset($_POST[$p])) {
        $CLICSHOPPING_EditLogError->saveCfgParam($key, $_POST[$p]);
      }
    }

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_EditLogError->getDef('alert_cfg_saved_success'), 'success', 'EditLogError');

    $CLICSHOPPING_EditLogError->redirect('Configure&module=' . $current_module);
  }
}
