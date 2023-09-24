<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Report\StatsProductsNotification\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;

class Process extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_StatsProductsNotification = Registry::get('StatsProductsNotification');

    $current_module = $this->page->data['current_module'];

    $m = Registry::get('StatsProductsNotificationAdminConfig' . $current_module);

    foreach ($m->getParameters() as $key) {
      $p = mb_strtolower($key);

      if (isset($_POST[$p])) {
        $CLICSHOPPING_StatsProductsNotification->saveCfgParam($key, $_POST[$p]);
      }
    }

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_StatsProductsNotification->getDef('alert_cfg_saved_success'), 'success');

    $CLICSHOPPING_StatsProductsNotification->redirect('Configure&module=' . $current_module);
  }
}
