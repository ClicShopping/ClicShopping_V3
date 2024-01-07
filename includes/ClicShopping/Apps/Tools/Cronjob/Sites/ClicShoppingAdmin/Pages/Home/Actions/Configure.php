<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

class Configure extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Cronjob = Registry::get('Cronjob');

    AdministratorAdmin::checkUserAccess();

    $this->page->setFile('configure.php');
    $this->page->data['action'] = 'Configure';

    $CLICSHOPPING_Cronjob->loadDefinitions('ClicShoppingAdmin/configure');

    $modules = $CLICSHOPPING_Cronjob->getConfigModules();

    $default_module = 'CJ';

    foreach ($modules as $m) {
      if ($CLICSHOPPING_Cronjob->getConfigModuleInfo($m, 'is_installed') === true) {
        $default_module = $m;
        break;
      }
    }

    $this->page->data['current_module'] = (isset($_GET['module']) && \in_array($_GET['module'], $modules)) ? $_GET['module'] : $default_module;
  }
}