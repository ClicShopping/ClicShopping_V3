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

  namespace ClicShopping\Apps\Catalog\Preview\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Preview = Registry::get('Preview');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Preview->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('PreviewAdminConfig' . $current_module);
      $m->install();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Preview->getDef('alert_module_install_success'), 'success', 'Preview');

      $CLICSHOPPING_Preview->redirect('Configure&module=' . $current_module);
    }
  }
