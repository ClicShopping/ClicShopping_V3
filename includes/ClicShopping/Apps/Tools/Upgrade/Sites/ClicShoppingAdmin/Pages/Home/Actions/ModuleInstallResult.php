<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ModuleInstallResult extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Upgrade = Registry::get('Upgrade');

      $this->page->setFile('module_install_result.php');
      $this->page->data['action'] = 'ModuleInstallResult';

      $CLICSHOPPING_Upgrade->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }