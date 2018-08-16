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

  namespace ClicShopping\Apps\Tools\SecurityCheck\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Configure extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_SecurityCheck = Registry::get('SecurityCheck');

      $this->page->setFile('configure.php');
      $this->page->data['action'] = 'Configure';

      $CLICSHOPPING_SecurityCheck->loadDefinitions('ClicShoppingAdmin/configure');

      $modules = $CLICSHOPPING_SecurityCheck->getConfigModules();

      $default_module = 'SC';

      foreach ($modules as $m) {
        if ($CLICSHOPPING_SecurityCheck->getConfigModuleInfo($m, 'is_installed') === true ) {
          $default_module = $m;
          break;
        }
      }

      $this->page->data['current_module'] = (isset($_GET['module']) && in_array($_GET['module'], $modules)) ? $_GET['module'] : $default_module;
    }
  }